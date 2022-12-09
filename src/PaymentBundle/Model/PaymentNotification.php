<?php

namespace PaymentBundle\Model;

use PaymentBundle\Entity\Model\Money;
use PaymentBundle\Enum\PaymentStatusEnum;

/**
 * Class PaymentNotification
 *
 * @package PaymentBundle\Model
 */
class PaymentNotification
{

    /**
     * @var Money
     */
    private $amount;

    /**
     * @var PaymentStatusEnum
     */
    private $status;

    /**
     * Gateway specific agreement id.
     *
     * @var string
     */
    private $agreementId;

    /**
     * Gateway specific transaction id.
     *
     * @var string
     */
    private $transactionId;

    /**
     * @var \Exception|null
     */
    private $exception;

    /**
     * PaymentNotification constructor.
     *
     * @param Money             $amount        A how much we receive.
     * @param PaymentStatusEnum $status        A payment status.
     * @param string            $agreementId   A gateway specific agreement id.
     * @param string            $transactionId A gateway specific transaction id.
     * @param \Exception|null   $exception     Occurred exception.
     */
    public function __construct(
        Money $amount,
        PaymentStatusEnum $status,
        $agreementId,
        $transactionId,
        \Exception $exception = null
    ) {
        $this->amount = $amount;
        $this->status = $status;
        $this->agreementId = $agreementId;
        $this->transactionId = $transactionId;
        $this->exception = $exception;
    }

    /**
     * @param \Exception $exception Occurred exception.
     *
     * @return static
     */
    public static function createFailed(\Exception $exception)
    {
        return new static(new Money(0.0, 'USD'), PaymentStatusEnum::failed(), '', '', $exception);
    }

    /**
     * @return Money
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return PaymentStatusEnum
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getAgreementId()
    {
        return $this->agreementId;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
