<?php

namespace CacheBundle\Repository;

use CacheBundle\Entity\Document;
use Common\Enum\CollectionTypeEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * Class DocumentRepository
 * @package CacheBundle\Repository
 */
class DocumentRepository extends EntityRepository
{

    /**
     * Get document for specified query.
     *
     * @param integer $query A Query entity id.
     * @param integer $page  Request page number.
     *
     * @return Document[]
     */
    public function getForQuery($query, $page)
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Document')
            ->addSelect('Comment, CommentAuthor')
            ->join('Document.pages', 'Page')
            ->leftJoin('Document.comments', 'Comment', Join::WITH, $expr->andX(
                $expr->eq('Comment.document', 'Document.id'),
                $expr->eq('Comment.new', 1)
            ))
            ->leftJoin('Comment.author', 'CommentAuthor')
            ->where($expr->andX(
                $expr->eq('Page.number', ':number'),
                $expr->eq('Page.query', ':query')
            ))
            ->setParameters([
                'number' => $page,
                'query' => $query,
            ])
            ->addOrderBy('Comment.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get documents by given ids for specified query without related pages.
     * Result will be ordered depends on specified ids order.
     *
     * @param integer        $collectionId   A document collection entity id.
     * @param string         $collectionType A document collection type.
     * @param array          $ids            Array of document ids.
     * @param string[]|array $fields         Array of required document fields. Fetch
     *                                       all if empty.
     *
     * @return Document[]
     */
    public function getFromCollectionByIds($collectionId, $collectionType, array $ids, array $fields = [])
    {
        $expr = $this->_em->getExpressionBuilder();

        $condition = $expr->andX($expr->in('Document.id', $ids));
        switch ($collectionType) {
            case CollectionTypeEnum::FEED:
                $condition->add($expr->eq('Page.clipFeed', ':collectionId'));
                break;

            case CollectionTypeEnum::QUERY:
                $condition->add($expr->eq('Page.query', ':collectionId'));
                break;

            default:
                throw new \InvalidArgumentException('Invalid collection type.');
        }

        $select = 'Document';
        if (count($fields) > 0) {
            $select = 'partial Document.{'. implode(',', $fields). '}';
        }

        $results = $this->createQueryBuilder('Document')
            ->select($select)
            ->addSelect('Comment, CommentAuthor, Page')
            ->join('Document.pages', 'Page')
            ->leftJoin('Document.comments', 'Comment', Join::WITH, $expr->andX(
                $expr->eq('Comment.document', 'Document.id'),
                $expr->eq('Comment.new', 1)
            ))
            ->leftJoin('Comment.author', 'CommentAuthor')
            ->where($condition)
            ->setParameter('collectionId', $collectionId)
            //
            // We should clearly say how we want to order because mysql does not
            // guarantee what result will be ordered by primary key.
            //
            ->addOrderBy('Document.id', 'asc')
            ->addOrderBy('Comment.createdAt', 'desc')
            ->getQuery()
            ->getResult();

        //
        // Now we use specified ids as index for fetched documents. Since we got
        // ordered result we may use binary search for fetching proper document
        // by id.
        //

        $orderedResult = [];
        foreach ($ids as $id) {
            $idx = \app\a\binarySearch($results, $id, \nspl\op\methodCaller('getId'));
            if ($idx === false) {
                continue;
            }

            $orderedResult[] = $results[$idx];
        }

        return $orderedResult;
    }

    /**
     * Get documents by given ids for specified query.
     *
     * @param array $ids Array of document ids.
     *
     * @return Document[]
     */
    public function getByIds(array $ids)
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Document')
            ->addSelect('Comment, CommentAuthor')
            ->where($expr->in('Document.id', $ids))
            ->leftJoin('Document.comments', 'Comment', Join::WITH, $expr->andX(
                $expr->eq('Comment.document', 'Document.id'),
                $expr->eq('Comment.new', 1)
            ))
            ->leftJoin('Comment.author', 'CommentAuthor')
            ->addOrderBy('Comment.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $fields Array of required fields names, `id` return
     *                      always.
     * @param array $ids    Array of document ids.
     *
     * @return Document[]
     */
    public function getWithFieldsByIds(array $fields, array $ids)
    {
        return $this->createQueryBuilder('Document')
            ->select('partial Document.{id, '.implode(',', $fields).'}')
            ->where('Document.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check whether the documents exists.
     *
     * @param string[] $checkedIds Array of document ids.
     *
     * @return string[] Array of not exists ids from passed.
     */
    public function checkIds(array $checkedIds)
    {
        $expr = $this->_em->getExpressionBuilder();

        $existsIds = $this->createQueryBuilder('Document')
            ->select('Document.id')
            ->where($expr->in('Document.id', array_map(\nspl\op\str, $checkedIds)))
            ->getQuery()
            ->getArrayResult();

        return array_diff($checkedIds, \nspl\a\flatten($existsIds));
    }

    /**
     * Remove from provided ids whose document id which already exists in specified document collection.
     *
     * @param string $collectionId   A DocumentCollectionInterface entity id.
     * @param string $collectionType A DocumentCollectionInterface type.
     * @param array  $ids            Array of document ids.
     *
     * @return string[] Array of documents which is not exists in specified document collection.
     */
    public function sanitizeIds($collectionId, $collectionType, array $ids)
    {
        $expr = $this->_em->getExpressionBuilder();

        $condition = $expr->andX($expr->in('Document.id', array_map(\nspl\op\str, $ids)));
        switch ($collectionType) {
            case CollectionTypeEnum::FEED:
                $condition->add($expr->eq('Page.clipFeed', ':collectionId'));
                break;

            case CollectionTypeEnum::QUERY:
                $condition->add($expr->eq('Page.query', ':collectionId'));
                break;

            default:
                throw new \InvalidArgumentException('Invalid collection type.');
        }

        $existsIds = $this->createQueryBuilder('Document')
            ->select('Document.id')
            ->join('Document.pages', 'Page')
            ->where($condition)
            ->setParameter('collectionId', $collectionId)
            ->getQuery()
            ->getArrayResult();

        return array_diff($ids, \nspl\a\flatten($existsIds));
    }

    /**
     * @param array $queryId
     * @return array
     */
    public function getByQuery(array $queryId)
    {
        return $this->createQueryBuilder('Document')
            ->select('Document.data','Query.id')
            ->join('Document.pages', 'Page')
            ->join('Page.query', 'Query')
            ->where('Query.id IN (:queryId)')
            ->setParameter('queryId', $queryId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array $clipFeedId
     * @return array
     */
    public function getByClip(array $clipFeedId)
    {
        return $this->createQueryBuilder('Document')
            ->select('Document.data','IDENTITY(Page.clipFeed) as clipFeedId ')
            ->join('Document.pages', 'Page')
            ->where('Page.clipFeed IN (:clipFeedId)')
            ->setParameter('clipFeedId', $clipFeedId)
            ->getQuery()
            ->getResult();
    }
}
