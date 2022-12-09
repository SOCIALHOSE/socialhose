<?php

namespace CacheBundle\Entity;

use Common\Enum\CollectionTypeEnum;

/**
 * Interface DocumentCollectionInterface
 *
 * Used for classes which can be used for holding document collection.
 *
 * @package CacheBundle\Entity
 */
interface DocumentCollectionInterface
{

    /**
     * Set totalCount
     *
     * @param integer $totalCount Count of all available data.
     *
     * @return static
     */
    public function setTotalCount($totalCount);

    /**
     * Get totalCount
     *
     * @return integer
     */
    public function getTotalCount();

    /**
     * Create proper Page entity instance for binding document and current
     * collection.
     *
     * @param integer $number Page number.
     *
     * @return Page
     */
    public function createPage($number);

    /**
     * @return integer
     */
    public function getCollectionId();

    /**
     * @return CollectionTypeEnum
     */
    public function getCollectionType();
}
