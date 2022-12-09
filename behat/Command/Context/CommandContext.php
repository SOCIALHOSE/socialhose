<?php

namespace Command\Context;

use Behat\Gherkin\Node\TableNode;
use Command\Util\CommandTest;
use Command\Util\CommandTestFactory;
use Common\Context\AbstractContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandContext
 * @package Command\Context
 */
class CommandContext extends AbstractContext
{

    /**
     * @var CommandTestFactory
     */
    private $factory;

    /**
     * @var CommandTest
     */
    private $command;

    /**
     * @param ContainerInterface $container   A ContainerInterface instance.
     * @param string             $fixturesDir Path to fixtures directory.
     */
    public function __construct(ContainerInterface $container, $fixturesDir)
    {
        parent::__construct($container, $fixturesDir);

        /** @var KernelInterface $kernel */
        $kernel = $container->get('kernel');
        $this->factory = new CommandTestFactory($kernel);
    }

    /**
     * @Given /^I run command (?P<command>.+)$/
     *
     * @param string    $name  Command name.
     * @param TableNode $table Command parameters in table format.
     *
     * @return void
     */
    public function runCommand($name, TableNode $table = null)
    {
        $params = [];
        if ($table !== null) {
            foreach ($table as $row) {
                $params[current($row)] = next($row);
            }
        }

        $this->command = $this->factory->create($name, $params)->run();
    }

    /**
     * @Then /^(?:|[Cc]ommand )[Rr]eturned (?P<code>\d+) exit code$/
     *
     * @param integer $code Command exit code.
     *
     * @return void
     */
    public function checkExitCode($code = 0)
    {
        self::assertEquals($code, $this->command->getExitCode());
    }
}
