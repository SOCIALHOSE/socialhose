<?php

namespace QueueBundle\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotificationsFetcherConsumer
 *
 * @package QueueBundle\Consumer
 */
class NotificationsFetcherConsumer extends AbstractConsumer
{

    const BUCKET_SIZE = 100;


    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * NotificationFetcherConsumer constructor.
     *
     * @param LoggerInterface   $logger     A LoggerInterface instance.
     * @param Connection        $connection A database Connection instance.
     * @param ProducerInterface $producer   A ProducerInterface instance.
     */
    public function __construct(
        LoggerInterface $logger,
        Connection $connection,
        ProducerInterface $producer,
        ContainerInterface $container
    ) {
        parent::__construct($logger, $connection);

        $this->connection = $connection;
        $this->producer = $producer;
        $this->container = $container;
    }

    /**
     * Execute consumer specific code.
     *
     * @param string $messageBody Sanitized message body.
     *
     * @return mixed
     */
    protected function doExecute($messageBody)
    {
        //
        // We don't need seconds, so for each date we set it to 0.
        //
        $date = new \DateTime($messageBody);
        if (! $date instanceof \DateTime) {
            $this->error('Invalid date', [ 'date' => $messageBody ]);

            return true; // We return true in order to not requeue this invalid
                         // message again.
        }
        $this->info('Fetch notification\'s', [ 'date' => $messageBody ]);

        //
        // Find all notification which should be send for specified date.
        // Also we should remove all fetched notification from scheduling.
        //
        $rows = $this->connection->fetchAll('
            SELECT notification_id, schedules FROM internal_notification_scheduling
            WHERE date = :date
            GROUP BY notification_id
        ', [ 'date' => $date->format('Y-m-d H:i:s') ]);
        $this->connection->executeQuery('
            DELETE FROM internal_notification_scheduling
            WHERE date = :date
        ', [ 'date' => $date->format('Y-m-d H:i:s') ]);

        //
        // All founded notification we should publish into queue.
        //
        foreach ($rows as $row) {
            $this->producer->publish(serialize($row));
        }

        return true;
    }
}
