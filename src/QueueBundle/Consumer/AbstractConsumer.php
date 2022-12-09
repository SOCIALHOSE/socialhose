<?php

namespace QueueBundle\Consumer;

use Doctrine\DBAL\Connection;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractConsumer
 *
 * @package QueueBundle\Consumer
 */
abstract class AbstractConsumer implements ConsumerInterface
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * AbstractConsumer constructor.
     *
     * @param LoggerInterface $logger     A LoggerInterface instance.
     * @param Connection      $connection A Connection instance.
     */
    public function __construct(
        LoggerInterface $logger,
        Connection $connection
    ) {
        $this->logger = $logger;
        $this->connection = $connection;
    }

    /**
     * @param AMQPMessage $msg The message.
     *
     * @return mixed false to reject and requeue, any other value to acknowledge.
     */
    public function execute(AMQPMessage $msg)
    {
        //
        // The database may close a connection if we don't use it in long-running
        // code. And we should handle this situation 'cause doctrine will not
        // reconnect so we should check connection and manually reconnect if it's
        // closed.
        //

        $this->logger->info('This connection return ' . $this->connection->ping());
        if (! $this->connection->ping()) {
            try {
                $this->connection->close();
                $this->connection->connect();
            } catch (\Throwable $exception) {
                $this->logger->critical(sprintf(
                    '%s: Can\'t reconnect to database due to %s',
                    static::class,
                    $exception->getMessage()
                ));
            }
        }

        try {
            return $this->doExecute(trim($msg->getBody()));
        } catch (\Exception $exception) {
            $this->logError($exception);

            //
            // Because we don't want to reprocess failed job.
            //
            return true;
        }
    }

    /**
     * Add error message to log.
     *
     * @param string $message Some error message.
     * @param array  $context Logged message context.
     *
     * @return void
     */
    protected function error($message, array $context = [])
    {
        $this->logger->error(sprintf(
            '%s: %s',
            static::class,
            $message
        ), $context);
    }

    /**
     * Add info message to log.
     *
     * @param string $message Some info message.
     * @param array  $context Logged message context.
     *
     * @return void
     */
    protected function info($message, array $context = [])
    {
        $this->logger->info(sprintf(
            '%s: %s',
            static::class,
            $message
        ), $context);
    }

    /**
     * Execute consumer specific code.
     *
     * @param string $messageBody Sanitized message body.
     *
     * @return mixed
     */
    abstract protected function doExecute($messageBody);

    /**
     * @param \Exception $exception A occurred exception.
     *
     * @return void
     */
    private function logError(\Exception $exception)
    {
        $this->error(sprintf(
            'Exception \'%s\' with message \'%s\'',
            get_class($exception),
            $exception->getMessage()
        ), [ 'trace' => $exception->getTrace() ]);
    }
}
