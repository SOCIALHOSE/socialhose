<?php

namespace IndexBundle\Fixture\Executor;

/**
 * Interface IndexFixtureExecutorInterface
 * @package IndexBundle\Fixture\Executor
 */
interface IndexFixtureExecutorInterface
{

    /**
     * Set logger callback.
     *
     * @param \Closure|callable $logger A function which will be used for
     *                                  log all messages.
     *
     * @return IndexFixtureExecutorInterface
     */
    public function setLogger($logger);

    /**
     * Execute specified fixtures.
     *
     * @param array $fixtures Array of IndexFixtureInterface instances.
     *
     * @return void
     */
    public function execute(array $fixtures);
}
