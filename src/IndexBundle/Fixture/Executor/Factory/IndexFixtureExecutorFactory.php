<?php

namespace IndexBundle\Fixture\Executor\Factory;

use IndexBundle\Fixture\Executor\FilteredIndexFixtureExecutor;
use IndexBundle\Fixture\Executor\IndexFixtureExecutor;
use IndexBundle\Fixture\Executor\IndexFixtureExecutorInterface;
use IndexBundle\Fixture\IndexFixtureInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;

/**
 * Class IndexFixtureExecutorFactory
 * @package IndexBundle\Fixture\Executor\Factory
 */
class IndexFixtureExecutorFactory implements IndexFixtureExecutorFactoryInterface
{

    /**
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function internal(InternalIndexInterface $index)
    {
        return new FilteredIndexFixtureExecutor(
            new IndexFixtureExecutor($index),
            function (IndexFixtureInterface $fixture) {
                return $fixture->getIndex() === IndexFixtureInterface::INDEX_INTERNAL;
            }
        );
    }

    /**
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function external(InternalIndexInterface $index)
    {
        return new FilteredIndexFixtureExecutor(
            new IndexFixtureExecutor($index),
            function (IndexFixtureInterface $fixture) {
                return $fixture->getIndex() === IndexFixtureInterface::INDEX_EXTERNAL;
            }
        );
    }

    /**
     * @param SourceIndexInterface $index A SourceIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function source(SourceIndexInterface $index)
    {
        return new FilteredIndexFixtureExecutor(
            new IndexFixtureExecutor($index),
            function (IndexFixtureInterface $fixture) {
                return $fixture->getIndex() === IndexFixtureInterface::INDEX_SOURCE;
            }
        );
    }
}
