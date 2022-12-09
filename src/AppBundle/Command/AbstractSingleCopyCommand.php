<?php

namespace AppBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Class AbstractSingleCopyCommand
 *
 * Base class for commands which should be run in single instance at time.
 *
 * @package AppBundle\Command
 */
abstract class AbstractSingleCopyCommand extends Command
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * AbstractSingleCopyCommand constructor.
     *
     * @param string          $name   Command name.
     * @param LoggerInterface $logger A LoggerInterface instabce.
     */
    public function __construct($name, LoggerInterface $logger)
    {
        parent::__construct($name);

        $this->logger = $logger;
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
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lock = new LockHandler($this->getName());
        if (!$lock->lock()) {
            $output->writeln(sprintf(
                'Command \'%s\' is already executing.',
                $this->getName()
            ));

            return 1;
        }

        try {
            $result = $this->doExecute($input, $output);
        } catch (\Exception $exception) {
            $this->logger->critical(sprintf(
                'Command \'%s\' got exception \'%s\' while executing. %s',
                $this->getName(),
                get_class($exception),
                $exception->getMessage()
            ));
            $result = 127;
        } finally {
            $lock->release();
        }

        return $result;
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     */
    abstract protected function doExecute(InputInterface $input, OutputInterface $output);
}
