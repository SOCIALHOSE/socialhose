<?php

namespace PaymentBundle\Entity;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use PaymentBundle\Enum\PaymentGatewayEnum;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class Agreement
 *
 * @package PaymentBundle\Entity
 *
 * @ORM\Table(name="billing_subscription_agreement")
 * @ORM\Entity(repositoryClass="PaymentBundle\Repository\AgreementRepository")
 */
class Agreement implements EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var PaymentGatewayEnum
     *
     * @ORM\Column(type="payment_gateway")
     */
    private $gateway;

    /**
     * @var AbstractSubscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Subscription\AbstractSubscription",
     *     cascade={ "remove", "persist" }
     * )
     */
    private $subscription;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $agreementId;

    /**
     * @return PaymentGatewayEnum
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param PaymentGatewayEnum $gateway A Used payment gateway.
     *
     * @return Agreement
     */
    public function setGateway(PaymentGatewayEnum $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * @return AbstractSubscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription entity instance.
     *
     * @return Agreement
     */
    public function setSubscription(AbstractSubscription $subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return string
     */
    public function getAgreementId()
    {
        return $this->agreementId;
    }

    /**
     * @param string $agreementId Gateway specific agreement id.
     *
     * @return Agreement
     */
    public function setAgreementId($agreementId)
    {
        $this->agreementId = $agreementId;

        return $this;
    }
}
