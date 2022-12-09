<?php

namespace QueueBundle\Consumer;

use AppBundle\Manager\Feed\FeedManagerInterface;
use AppBundle\Manager\StoredQuery\StoredQueryManagerInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Entity\Query\StoredQuery;
use CacheBundle\Repository\QueryFeedRepository;
use CacheBundle\Repository\StoredQueryRepository;
use Common\Enum\StoredQueryStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DocumentsFetchComponent
 *
 * @package QueueBundle\Consumer
 */
class DocumentsFetchConsumer extends AbstractConsumer
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var StoredQueryManagerInterface
     */
    private $queryManager;

    /**
     * @var FeedManagerInterface
     */
    private $feedManager;

    /**
     * DocumentsFetchConsumer constructor.
     *
     * @param LoggerInterface             $logger       A LoggerInterface
     *                                                  instance.
     * @param EntityManagerInterface      $em           A EntityManagerInterface
     *                                                  instance.
     * @param StoredQueryManagerInterface $queryManager A StoredQueryManagerInterface
     *                                                  instance.
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        StoredQueryManagerInterface $queryManager
    ) {
        parent::__construct($logger, $em->getConnection());

        $this->em = $em;
        $this->queryManager = $queryManager;
    }

    /**
     * Execute consumer specific code.
     *
     * @param string $storedQueryId Fetched stored query id.
     *
     * @return mixed
     */
    protected function doExecute($storedQueryId)
    {
        $this->info('Got document fetch request for stored query', [ 'id' => $storedQueryId ]);

        if (! is_string($storedQueryId) || ($storedQueryId === '')) {
            $this->error('Stored query id should be not empty string');

            return true; // We return true in order to not requeue this invalid
                         // message again.
        }

        /** @var StoredQueryRepository $repository */
        $repository = $this->em->getRepository(StoredQuery::class);
        $query = $repository->find($storedQueryId);

        if (! $query instanceof StoredQuery) {
            throw new \LogicException(sprintf(
                'Can\'t find %s with id \'%s\'',
                StoredQuery::class,
                $storedQueryId
            ));
        }

        $previousStatus = $query->getStatus();
        $query = $this->queryManager->fetchDocuments($query);

        if ($previousStatus !== StoredQueryStatusEnum::SYNCED) {
            $this->info('Fetch documents for new stored query', [ 'id' => $storedQueryId ]);

            //
            // Remove excluded documents from feeds which are created for
            // this stored query.
            //
            /** @var QueryFeedRepository $repository */
            $repository = $this->em->getRepository(QueryFeed::class);
            $feeds = $repository->getWithExcludedDocumentsForQuery($query->getId());

            /** @var AbstractFeed $feed */
            foreach ($feeds as $feed) {
                $this->feedManager->deleteDocuments(
                    $feed,
                    \nspl\a\map(
                        \nspl\op\methodCaller('getId'),
                        $feed->getExcludedDocuments()
                    )
                );
            }
        } else {
            $this->info('Update already exists stored query', [ 'id' => $storedQueryId ]);
        }

        $query->setLastUpdateAt(new \DateTime());
        $this->em->persist($query);
        $this->em->flush();
        $this->info('Stored query successfully processed', [ 'id' => $storedQueryId ]);

        $this->em->clear();
        gc_collect_cycles();

        return true;
    }
}
