<?php

namespace Command\Util;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CommandTest
 * @package Command\Util
 */
class CommandTest
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var BufferedOutput
     */
    private $output;

    /**
     * @var integer
     */
    private $exitCode;

    /**
     * Command constructor.
     *
     * @param Application $application A Application instance.
     * @param string      $command     Command name.
     * @param array       $params      Command parameters.
     */
    public function __construct(
        Application $application,
        $command,
        array $params = []
    ) {
        $this->application = $application;
        $this->input = new ArrayInput([ 'command' => $command ] + $params);
    }

    /**
     * Run this command.
     *
     * @return CommandTest
     */
    public function run()
    {
        $this->output = new BufferedOutput();
        $this->exitCode = $this->application->run($this->input, $this->output);

        return $this;
    }

    /**
     * Get exit code.
     *
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Get output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output->fetch();
    }
}
