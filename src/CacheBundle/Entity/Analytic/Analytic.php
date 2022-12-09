<?php

namespace CacheBundle\Entity\Analytic;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use CacheBundle\Form\AnalyticType;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

/**
 * Analytic
 *
 * Holds all data necessary for viewing and computing analytics.
 *
 * @ORM\Table(name="analytics")
 * @ORM\Entity(
 *  repositoryClass="CacheBundle\Repository\AnalyticRepository"
 * )
 *
 * @see Analytic
 * @ORM\HasLifecycleCallbacks
 */
class Analytic implements
    ManageableEntityInterface,
    NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    private $name;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $owner;

    /**
     * @var AnalyticContext
     *
     * @ORM\ManyToOne(
     *     targetEntity="CacheBundle\Entity\Analytic\AnalyticContext",fetch="EAGER",
     *     cascade={ "persist" },
     *     inversedBy="analytics"
     * )
     * @ORM\JoinColumn(name="context_id",referencedColumnName="hash")
     */
    private $context;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;


    /**
     * Analytic constructor.
     *
     * @param User            $owner   Owner of this analytic.
     * @param AnalyticContext $context Used context.
     */
    public function __construct(User $owner, AnalyticContext $context)
    {
        $this->owner = $owner;
        $this->context = $context;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name A saved analytic name.
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner Saved analytic owner.
     *
     * @return $this
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return AnalyticContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param AnalyticContext $context Used context.
     *
     * @return $this
     */
    public function setContext(AnalyticContext $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createDate('createdAt', [ 'analytic' ]),
            PropertyMetadata::createDate('updatedAt', [ 'analytic' ]),
            PropertyMetadata::createEntity('context',AnalyticContext::class,['context']),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'analytic','context'];
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return AnalyticType::class;
    }
    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return AnalyticType::class;
    }
    /**
     * Check whether specified user owner.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function isOwnedBy(User $user)
    {
        return $user->getId() === $this->owner->getId();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PreUpdate()
     *
     * @return void
     * @throws \Exception
     */
    public function onUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt When this analytic is updated.
     *
     * @return $Analytic
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

}
