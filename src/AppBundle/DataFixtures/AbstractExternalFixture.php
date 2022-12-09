<?php

namespace AppBundle\DataFixtures;

use IndexBundle\Fixture\IndexFixtureInterface;
use IndexBundle\Model\Generator\ExternalDocumentGenerator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class AbstractExternalFixture
 * Base class for external index fixtures.
 *
 * @package AppBundle\DataFixtures\Index
 */
abstract class AbstractExternalFixture implements
    IndexFixtureInterface,
    ContainerAwareInterface
{

    use BaseFixtureTrait;

    /**
     * @var ExternalDocumentGenerator
     */
    protected $generator;

    /**
     * AbstractExternalFixture constructor.
     */
    public function __construct()
    {
        $this->generator = new ExternalDocumentGenerator();
    }

    /**
     * Return index type for this fixture.
     *
     * @return string
     */
    public function getIndex()
    {
        return self::INDEX_EXTERNAL;
    }
}
