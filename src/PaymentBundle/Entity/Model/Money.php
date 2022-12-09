<?php

namespace PaymentBundle\Entity\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Intl\Intl;

/**
 * Class Money
 *
 * Value object for money.
 *
 * @package PaymentBundle\Entity\Model
 *
 * @ORM\Embeddable
 */
final class Money
{

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(length=4)
     */
    private $currency;

    /**
     * Money constructor.
     *
     * @param float  $amount   Money amount.
     * @param string $currency Money currency.
     *
     */
    public function __construct($amount, $currency)
    {
        $currency = strtoupper($currency);

        $currencies = array_keys(Intl::getCurrencyBundle()->getCurrencyNames());
        if (! in_array($currency, $currencies, true)) {
            throw new \InvalidArgumentException('Unknown currency: \''. $currency .'\'');
        }

        $this->amount = round($amount, 2);
        $this->currency = $currency;
    }

    /**
     * Get money amount.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get money currency.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
