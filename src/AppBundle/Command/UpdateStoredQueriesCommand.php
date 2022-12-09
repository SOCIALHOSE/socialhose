<?php

namespace AppBundle\Command;

use CacheBundle\Entity\Query\StoredQuery;
use CacheBundle\Repository\StoredQueryRepository;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateStoredQueriesCommand
 * @package AppBundle\Command
 */
class UpdateStoredQueriesCommand extends Command
{

    const NAME = 'socialhose:stored-query:update';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * UpdateStoredQueriesCommand constructor.
     *
     * @param EntityManagerInterface $em       A EntityManagerInterface instance.
     * @param ProducerInterface      $producer A ProducerInterface instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        ProducerInterface $producer
    ) {
        parent::__construct(self::NAME);

        $this->em = $em;
        $this->producer = $producer;
    }


    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Update stored queries.');
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
        $queries = $this->getQueries();

        /** @var StoredQuery $query */
        foreach ($queries as $query) {
            $this->producer->publish($query->getId());
        }

        return 0;
    }

    /**
     * @return \Generator
     */
    private function getQueries()
    {
        /** @var StoredQueryRepository $repository */
        $repository = $this->em->getRepository(StoredQuery::class);

        $iterate = $repository->getForUpdating()->getQuery()->iterate();

        foreach ($iterate as $query) {
            $query = $query[0];
            yield $query;
            $this->em->detach($query);
        }
    }
}
