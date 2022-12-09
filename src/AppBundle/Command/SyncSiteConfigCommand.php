<?php

namespace AppBundle\Command;

use AppBundle\Configuration\ConfigurationInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SyncSiteConfigCommand
 * @package AppBundle\Command
 */
class SyncSiteConfigCommand extends Command
{

    const NAME = 'socialhose:site-settings:sync';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * SyncSiteConfigCommand constructor.
     *
     * @param ConfigurationInterface $configuration A ConfigurationInterface
     *                                              instance.
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        parent::__construct(self::NAME);

        $this->configuration = $configuration;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Sync site settings with base');
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer null or 0 if everything went fine, or an error code.
     *
     * @throws \LogicException When this abstract method is not implemented.
     *
     * @see setCode()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configuration->syncWithDefinitions();

        return 0;
    }
}
