<?php

namespace AppBundle\Entity;

/**
 * Interface EntityInterface
 * @package AppBundle\Entity
 */
interface EntityInterface
{

    /**
     * Get id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType();
}
