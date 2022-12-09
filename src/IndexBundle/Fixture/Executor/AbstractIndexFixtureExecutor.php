<?php

namespace IndexBundle\Fixture\Executor;

/**
 * Class AbstractIndexFixtureExecutor
 *
 * @package IndexBundle\Fixture\Executor
 */
abstract class AbstractIndexFixtureExecutor implements IndexFixtureExecutorInterface
{

    /**
     * @var \Closure|callable
     */
    protected $logger;

    /**
     * Set logger callback.
     *
     * Callback will receive one argument - message.
     *
     * @example function logger(string $message): void {}
     *
     * @param \Closure|callable $logger A function which will be used for
     *                                  log all messages.
     *
     * @return static
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Log specified message by using logger callback.
     *
     * @param string $message Some message.
     *
     * @return void
     */
    protected function log($message)
    {
        $logger = $this->logger;
        if (($logger instanceof  \Closure) || is_callable($logger)) {
            $logger($message);
        }
    }
}
