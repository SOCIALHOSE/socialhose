<?php

namespace CacheBundle\Entity;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use UserBundle\Entity\User;

/**
 * Source
 *
 * @ORM\Table(name="source_list")
 * @ORM\Entity(
 *  repositoryClass="CacheBundle\Repository\SourceListRepository"
 * )
 * @UniqueEntity({"name", "user"})
 * @ORM\HasLifecycleCallbacks
 */
class SourceList implements NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $isGlobal = false;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    protected $updatedBy;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="sourcesLists")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="CacheBundle\Entity\SourceToSourceList",
     *     mappedBy="list",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $sources;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $sourceNumber = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return SourceList
     */
    public function cloneList()
    {
        $clone = clone $this;

        $clone
            ->setSourceNumber(0)
            ->setUpdatedAt(null)
            ->setUpdatedBy(null);
        $clone->sources = new ArrayCollection();

        return $clone;
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
            PropertyMetadata::createInteger('sourceNumber', [ 'source_list' ]),
            PropertyMetadata::createBoolean('shared', [ 'source_list' ])
                ->setField('isGlobal'),
            PropertyMetadata::createString('name', [ 'source_list' ]),
            PropertyMetadata::createEntity('user', User::class, [ 'source_list' ]),
            PropertyMetadata::createDate('createdAt', [ 'source_list' ]),
            PropertyMetadata::createDate('updatedAt', [ 'source_list' ]),
            PropertyMetadata::createEntity('updatedBy', User::class, [ 'source_list' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'source_list', 'id' ];
    }

    /**
     * @ORM\PreUpdate()
     *
     * @return void
     */
    public function onUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set title
     *
     * @param string $name Source list name.
     *
     * @return SourceList
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt When this list is created.
     *
     * @return SourceList
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt When this list is updated.
     *
     * @return SourceList
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

    /**
     * Set updatedBy
     *
     * @param User $user A User entity instance.
     *
     * @return SourceList
     */
    public function setUpdatedBy(User $user = null)
    {
        $this->updatedBy = $user;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set user
     *
     * @param User $user A owner User entity instance.
     *
     * @return SourceList
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set isGlobal
     *
     * @param boolean $isGlobal Is global source list or not.
     *
     * @return SourceList
     */
    public function setIsGlobal($isGlobal)
    {
        $this->isGlobal = $isGlobal;

        return $this;
    }

    /**
     * Get isGlobal
     *
     * @return boolean
     */
    public function getIsGlobal()
    {
        return $this->isGlobal;
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
        return $user->getId() === $this->user->getId();
    }

    /**
     * Set sourceNumber
     *
     * @param integer $sourceNumber Sources count.
     *
     * @return SourceList
     */
    public function setSourceNumber($sourceNumber)
    {
        $this->sourceNumber = $sourceNumber;

        return $this;
    }

    /**
     * Get sourceNumber
     *
     * @return integer
     */
    public function getSourceNumber()
    {
        return $this->sourceNumber;
    }

    /**
     * Add source
     *
     * @param SourceToSourceList $source A SourceToSourceList entity instance.
     *
     * @return SourceList
     */
    public function addSource(SourceToSourceList $source)
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param SourceToSourceList $source A SourceToSourceList entity instance.
     *
     * @return SourceList
     */
    public function removeSource(SourceToSourceList $source)
    {
        $this->sources->removeElement($source);

        return $this;
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSources()
    {
        return $this->sources;
    }
}
