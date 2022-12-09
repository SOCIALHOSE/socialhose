<?php

namespace Common\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Common\Util\DatabaseHelper;
use Common\Util\Index\InternalSourceConnection;
use Common\Util\Processor\DataProcessor;
use Common\Util\Index\ExternalIndexConnection;
use Common\Util\Index\InternalIndexConnection;
use Common\Util\Matcher\AppMatcher;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Fixture\Executor\Factory\IndexFixtureExecutorFactory;
use IndexBundle\Fixture\Executor\Factory\IndexFixtureExecutorFactoryInterface;
use IndexBundle\Fixture\Loader\IndexFixtureLoader;
use Seld\JsonLint\JsonParser;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractContext
 * Base class for all application test context's.
 *
 * @package Common\Context
 */
class AbstractContext extends \PHPUnit_Framework_Assert implements Context
{

    use DatabaseContextTrait,
        IndexContextTrait;

    /**
     * True if test running with debug.
     *
     * @var boolean
     */
    protected $debug;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Path to data fixtures directory.
     *
     * @var string
     */
    protected $fixturesDir;

    /**
     * @var DataProcessor
     */
    protected $processor;

    /**
     * @var IndexFixtureExecutorFactoryInterface
     */
    protected $indexExecutorFactory;

    /**
     * True if all indices initialized.
     *
     * @var boolean
     */
    private static $indexInitialized = false;

    /**
     * Setup database schemas.
     *
     * @BeforeSuite
     *
     * @return void
     */
    public static function setup()
    {
        if (strtolower(trim(getenv('WITHOUT_CLEAR'))) !== 'true') {
            system('./bin/console --env=test cache:clear');
            system('./bin/console --env=test doctrine:database:create --if-not-exists -n');
            system('./bin/console --env=test doctrine:schema:update --force -n');
        }
    }

    /**
     * Clear external index and load fixtures before scenario.
     *
     * @BeforeScenario @external-index-fixtures
     *
     * @return void
     */
    public function setupExternalIndexFixtures()
    {
        $this->createIndexes();

        echo 'Purge external index ... ';
        $this->externalIndex->purge();
        echo 'done'. PHP_EOL;

        echo 'Load indices fixtures: '. PHP_EOL;

        $loader = new IndexFixtureLoader($this->container);
        $loader->loadFromDirectory($this->fixturesDir);
        $this->indexExecutorFactory->external($this->externalIndex->getIndex())
            ->setLogger(function ($message) {
                echo "  > {$message}". PHP_EOL;
            })
            ->execute($loader->getFixtures());

        // Wait to insure that all fixtures was indexed.
        sleep(2);
    }

    /**
     * Clear external index and load fixtures before scenario.
     *
     * @BeforeScenario @internal-index-fixtures
     *
     * @return void
     */
    public function setupInternalIndexFixtures()
    {
        $this->createIndexes();

        echo 'Purge internal index ... ';
        $this->internalIndex->purge();
        echo 'done'. PHP_EOL;

        echo 'Load indices fixtures: '. PHP_EOL;

        $loader = new IndexFixtureLoader($this->container);
        $loader->loadFromDirectory($this->fixturesDir);
        $this->indexExecutorFactory->internal($this->internalIndex->getIndex())
            ->setLogger(function ($message) {
                echo "  > {$message}". PHP_EOL;
            })
            ->execute($loader->getFixtures());

        // Wait to insure that all fixtures was indexed.
        sleep(2);
    }

    /**
     * Clear external index and load fixtures before scenario.
     *
     * @BeforeScenario @source-index-fixtures
     *
     * @return void
     */
    public function setupSourceIndexFixtures()
    {
        //
        // Remove source_update.date
        //
        // NOTICE: Insure that you don't run test in production :)
        //
        unlink(realpath(__DIR__ . '/../../../var/source_update.date'));

        $this->createIndexes();
        $this->setupExternalIndexFixtures();

        echo 'Purge source index ... ';
        $this->sourceIndex->purge();
        echo 'done'. PHP_EOL;

        echo 'Load indices fixtures: '. PHP_EOL;

        $loader = new IndexFixtureLoader($this->container);
        $loader->loadFromDirectory($this->fixturesDir);
        $this->indexExecutorFactory->source($this->sourceIndex->getIndex())
            ->setLogger(function ($message) {
                echo "  > {$message}". PHP_EOL;
            })
            ->execute($loader->getFixtures());

        // Wait to insure that all fixtures was indexed.
        sleep(2);
    }

