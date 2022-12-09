<?php

namespace CacheBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CommonFeedRepository
 * @package CacheBundle\Repository
 */
class CommonFeedRepository extends EntityRepository
{

    /**
     * Get single feed from repository.
     *
     * @param integer $id   A Feed entity instance id.
     * @param integer $user Filter feeds by specified owner if set.
     *
     * @return \CacheBundle\Entity\Feed\AbstractFeed|null
     */
    public function getOne($id, $user)
    {
        $expr = $this->_em->getExpressionBuilder();
        $condition = $expr->andX($expr->eq('Feed.id', ':id'));
        $parameters = [ 'id' => $id ];

        if ($user !== null) {
            $condition->add($expr->eq('Feed.user', ':user'));
            $parameters['user'] = $user;
        }

        return $this->createQueryBuilder('Feed')
            ->where($condition)
            ->setParameters($parameters)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
