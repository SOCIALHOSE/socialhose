<?php

namespace PaymentBundle\Entity;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use PaymentBundle\Entity\Model\Money;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Enum\PaymentStatusEnum;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class Payment
 *
 * @package PaymentBundle\Entity
 *
 * @ORM\Table(name="payments")
 * @ORM\Entity(repositoryClass="PaymentBundle\Repository\PaymentRepository")
 */
class Payment implements EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var AbstractSubscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Subscription\AbstractSubscription",
     *     cascade={ "remove", "persist" },
     *     inversedBy="payments"
     * )
     */
    private $subscription;

    /**
     * @var PaymentGatewayEnum
     *
     * @ORM\Column(type="payment_gateway")
     */
    private $gateway;

    /**
     * Gateway specific transaction id.
     *
     * @var string
     *
     * @ORM\Column
     */
    private $transactionId;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="PaymentBundle\Entity\Model\Money", columnPrefix="amount_")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var PaymentStatusEnum
     *
     * @ORM\Column(type="payment_status")
     */
    private $status;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return AbstractSubscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @param AbstractSubscription $subscription A AbstractSubscription instance.
     *
     * @return Payment
     */
    public function setSubscription(AbstractSubscription $subscription = null)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * @return PaymentGatewayEnum
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param PaymentGatewayEnum $gateway A PaymentGatewayEnum instance.
     *
     * @return Payment
     */
    public function setGateway(PaymentGatewayEnum $gateway)
    {
        $this->gateway = $gateway;

        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId Payment gateway specific transaction id.
     *
     * @return Payment
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param Money $amount A Money instance.
     *
     * @return Payment
     */
    public function setAmount(Money $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt When payment was created.
     *
     * @return Payment
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return PaymentStatusEnum
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param PaymentStatusEnum $status A PaymentStatusEnum instance.
     *
     * @return Payment
     */
    public function setStatus(PaymentStatusEnum $status)
    {
        $this->status = $status;

        return $this;
    }
}
