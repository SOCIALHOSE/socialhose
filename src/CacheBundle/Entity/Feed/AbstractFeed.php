<?php

namespace CacheBundle\Entity\Feed;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Entity\NormalizableEntityInterface;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Entity\Category;
use CacheBundle\Entity\Document;
use Common\Enum\CollectionTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * AbstractFeed
 *
 * @ORM\Table(name="feeds")
 * @ORM\Entity(
 *     repositoryClass="CacheBundle\Repository\CommonFeedRepository"
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "query"="QueryFeed",
 *  "clip"="ClipFeed",
 * })
 */
abstract class AbstractFeed implements
    EntityInterface,
    NormalizableEntityInterface,
    ManageableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank(groups={ "Feed_Create" })
     */
    protected $name;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *  targetEntity="UserBundle\Entity\User"
     * )
     */
    protected $user;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(
     *  targetEntity="CacheBundle\Entity\Category",
     *  inversedBy="feeds"
     * )
     *
     * @Assert\NotBlank(groups={ "Feed_Create" })
     */
    protected $category;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $exported = false;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CacheBundle\Entity\Document", cascade={ "persist", "remove" })
     * @ORM\JoinTable(name="deleted_documents")
     */
    protected $excludedDocuments;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->excludedDocuments = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name Feed name.
     *
     * @return AbstractFeed
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param User $user A User entity instance.
     *
     * @return static
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
     * Set category
     *
     * @param Category $category A Category entity instance.
     *
     * @return AbstractFeed
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return boolean
     */
    public function isExported()
    {
        return $this->exported;
    }

    /**
     * @return boolean
     *
     * @deprecated
     * @see AbstractFeed::isExported()
     */
    public function getExported()
    {
        return $this->exported;
    }

    /**
     * @param boolean $exported Is this feed exported or not.
     *
     * @return static
     */
    public function setExported($exported)
    {
        $this->exported = $exported;

        return $this;
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        // All derived feed's will be created in different ways.
        return '';
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return null;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        // For all feeds we shouldn't return specific types, only 'feed'.
        return 'feed';
    }

    /**
     * @param string $subType A feed subtype.
     *
     * @return AbstractFeed
     */
    public static function createBySubType($subType)
    {
        switch ($subType) {
            case ClipFeed::getSubType():
                return new ClipFeed();

            case QueryFeed::getSubType():
                return new QueryFeed();

            default:
                throw new \InvalidArgumentException('Unknown sub type.');
        }
    }

    /**
     * Get concrete feed type.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return string
     */
    public static function getSubType(AbstractFeed $feed = null)
    {
        return \app\op\camelCaseToUnderscore(\app\c\getShortName($feed ?: static::class));
    }

    /**
     * Get specific feed type.
     *
     * Used by frontend.
     *
     * @return string
     */
    abstract public function getSpecificType();

    /**
     * @return integer
     */
    abstract public function getCollectionId();

    /**
     * @return CollectionTypeEnum
     */
    abstract public function getCollectionType();

    /**
     * Add excludedDocument
     *
     * @param Document $excludedDocument Excluded Document entity instance.
     *
     * @return static
     */
    public function addExcludedDocument(Document $excludedDocument)
    {
        $this->excludedDocuments[] = $excludedDocument;

        return $this;
    }

    /**
     * Remove excludedDocument
     *
     * @param Document $excludedDocument Removed Document entity instance.
     *
     * @return static
     */
    public function removeExcludedDocument(Document $excludedDocument)
    {
        $this->excludedDocuments->removeElement($excludedDocument);

        return $this;
    }

    /**
     * Get excludedDocuments
     *
     * @return Collection
     */
    public function getExcludedDocuments()
    {
        return $this->excludedDocuments;
    }
}
