<?php

namespace PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use PaymentBundle\Entity\Agreement;
use PaymentBundle\Enum\PaymentGatewayEnum;

/**
 * Class AgreementRepository
 *
 * @package PaymentBundle\Repository
 */
class AgreementRepository extends EntityRepository
{

    /**
     * @param PaymentGatewayEnum $gateway A used payment gateway.
     * @param string             $id      Platform specific agreement id.
     *
     * @return Agreement|null
     */
    public function findByPlatformId(PaymentGatewayEnum $gateway, $id)
    {
        return $this->createQueryBuilder('Agreement')
            ->addSelect('Subscription')
            ->join('Agreement.subscription', 'Subscription')
            ->where('Agreement.agreementId = :id AND Agreement.gateway = :gateway')
            ->setParameter('id', $id)
            ->setParameter('gateway', $gateway->getValue())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
