<?php

namespace AppBundle\Manager\Feed;

use CacheBundle\Entity\Document;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Repository\DocumentRepository;
use Common\Enum\FieldNameEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Class QueryFeedManager
 *
 * @package AppBundle\Manager\Feed
 */
class FeedManager implements FeedManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var InternalIndexInterface
     */
    private $index;

    /**
     * FeedManager constructor.
     *
     * @param EntityManagerInterface $em    A EntityManagerInterface instance.
     * @param InternalIndexInterface $index A InternalIndexInterface instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        InternalIndexInterface $index
    ) {
        $this->em = $em;
        $this->index = $index;
    }

    /**
     * Clip document to specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     * @param array        $ids  Array for Document entities ids.
     *
     * @return void
     */
    public function clip(AbstractFeed $feed, array $ids)
    {
        if (! $feed instanceof ClipFeed) {
            throw new \InvalidArgumentException('Can clip only to ' . ClipFeed::class);
        }

        if (count($ids) === 0) {
            return;
        }

        //
        // Create new page for already exists documents.
        //
        /** @var DocumentRepository $repository */
        $repository = $this->em->getRepository(Document::class);

        $documents = [];
        if (count($ids) > 0) {
            $documents = $repository->getByIds($ids);
        }

        $articleDocuments = [];
        foreach ($documents as $document) {
            $page = $feed->createPage(0);
            $document->addPage($page);

            $this->em->persist($page);

            $articleDocuments[] = $this->index->getStrategy()
                ->createDocument($document->getData())
                ->mapRawData(function (array $data) use ($feed) {
                    $data[FieldNameEnum::COLLECTION_ID] = $feed->getCollectionId();
                    $data[FieldNameEnum::COLLECTION_TYPE] = $feed->getCollectionType()->getValue();

                    return $data;
                });
        }

        $this->index->index($articleDocuments);

        $feed->setTotalCount($feed->getTotalCount() + count($ids));
        // Persist query just in case it has not been done yet.
        $this->em->persist($feed);
        $this->em->flush();
    }

    /**
     * Delete documents from specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     * @param array        $ids  Array of Document entities ids.
     *
     * @return void
     */
    public function deleteDocuments(AbstractFeed $feed, array $ids = [])
    {
        $factory = $this->index->getFilterFactory();
        $builder = $this->index->createRequestBuilder();

        if (count($ids) > 0) {
            //
            // Remove only specified document document.
            //
            $builder->addFilter($factory->in(FieldNameEnum::SEQUENCE, $ids));
        }

        $response = $builder
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_ID, $feed->getCollectionId()))
            ->addFilter($factory->eq(FieldNameEnum::COLLECTION_TYPE, $feed->getCollectionType()))
            ->setSources(['_id'])
            ->build()
            ->execute()
            ->getDocuments();

        $realIds = \nspl\a\map(\nspl\op\itemGetter('sequence'), $response);

        if (count($realIds) === 0) {
            return;
        }

        if ($feed instanceof ClipFeed) {
            //
            // For clip feeds we remove documents from index 'cause we copy all
            // documents for each clip feed.
            //
            $this->index->remove($realIds);
        } elseif ($feed instanceof QueryFeed) {
            $config = [];
            $script = sprintf(
                'if (ctx._source.%s.contains(%d)) { ctx.op = "none"} else {ctx._source.%s.add(%d)}',
                FieldNameEnum::DELETE_FROM,
                $feed->getId(),
                FieldNameEnum::DELETE_FROM,
                $feed->getId()
            );

            foreach ($realIds as $id) {
                $config[$id] = ['script' => $script];
            }

            $this->index->updateBulk($config);
        }
    }

    /**
     * Get index used by feed manager.
     *
     * @return InternalIndexInterface
     */
    public function getIndex()
    {
        return $this->index;
    }
}
