<?php

namespace AppBundle\DataFixtures;

use IndexBundle\Fixture\IndexFixtureInterface;
use IndexBundle\Model\Generator\ExternalDocumentGenerator;
use IndexBundle\Model\Generator\InternalDocumentGenerator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class AbstractInternalFixture
 * Base class for internal index fixtures.
 *
 * @package AppBundle\DataFixtures\Index
 */
abstract class AbstractInternalIndexFixture implements
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
        $this->generator = new InternalDocumentGenerator();
    }

    /**
     * Return index type for this fixture.
     *
     * @return string
     */
    public function getIndex()
    {
        return self::INDEX_INTERNAL;
    }
}
