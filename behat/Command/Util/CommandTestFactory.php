<?php

namespace Command\Util;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandTestFactory
 * @package Command\Util
 */
class CommandTestFactory
{

    /**
     * @var Application
     */
    private $application;

    /**
     * CommandRunner constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        // If don't set to false, application will call 'exit' after command was
        // executed.
        $this->application->setAutoExit(false);
    }

    /**
     * Create new command instance.
     *
     * @param string $command Command name.
     * @param array  $params  Command parameters.
     *
     * @return CommandTest
     */
    public function create($command, array $params = [])
    {
        return new CommandTest($this->application, $command, $params);
    }
}
