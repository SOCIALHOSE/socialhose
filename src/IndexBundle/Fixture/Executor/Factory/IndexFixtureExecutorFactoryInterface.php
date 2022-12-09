<?php

namespace IndexBundle\Fixture\Executor\Factory;

use IndexBundle\Fixture\Executor\IndexFixtureExecutorInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;

/**
 * Interface IndexFixtureExecutorFactoryInterface
 * @package IndexBundle\Fixture\Executor\Factory
 */
interface IndexFixtureExecutorFactoryInterface
{

    /**
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function internal(InternalIndexInterface $index);

    /**
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function external(InternalIndexInterface $index);

    /**
     * @param SourceIndexInterface $index A SourceIndexInterface instance.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function source(SourceIndexInterface $index);
}
