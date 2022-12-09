<?php

namespace AppBundle\Command;

use AppBundle\Manager\Source\SourceManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FetchSourcesCommand
 * @package AppBundle\Command
 */
class FetchSourcesCommand extends AbstractSingleCopyCommand
{

    /**
     * Command name.
     */
    const NAME = 'socialhose:sources:fetch';

    /**
     * @var SourceManagerInterface
     */
    private $manager;

    /**
     * FetchSourcesCommand constructor.
     *
     * @param SourceManagerInterface $manager A SourceManagerInterface instance.
     * @param LoggerInterface        $logger  A LoggerInterface instance.
     */
    public function __construct(
        SourceManagerInterface $manager,
        LoggerInterface $logger
    ) {
        parent::__construct(self::NAME, $logger);

        $this->manager = $manager;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Fetch all sources from external index.');
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $this->manager->pullFromExternal();

        return 0;
    }
}
