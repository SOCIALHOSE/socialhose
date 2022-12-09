<?php

namespace CacheBundle\Repository;

use CacheBundle\Entity\Query\StoredQuery;
use Common\Enum\StoredQueryStatusEnum;
use Doctrine\ORM\EntityRepository;

/**
 * Class SimpleQueryRepository
 * @package CacheBundle\Repository
 */
class StoredQueryRepository extends EntityRepository implements
    QueryRepositoryInterface
{

    /**
     * Get query entity by internal representation of search query.
     *
     * @param string  $hash A search query hash.
     * @param integer $user A User entity id, who made search request.
     *
     * @return \CacheBundle\Entity\Query\StoredQuery|null
     */
    public function get($hash, $user = null)
    {
        $expr = $this->_em->getExpressionBuilder();

        $condition = $expr->andX($expr->eq('Query.hash', ':hash'));
        $parameters = [ 'hash' => $hash ];

        if ($user !== null) {
            $condition->add($expr->eq('Query.user', ':user'));
            $parameters['user'] = $user;
        }

        return $this->createQueryBuilder('Query')
            ->select('partial Query.{id, totalCount}')
            ->where($condition)
            ->setParameters($parameters)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * List stored queries ready for updating.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getForUpdating()
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Query')
            ->distinct('Query.id')
            ->join('Query.feeds', 'feeds')
            ->where($expr->andX(
                $expr->neq('Query.status', ':status'),
                $expr->eq('Query.limitExceed', 0)
            ))
            ->setParameter('status', StoredQueryStatusEnum::INITIALIZE);
    }

    /**
     * Get Query by feed
     *
     * @param integer $feed A Feed entity id.
     *
     * @return StoredQuery
     */
    public function getByFeed($feed)
    {
        return $this->createQueryBuilder('Query')
            ->leftJoin('Query.feeds', 'feeds')
            ->where('feeds.id = :feedId')
            ->setParameter(':feedId', $feed)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
