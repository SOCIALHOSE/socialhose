<?php

namespace CacheBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class SimpleQueryRepository
 * @package CacheBundle\Repository
 */
class SimpleQueryRepository extends EntityRepository implements
    QueryRepositoryInterface
{

    /**
     * Get query entity by internal representation of search query.
     *
     * @param string  $hash A search query hash.
     * @param integer $user A User entity id, who made search request.
     *
     * @return \CacheBundle\Entity\Query\SimpleQuery|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($hash, $user = null)
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Query')
            ->select('partial Query.{id, totalCount, expirationDate, raw, rawFilters, rawAdvancedFilters}')
            ->where($expr->andX(
                $expr->eq('Query.hash', ':hash'),
                $expr->gt('Query.expirationDate', ':date')
            ))
            ->setParameters([
                'hash' => $hash,
                'date' => date_create(),
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get all old queries.
     *
     * @return integer[] Old queries ids.
     */
    public function getOld()
    {
        $expr = $this->_em->getExpressionBuilder();

        // Get all old queries ids.
        $ids = $this->createQueryBuilder('Query')
            ->select('Query.id')
            ->where($expr->lte('Query.expirationDate', ':date'))
            ->setParameter('date', date_create())
            ->getQuery()
            ->getArrayResult();
        return array_map(function (array $row) {
            return $row['id'];
        }, $ids);
    }
}
