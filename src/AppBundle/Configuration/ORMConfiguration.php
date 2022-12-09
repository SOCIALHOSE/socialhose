<?php

namespace AppBundle\Configuration;

use AdminBundle\Entity\SiteSettings;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ORMConfiguration
 * @package AppBundle\Configuration
 */
class ORMConfiguration extends AbstractConfiguration
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Configuration constructor.
     *
     * @param ConfigurationDefinitionMap $definitions A ConfigurationDefinitionMap
     *                                                instance.
     * @param EntityManagerInterface     $em          A EntityManagerInterface
     *                                                instance.
     */
    public function __construct(
        ConfigurationDefinitionMap $definitions,
        EntityManagerInterface $em
    ) {
        $this->em = $em;

        parent::__construct($definitions);
    }

    /**
     * Create default parameter from config.
     *
     * @param string $name Parameter name.
     *
     * @return ConfigurationParameterInterface
     */
    protected function createParameter($name)
    {
        $config = $this->definitions->getDefinition($name);

        return SiteSettings::create()
            ->setSection($config['section'])
            ->setName($name)
            ->setTitle($config['title'])
            ->setValue($config['default']);
    }

    /**
     * Load configuration from storage.
     *
     * @return ConfigurationParameterInterface[]
     */
    protected function loadData()
    {
        return $this->em->getRepository(SiteSettings::class)->findAll();
    }

    /**
     * @param ConfigurationParameterInterface[]|array $changed Array of changed
     *                                                         instances.
     * @param ConfigurationParameterInterface[]|array $removed Array of removed
     *                                                         parameter names.
     *
     * @return void
     */
    protected function doSync(array $changed, array $removed)
    {
        foreach ($changed as $parameter) {
            $this->em->persist($parameter);
        }

        foreach ($removed as $parameter) {
            $this->em->remove($parameter);
        }

        $this->em->flush();
    }
}
