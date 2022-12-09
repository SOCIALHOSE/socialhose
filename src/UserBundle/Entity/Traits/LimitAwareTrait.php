<?php

namespace UserBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Enum\AppLimitEnum;

/**
 * Trait LimitAwareTrait
 *
 * Contains mapping for some application limits and setter and getter for it.
 *
 * @package UserBundle\Entity\Traits
 */
trait LimitAwareTrait
{

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $searchesPerDay = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $savedFeeds = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $masterAccounts = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $subscriberAccounts = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $alerts = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $newsletters = 0;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(value=0)
     */
    private $webFeeds = 0;

    /**
     * @return integer
     */
    public function getSearchesPerDay()
    {
        return $this->searchesPerDay;
    }

    /**
     * Set searchesPerDay
     *
     * @param integer $searchesPerDay Search per day limit.
     *
     * @return static
     */
    public function setSearchesPerDay($searchesPerDay)
    {
        $this->searchesPerDay = $searchesPerDay;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSavedFeeds()
    {
        return $this->savedFeeds;
    }

    /**
     * Set savedFeeds
     *
     * @param integer $savedFeeds Saved feed limit.
     *
     * @return static
     */
    public function setSavedFeeds($savedFeeds)
    {
        $this->savedFeeds = $savedFeeds;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebFeeds()
    {
        return $this->webFeeds;
    }

    /**
     * Set webFeeds
     *
     * @param integer $webFeeds Saved feed limit.
     *
     * @return static
     */
    public function setWebFeeds($webFeeds)
    {
        $this->webFeeds = $webFeeds;

        return $this;
    }

    /**
     * @return integer
     */
    public function getMasterAccounts()
    {
        return $this->masterAccounts;
    }

    /**
     * @param integer $masterAccounts Master accounts limit.
     *
     * @return static
     */
    public function setMasterAccounts($masterAccounts)
    {
        $this->masterAccounts = $masterAccounts;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSubscriberAccounts()
    {
        return $this->subscriberAccounts;
    }

    /**
     * @param integer $subscriberAccounts Subscriber account limit.
     *
     * @return static
     */
    public function setSubscriberAccounts($subscriberAccounts)
    {
        $this->subscriberAccounts = $subscriberAccounts;

        return $this;
    }

    /**
     * @return integer
     */
    public function getAlerts()
    {
        return $this->alerts;
    }

    /**
     * @param integer $alerts Alerts count.
     *
     * @return static
     */
    public function setAlerts($alerts)
    {
        $this->alerts = $alerts;

        return $this;
    }

    /**
     * @return integer
     */
    public function getNewsletters()
    {
        return $this->newsletters;
    }

    /**
     * @param integer $newsletters Newsletters count.
     *
     * @return static
     */
    public function setNewsletters($newsletters)
    {
        $this->newsletters = $newsletters;

        return $this;
    }

    /**
     * Get limit value for specified limit name.
     *
     * @param AppLimitEnum $appLimit Requested limit name.
     *
     * @return integer
     */
    public function getLimitValue(AppLimitEnum $appLimit)
    {
        return $this->{'get'. ucfirst($appLimit->getValue())}();
    }

    /**
     * Set limit value for specified limit name.
     *
     * @param AppLimitEnum $appLimit Changed limit name.
     * @param integer      $newValue New value for limit.
     *
     * @return $this
     */
    public function setLimitValue(AppLimitEnum $appLimit, $newValue)
    {
        $this->{'set'. ucfirst($appLimit->getValue())}($newValue);

        return $this;
    }
}
