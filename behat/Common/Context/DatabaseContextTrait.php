<?php

namespace Common\Context;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use AppBundle\Utils\Purger\TruncateORMPurger;
use Behat\Gherkin\Node\TableNode;
use Common\Util\DatabaseHelper;
use Common\Util\Metadata\EntityMetadata;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

/**
 * Class DatabaseContextTrait
 * Contains steps definitions for working with database.
 *
 * @package Common\Context
 */
trait DatabaseContextTrait
{

    /**
     * @var DatabaseHelper
     */
    private $dataBaseHelper;

    /**
     * Clear database and load fixtures before scenario.
     *
     * @BeforeScenario @db-fixtures
     *
     * @return void
     */
    public function setupDbFixtures()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.entity_manager');

        echo 'Purge database ... ';
        $purger = new TruncateORMPurger(new ORMPurger($em));
        $purger->purge();
        echo 'done'. PHP_EOL;

        $loader = new ContainerAwareLoader($this->container);
        $loader->loadFromDirectory($this->fixturesDir);

        echo 'Load database fixtures:'. PHP_EOL;
        $executor = new ORMExecutor($em);
        $executor
            ->setLogger(function ($message) {
                echo "  > {$message}". PHP_EOL;
            });
        $executor->execute($loader->getFixtures(), true);
    }

    /**
     * @Then /^(?:|[Dd]atabase )[Hh]as entity (?P<name>.+)$/
     *
     * @param string    $name  Entity short name like AppBundle:Entity or FQCN.
     * @param TableNode $table Search parameters in table format.
     *
     * @return void
     */
    public function hasEntity($name, TableNode $table)
    {
        $params = [];

        $tableData = $table->getTable();
        foreach ($tableData as $row) {
            $params[current($row)] = next($row);
        }

        $entity = $this->dataBaseHelper->getEntity($name, $params);

        self::assertNotNull(
            $entity,
            "Can't find entity {$name} with parameters " . PHP_EOL
            . json_encode($params, JSON_PRETTY_PRINT)
        );
    }

    /**
     * @Then /^(?:|[Dd]atabase )[Hh]as (?P<count>\d+) entity (?P<name>.+)$/
     * @Then /?[Dd]on't has entity (?P<name>.+)$?
     *
     * @param string    $name  Entity short name like AppBundle:Entity or FQCN.
     * @param integer   $count Expected entities count.
     * @param TableNode $table Search parameters in table format.
     *
     * @return void
     */
    public function hasEntities($name, $count, TableNode $table)
    {
        $params = [];

        $tableData = $table->getTable();
        foreach ($tableData as $row) {
            $params[current($row)] = next($row);
        }

        $entities = $this->dataBaseHelper->getEntities($name, $params);

        self::assertCount(
            (int) $count,
            $entities,
            "Can't find {$count} entity {$name} with parameters " . PHP_EOL
            . json_encode($params, JSON_PRETTY_PRINT) .PHP_EOL .
            'Actually found: '. count($entities)
        );
    }

    /**
     * @Then /^[Ii] want to delete entity (?P<name>.+)$/
     *
     * @param string    $name  Entity short name like AppBundle:Entity or FQCN.
     * @param TableNode $table Search parameters in table format.
     *
     * @return void
     */
    public function deleteEntity($name, TableNode $table)
    {
        $params = [];

        $tableData = $table->getTable();
        foreach ($tableData as $row) {
            $params[current($row)] = next($row);
        }

        $this->dataBaseHelper->deleteEntity($name, $params);
    }

    /**
     * @Then /^(?:|[Dd]atabase )[Dd]on't has entity (?P<name>.+)$/
     * @Then /?[Hh]as entity (?P<name>.+)$?
     *
     * @param string    $name  Entity short name like AppBundle:Entity or FQCN.
     * @param TableNode $table Search parameters ikn table format.
     *
     * @return void
     */
    public function entityNotExists($name, TableNode $table)
    {
        $params = [];

        $tableData = $table->getTable();
        foreach ($tableData as $row) {
            $params[current($row)] = next($row);
        }

        $entities = $this->dataBaseHelper->getEntities($name, $params);

        self::assertCount(
            0,
            $entities,
            "Entity {$name} with parameters " . PHP_EOL
            . json_encode($params, JSON_PRETTY_PRINT) . PHP_EOL .'exists!'
        );
    }

    /**
     * @param EntityManagerInterface $em       A EntityManagerInterface instance.
     * @param array                  $fqcnList List of available fqcn's.
     *
     * @return array|mixed
     */
    protected function processEntityMetadata(EntityManagerInterface $em, array $fqcnList)
    {
        $entities = [];

        foreach ($fqcnList as $fqcn) {
            $reflection = new \ReflectionClass($fqcn);
            if ($reflection->implementsInterface(NormalizableEntityInterface::class)) {
                $name = \app\c\entityFqcnToShort($fqcn);

                if ($reflection->isAbstract()) {
                    $entities[$name] = new EntityMetadata($this->processAbstractMetadata($em, $reflection));
                } else {
                    /** @var NormalizableEntityInterface $entity */
                    $entity = $reflection->newInstanceWithoutConstructor();
                    $entities[$name] = new EntityMetadata($entity->getMetadata());
                }
            }
        }

        return $entities;
    }

    /**
     * @param EntityManagerInterface $em         A EntityManagerInterface
     *                                           instance.
     * @param \ReflectionClass       $reflection A ReflectionClass instance.
     *
     * @return Metadata
     */
    protected function processAbstractMetadata(
        EntityManagerInterface $em,
        \ReflectionClass $reflection
    ) {
        /** @var ClassMetadataInfo $doctrineMetadata */
        $doctrineMetadata = $em->getClassMetadata($reflection->getName());
        $map = $doctrineMetadata->discriminatorMap;

        if (! is_array($map) || (count($map) === 0)) {
            // Parsed abstract class don't has discriminator column.
            $message = 'Abstract class without discriminator column not allowed';
            throw new \InvalidArgumentException($message);
        }

        $metadata = new Metadata($reflection->getName());

        $metadataList = array_map(function ($fqcn) use ($em) {
            $reflection = new \ReflectionClass($fqcn);
            if ($reflection->isAbstract()) {
                /** @var ClassMetadataInfo $doctrineMetadata */
                $doctrineMetadata = $em->getClassMetadata($fqcn);
                $map = $doctrineMetadata->discriminatorMap;
                return $this->processAbstractMetadata($em, $map);
            }

            /** @var NormalizableEntityInterface $entity */
            $entity = $reflection->newInstanceWithoutConstructor();
            return $entity->getMetadata();
        }, $map);

        return $metadata->admixList($metadataList);
    }
}
