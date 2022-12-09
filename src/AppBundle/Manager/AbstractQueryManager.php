<?php

namespace AppBundle\Manager;

use AppBundle\Response\SearchResponse;
use AppBundle\Response\SearchResponseInterface;
use CacheBundle\Entity\Document;
use CacheBundle\Entity\DocumentCollectionInterface;
use CacheBundle\Repository\DocumentRepository;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Model\ArticleDocumentInterface;

/**
 * Class AbstractQueryManager
 * Base class for query managers.
 *
 * @package AppBundle\Manager
 */
abstract class AbstractQueryManager
{

    /**
     * Size of documents flush buckets.
     */
    const FLUSH_BUCKET_SIZE = 10;

    /**
     * @var array
     */
    private $bucket = [];

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * AbstractQueryManager constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param SearchResponseInterface     $response   A SearchResponseInterface
     *                                                instance..
     * @param DocumentCollectionInterface $collection A DocumentCollectionInterface
     *                                                entity instance.
     * @param IndexStrategyInterface      $strategy   A DocumentNormalizerInterface
     *                                                instance.
     * @param integer                     $pageNumber A requested page number.
     *
     * @return SearchResponseInterface
     */
    protected function cache(
        SearchResponseInterface $response,
        DocumentCollectionInterface $collection,
        IndexStrategyInterface $strategy,
        $pageNumber
    ) {
       if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }
        //
        // Persist just in case it has not been done yet.
        //
        $this->em->persist($collection);

        // Obviously, we should not process response which return zero results.
        $responseCount = count($response);
        if ($responseCount <= 0) {
            return $response;
        }

        // Array of all founded document ids.
        $ids = [];
        // Map between document id and index in response.
        $documentIndex = [];

        //
        // Fetch all documents ids from response and create map between id and
        // index.
        //
        $index = 0;
        /** @var ArticleDocumentInterface[] $documents */
        $documents = $response->getDocuments();
        foreach ($documents as $articleDocument) {
            $id = $articleDocument->getId();

            $ids[$id] = true;
            $documentIndex[$id] = $index++;
        }

        //
        // Insure that we don't get same document more then once.
        //
        $ids = array_keys($ids);

        //
        // Check which documents we already have and which we didn't have. For
        // the firsts we create new page entity. For the seconds we created new
        // document and also create new page.
        //
        /** @var DocumentRepository $repository */
        $repository = $this->em->getRepository(Document::class);

        $nonExistsIds = $repository->checkIds($ids);
        // Get ids of exists documents.
        $existsIds = array_diff($ids, $nonExistsIds);

        //
        // Parse and create documents entities and pages entities for each
        // document which we don't find in our database.
        //
        $newDocuments = [];
        try {
            foreach ($nonExistsIds as $id) {
                //
                // For parsing we use search response from external index and
                // document index from response which we got from map.
                //
                $document = $documents[$documentIndex[$id]];
                $document = $this->persistEntityBucket($document->toDocumentEntity());
                $newDocuments[] = $document;

                $page = $this->persistEntityBucket($collection->createPage($pageNumber));
                $document->addPage($page);
            }
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Got error while persisting not exists document with id \'%s\'. %s',
                $id,
                $exception->getMessage()
            ), 0, $exception);
        }

        //
        // Create new page for already exists documents.
        //
        $existsDocuments = [];
        if (count($existsIds) > 0) {
            $existsDocuments = array_map(function ($id) {
                return $this->em->getReference(Document::class, $id);
            }, $existsIds);
        }

        try {
            foreach ($existsDocuments as $document) {
                $page = $this->persistEntityBucket($collection->createPage($pageNumber));
                $document->addPage($page);
            }
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Got error while binding exists document with id \'%s\' to new page. %s',
                $document->getId(),
                $exception->getMessage()
            ), 0, $exception);
        }

        $this->flushRemain();
        $documents = array_merge($newDocuments, $existsDocuments);
        usort($documents, [$this, 'dateCompare']);

        return new SearchResponse(
            $this->articleDocumentsFromEntities($documents, $strategy),
            [],
            $collection->getTotalCount(),
            count($nonExistsIds)
        );
    }

    /**
     * @param array                  $entities Array of converted entities.
     * @param IndexStrategyInterface $strategy Used index strategy.
     *
     * @return ArticleDocumentInterface[]
     */
    protected function articleDocumentsFromEntities(array $entities, IndexStrategyInterface $strategy)
    {
        return \nspl\a\map(function (Document $document) use ($strategy) {
            $data = $document->getData();
            $data['__comments'] = $document->getComments();
            $data['__commentsCount'] = $document->getCommentsCount();

            return $strategy->createDocument($data);
        }, $entities);
    }

    /**
     * @param object $entity Persisted entity.
     *
     * @return object
     */
    private function persistEntityBucket($entity)
    {
        $this->bucket[] = $entity;
        $this->em->persist($entity);

        if (count($this->bucket) >= self::FLUSH_BUCKET_SIZE) {
            $this->em->flush();
            $this->bucket = [];
        }

        return $entity;
    }

    /**
     * @return void
     */
    private function flushRemain()
    {
        if (count($this->bucket) > 0) {
            $this->em->flush();
            $this->bucket = [];
        }
    }

    /**
     * @param Document $doc1
     * @param Document $doc2
     * @return false|int
     */
    public static function dateCompare(Document $doc1, Document $doc2) {
        $data1 = $doc1->getData();
        $data2 = $doc2->getData();
        $datetime1 = strtotime($data1['published']);
        $datetime2 = strtotime($data2['published']);
        return $datetime2 - $datetime1;
    }
}
