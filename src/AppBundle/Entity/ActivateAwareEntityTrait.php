<?php

namespace AppBundle\Entity;

/**
 * Class ActivateAwareEntityTrait
 * @package AppBundle\Entity
 */
trait ActivateAwareEntityTrait
{

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $active = true;

    /**
     * Set active
     *
     * @param boolean $active Flag, notification will be render if set.
     *
     * @return static
     */
    public function setActive($active = true)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
}
