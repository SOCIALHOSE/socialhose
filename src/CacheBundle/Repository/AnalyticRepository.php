<?php

namespace CacheBundle\Repository;

use CacheBundle\Entity\Analytic\Analytic;
use Doctrine\ORM\EntityRepository;

/**
 * Class AnalyticRepository
 * @package CacheBundle\Repository
 */
class AnalyticRepository extends EntityRepository
{

     /**
     * Get array of specified user analytics.
     *
     * @param integer $user A User entity id.
     *
     * @return Analytic[]
     */
    public function getList($user)
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Analytic')
            ->select(
                'partial Analytic.{id,createdAt,updatedAt}',
                'partial context.{hash,filters,rawFilters}'
            )
            ->leftJoin('Analytic.context', 'context')
            ->where($expr->eq('Analytic.owner', ':user'))
            ->setParameter('user', $user)
            ->addOrderBy('Analytic.id', 'desc')
            ->getQuery()
            ->getResult();
    }

}
