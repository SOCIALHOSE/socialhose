<?php

namespace PaymentBundle\Model;

/**
 * Class CreditCard
 *
 * @package PaymentBundle\Model
 */
class CreditCard
{

    private static $schemes = [
        // American Express card numbers start with 34 or 37 and have 15 digits.
        'AMEX' => [
            '/^3[47][0-9]{13}$/',
        ],
        // Discover card numbers begin with 6011, 622126 through 622925, 644 through 649 or 65.
        // All have 16 digits.
        'DISCOVER' => [
            '/^6011[0-9]{12}$/',
            '/^64[4-9][0-9]{13}$/',
            '/^65[0-9]{14}$/',
            '/^622(12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|91[0-9]|92[0-5])[0-9]{10}$/',
        ],
        // Maestro international cards begin with 675900..675999 and have between 12 and 19 digits.
        // Maestro UK cards begin with either 500000..509999 or 560000..699999 and have between 12 and 19 digits.
        'MAESTRO' => [
            '/^(6759[0-9]{2})[0-9]{6,13}$/',
            '/^(50[0-9]{4})[0-9]{6,13}$/',
            '/^5[6-9][0-9]{10,17}$/',
            '/^6[0-9]{11,18}$/',
        ],
        // All MasterCard numbers start with the numbers 51 through 55. All have 16 digits.
        // October 2016 MasterCard numbers can also start with 222100 through 272099.
        'MASTERCARD' => [
            '/^5[1-5][0-9]{14}$/',
            '/^2(22[1-9][0-9]{12}|2[3-9][0-9]{13}|[3-6][0-9]{14}|7[0-1][0-9]{13}|720[0-9]{12})$/',
        ],
        // All Visa card numbers start with a 4. New cards have 16 digits. Old cards have 13.
        'VISA' => [
            '/^4([0-9]{12}|[0-9]{15})$/',
        ],
    ];

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $number;

    /**
     * @var integer
     */
    private $cvv;

    /**
     * @var \DateTime
     */
    private $expiresAt;

    /**
     * @var CreditCardAddress
     */
    private $address;

    /**
     * @var string
     */
    private $schema;

    /**
     * CreditCard constructor.
     *
     * @param string            $firstName Cardholder first name.
     * @param string            $lastName  Cardholder last name.
     * @param string            $number    Credit card number.
     * @param integer           $cvv       CVV code.
     * @param \DateTime         $expiresAt When card should expires.
     * @param CreditCardAddress $address   A address instance.
     */
    public function __construct(
        $firstName,
        $lastName,
        $number,
        $cvv,
        \DateTime $expiresAt,
        CreditCardAddress $address
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->number = $number;
        $this->cvv = $cvv;
        $this->expiresAt = $expiresAt;
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @return integer
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        if ($this->schema === null) {
            $this->schema = '';

            foreach (self::$schemes as $schema => $regexes) {
                foreach ($regexes as $regex) {
                    if (preg_match($regex, $this->number)) {
                        $this->schema = $schema;
                        break 2;
                    }
                }
            }
        }

        return $this->schema;
    }

    /**
     * @return CreditCardAddress
     */
    public function getAddress()
    {
        return $this->address;
    }
}
