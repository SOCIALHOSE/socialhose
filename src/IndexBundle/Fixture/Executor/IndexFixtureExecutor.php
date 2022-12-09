<?php

namespace IndexBundle\Fixture\Executor;

use IndexBundle\Fixture\IndexFixtureInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Class IndexFixtureExecutor
 * Run all fixtures in one connection.
 *
 * @package IndexBundle\Fixture\Executor
 */
class IndexFixtureExecutor extends AbstractIndexFixtureExecutor
{

    /**
     * @var InternalIndexInterface
     */
    private $index;

    /**
     * IndexFixtureExecutor constructor.
     *
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     */
    public function __construct(InternalIndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * Execute specified fixtures.
     *
     * @param array $fixtures Array of IndexFixtureInterface instances.
     *
     * @return void
     */
    public function execute(array $fixtures)
    {
        /** @var IndexFixtureInterface $fixture */
        foreach ($fixtures as $fixture) {
            $this->log('loading ' . get_class($fixture) . " [{$fixture->getIndex()}]");
            $fixture->load($this->index);
        }
    }
}
