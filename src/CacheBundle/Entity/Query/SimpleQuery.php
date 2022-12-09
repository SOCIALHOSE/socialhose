<?php

namespace CacheBundle\Entity\Query;

use Doctrine\ORM\Mapping as ORM;

/**
 * SimpleQuery
 *
 * @ORM\Entity(
 *  repositoryClass="CacheBundle\Repository\SimpleQueryRepository"
 * )
 */
class SimpleQuery extends AbstractQuery
{

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $expirationDate;

    /**
     * Set expirationDate
     *
     * @param \DateTime|string $expirationDate May be a \DateTime instance or
     *                                         string for computing relative to
     *                                         query 'date' property.
     *
     * @return SimpleQuery
     */
    public function setExpirationDate($expirationDate)
    {
        if (is_string($expirationDate)) {
            $date = clone $this->date;
            $expirationDate = $date->modify($expirationDate);
        }

        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Check that this simple query is still fresh.
     *
     * @return boolean
     */
    public function isFresh()
    {
        return $this->expirationDate >= date_create();
    }
}
