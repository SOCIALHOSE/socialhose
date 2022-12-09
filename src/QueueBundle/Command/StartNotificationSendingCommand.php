<?php

namespace QueueBundle\Command;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StartNotificationSendingCommand
 *
 * Start notification sending process by publishing required date in queue.
 *
 * @package QueueBundle\Notification\Command
 *
 * @see NotificationFetcherCommand fetcher worker.
 */
class StartNotificationSendingCommand extends Command
{

    const NAME = 'socialhose:notification:start';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * StartNotificationSendingCommand constructor.
     *
     * @param LoggerInterface   $logger   A LoggerInterface instance.
     * @param ProducerInterface $producer A ProducerInterface instance.
     */
    public function __construct(
        LoggerInterface $logger,
        ProducerInterface $producer,
        ContainerInterface $container
    ) {
        parent::__construct(self::NAME);

        $this->logger = $logger;
        $this->producer = $producer;
        $this->container = $container;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Start notification sending process.');
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $date = date_create();
        $date = $date
            ->setTime($date->format('H'), $date->format('i'))
            ->format('c');
        $this->logger->info('Initialize notification sending for date '. $date);
        $this->producer->publish($date);

        return 0;
    }
}
