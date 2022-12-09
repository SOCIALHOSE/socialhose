<?php

namespace PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class PaymentRepository
 *
 * @package PaymentBundle\Repository
 */
class PaymentRepository extends EntityRepository
{

    /**
     * @return QueryBuilder
     */
    public function getListQueryBuilder()
    {
        return $this->createQueryBuilder('Payment')
            ->addSelect('Subscription, Plan, Owner')
            ->join('Payment.subscription', 'Subscription')
            ->join('Subscription.plan', 'Plan')
            ->join('Subscription.owner', 'Owner');
    }
}
