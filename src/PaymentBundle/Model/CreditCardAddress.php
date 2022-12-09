<?php

namespace PaymentBundle\Model;

/**
 * Class CreditCardAddress
 *
 * @package PaymentBundle\Model
 */
class CreditCardAddress
{

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $street;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * Address constructor.
     *
     * @param string $country    Country.
     * @param string $city       City name.
     * @param string $street     Street address.
     * @param string $postalCode Postal code.
     */
    public function __construct($country, $city, $street, $postalCode)
    {
        $this->country = strtoupper($country);
        $this->city = $city;
        $this->street = $street;
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }
}
