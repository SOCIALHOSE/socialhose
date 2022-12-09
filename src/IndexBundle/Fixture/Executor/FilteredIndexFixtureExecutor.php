<?php

namespace IndexBundle\Fixture\Executor;

/**
 * Class FilteredIndexFixtureExecutor
 * Runs only those fixtures that have been filtered.
 *
 * @package IndexBundle\Fixture\Executor
 */
class FilteredIndexFixtureExecutor extends AbstractIndexFixtureExecutor
{

    /**
     * @var IndexFixtureExecutorInterface
     */
    private $executor;

    /**
     * @var \Closure|callable
     */
    private $filter;

    /**
     * IndexFixtureExecutor constructor.
     *
     * @param IndexFixtureExecutorInterface $executor A
     *                                                IndexFixtureExecutorInterface
     *                                                instance.
     * @param \Closure|callable             $filter   Filter function.
     */
    public function __construct(
        IndexFixtureExecutorInterface $executor,
        $filter
    ) {
        if ((!$filter instanceof \Closure) || ! is_callable($filter)) {
            throw new \InvalidArgumentException('Filter must be valid callable of closure.');
        }

        $this->executor = $executor;
        $this->filter = $filter;
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
        $fixtures = array_filter($fixtures, $this->filter);
        $this->executor->setLogger($this->logger)->execute($fixtures);
    }
}