    /**
     * Create indexes if we want to upload index fixtures.
     *
     * @return void
     */
    public function createIndexes()
    {
        if (! self::$indexInitialized
            && (strtolower(trim(getenv('WITHOUT_CLEAR'))) !== 'true')) {
            $this->externalIndex->setup();
            $this->internalIndex->setup();
            $this->sourceIndex->setup();

            self::$indexInitialized = true;
        }
    }

    /**
     * @param ContainerInterface $container   A ContainerInterface instance.
     * @param string             $fixturesDir Path to fixtures directory.
     */
    public function __construct(ContainerInterface $container, $fixturesDir)
    {
        $this->debug = getenv('DEBUG') !== false;

        $this->container = $container;
        $this->fixturesDir = realpath($fixturesDir);

        if ($container->getParameter('kernel.environment') !== 'test') {
            $message = 'You should run test in test environment /:|';
            throw new \InvalidArgumentException($message);
        }

        // Create database helper.
        // See Common\Context\DatabaseContextTrait
        $this->dataBaseHelper = new DatabaseHelper($container->get('doctrine'));

        // Get serializer metadata for all entities.
        // We make it for simplification testing process. With this information
        // we can make more powerful expanders and matchers which help as to
        // write less but make more.
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        // Get all available entity fqcn's.
        $fqcnList = array_map(function (ClassMetadata $metadata) {
            return $metadata->getName();
        }, $em->getMetadataFactory()->getAllMetadata());

        $entities = $this->processEntityMetadata($em, $fqcnList);
        // Register all entities.
        AppMatcher::registerEntities($entities);

        $this->processor = new DataProcessor($container);

        // Decorate indices connections.
        $this->externalIndex = new ExternalIndexConnection(
            $this->get('index.external')
        );

        $this->internalIndex = new InternalIndexConnection(
            $this->get('index.articles')
        );

        $this->sourceIndex = new InternalSourceConnection(
            $this->get('index.sources')
        );

        $this->indexExecutorFactory = new IndexFixtureExecutorFactory();
    }

    /**
     * @When /^(?:|I )[Ww]ait (?P<milliseconds>\d+) millisecond(?: until| for)?[\w\s]*$/
     *
     * @param integer $milliseconds Seconds count.
     *
     * @return void
     */
    public function wait($milliseconds)
    {
        usleep($milliseconds * 1000);
    }

    /**
     * Gets a service.
     *
     * @param string $id The service identifier.
     *
     * @return object The associated service.
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name.
     *
     * @return mixed The parameter value.
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Match value against specified pattern.
     *
     * @param mixed  $value   Matched value.
     * @param mixed  $pattern Pattern.
     * @param string $error   Occurred error.
     *
     * @return boolean
     */
    protected function match($value, $pattern, &$error)
    {
        $value = $this->processor->process($value);

        $pattern = preg_replace('/\\s{2,}/', '', $pattern);

        // Lint pattern only if it contains json.
        if (($pattern[0] === '{') || ($pattern[0] === '[')) {
            // Lint pattern.
            $lint = new JsonParser();
            $exception = $lint->lint($pattern);
            if ($exception !== null) {
                throw new \RuntimeException('Pattern lint: ' . $exception->getMessage());
            }
        }

        // Process expressions between ##.
        $pattern = $this->processor->process($pattern);

        return AppMatcher::match($value, $pattern, $error);
    }

    /**
     * Lint json.
     *
     * @param PyStringNode|string $json Json to lint.
     *
     * @return void
     */
    protected function lintJson($json)
    {
        if ($json instanceof PyStringNode) {
            $json = $json->getRaw();
        }

        $linter = new JsonParser();
        $exception = $linter->lint($json);

        if ($exception !== null) {
            throw $exception;
        }
    }
}
