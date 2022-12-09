<?php

namespace AppBundle\Entity;

/**
 * Trait AbstractEntity
 *
 * Base entity trait used for implementing methods from EntityInterface and some
 * standard mapping.
 *
 * @package AppBundle\Entity
 */
trait BaseEntityTrait
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Create entity instance for fluid interface access.
     *
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return \app\op\camelCaseToUnderscore(\app\c\getShortName(static::class));
    }
}
