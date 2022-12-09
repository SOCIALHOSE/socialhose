<?php

namespace ApiBundle\Entity;

use AppBundle\Entity\EntityInterface;

/**
 * Interface ManageableEntityInterface
 * Interface for entities which can be managed by api methods.
 *
 * @package ApiBundle\Entity
 */
interface ManageableEntityInterface extends EntityInterface
{

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass();

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass();
}
