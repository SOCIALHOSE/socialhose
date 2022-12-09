<?php

namespace CacheBundle\Entity;

/**
 * Trait DocumentCollectionInterface
 * @package CacheBundle\Entity
 */
trait DocumentCollectionTrait
{

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $totalCount = 0;

    /**
     * Set totalCount
     *
     * @param integer $totalCount Count of all available data.
     *
     * @return static
     */
    public function setTotalCount($totalCount)
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * Get totalCount
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
}
