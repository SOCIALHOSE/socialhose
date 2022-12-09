<?php

namespace PaymentBundle\Agreement;

use Doctrine\ORM\EntityManagerInterface;
use PaymentBundle\Entity\Agreement;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Repository\AgreementRepository;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class ORMAgreementManager
 *
 * @package PaymentBundle\Agreement
 */
class ORMAgreementManager implements AgreementManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ORMAgreementManager constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription entity
     *                                           instance.
     *
     * @return void
     */
    public function removeAgreement(AbstractSubscription $subscription)
    {
        $this->em->getRepository(Agreement::class)
            ->createQueryBuilder('Agreement')
            ->delete()
            ->where('Agreement.subscription = :subscription')
            ->setParameter('subscription', $subscription->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription instance.
     *
     * @return string
     */
    public function getAgreementId(AbstractSubscription $subscription)
    {
        $id = $this->em->getRepository(Agreement::class)
            ->createQueryBuilder('Agreement')
            ->select('Agreement.agreementId')
            ->where('Agreement.subscription = :subscription AND Agreement.gateway = :gateway')
            ->setParameter('subscription', $subscription->getId())
            ->setParameter('gateway', $subscription->getGateway()->getValue())
            ->getQuery()
            ->getOneOrNullResult();

        if ($id === null) {
            return '';
        }

        return current($id);
    }

    /**
     * @param PaymentGatewayEnum $gateway     A used payment gateway.
     * @param string             $agreementId Gateway specific agreement id.
     *
     * @return AbstractSubscription|null
     */
    public function getSubscription(PaymentGatewayEnum $gateway, $agreementId)
    {
        /** @var AgreementRepository $repository */
        $repository = $this->em->getRepository(Agreement::class);

        $agreement = $repository->findByPlatformId($gateway, $agreementId);
        return $agreement === null ? null : $agreement->getSubscription();
    }

    /**
     * @param AbstractSubscription $subscription User for whom we should store
     *                                           agreement.
     * @param string               $agreementId  Gateway specific agreement id.
     *
     * @return void
     */
    public function storeAgreement(AbstractSubscription $subscription, $agreementId)
    {
        $agreement = Agreement::create()
            ->setGateway($subscription->getGateway())
            ->setSubscription($subscription)
            ->setAgreementId($agreementId);

        $this->em->persist($agreement);
        $this->em->flush();
    }
}
