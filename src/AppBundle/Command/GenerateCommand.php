<?php

namespace AppBundle\Command;

use IndexBundle\Index\External\InternalHoseIndex;
use IndexBundle\Model\Generator\ExternalDocumentGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCommand
 *
 * This command generate random documents and put it into demo elasticsearch
 * server.
 *
 * @package AppBundle\Command
 */
class GenerateCommand extends Command
{

    /**
     * Command name.
     */
    const NAME = 'socialhose:generate';

    /**
     * @var InternalHoseIndex
     */
    private $index;

    /**
     * GenerateCommand constructor.
     *
     * @param InternalHoseIndex $index A InternalHoseIndex instance.
     */
    public function __construct(InternalHoseIndex $index)
    {
        parent::__construct(self::NAME);

        $this->index = $index;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate random documents. Use only for development.')
            ->addOption(
                'count',
                'c',
                InputOption::VALUE_REQUIRED,
                'How much document create',
                10
            );
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
        $generator = new ExternalDocumentGenerator();

        $count = $input->getOption('count');

        $documents = [];
        for ($i = 0; $i < $count; ++$i) {
            $documents[] = $generator->generate();
        }

        $this->index->index($documents);

        return 0;
    }
}
