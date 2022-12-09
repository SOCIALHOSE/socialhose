<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Trait OwnerAwareEntityTrait
 *
 * Contains mapping for entities which should have owner relation with some user.
 *
 * @package AppBundle\Entity
 */
trait OwnerAwareEntityTrait
{

    /**
     * The user who created this notification.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * Set owner
     *
     * @param User $owner The owner of this notification.
     *
     * @return static
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Checks that this entity is owned by specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function isOwnedBy(User $user)
    {
        return $this->owner->getId() === $user->getId();
    }
}
