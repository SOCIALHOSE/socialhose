<?php

namespace AppBundle\DataFixtures;

use IndexBundle\Fixture\IndexFixtureInterface;
use IndexBundle\Model\Generator\SourceDocumentGenerator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class AbstractSourceIndexFixture
 * Base class for source index fixtures.
 *
 * @package AppBundle\DataFixtures\Index
 */
abstract class AbstractSourceIndexFixture implements
    IndexFixtureInterface,
    ContainerAwareInterface
{

    use BaseFixtureTrait;

    /**
     * @var SourceDocumentGenerator
     */
    protected $generator;

    /**
     * AbstractExternalFixture constructor.
     */
    public function __construct()
    {
        $this->generator = new SourceDocumentGenerator();
    }

    /**
     * Return index type for this fixture.
     *
     * @return string
     */
    public function getIndex()
    {
        return self::INDEX_SOURCE;
    }
}
