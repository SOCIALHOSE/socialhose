<?php

namespace CacheBundle\Entity;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use CacheBundle\Form\CategoryType;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Validator\Constraints\CategoryParent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Entity\User;

/**
 * Category
 *
 * @ORM\Table(name="categories")
 * @ORM\Entity(
 *  repositoryClass="CacheBundle\Repository\CategoryRepository"
 * )
 *
 * @Assert\GroupSequence({ "Category", "parent" , "unique" })
 */
class Category implements
    ManageableEntityInterface,
    NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * My category.
     */
    const TYPE_MY_CONTENT = 'my_content';

    /**
     * My category name.
     */
    const NAME_MY_CONTENT = 'My Content';

    /**
     * Category for deleted feeds.
     */
    const TYPE_DELETED_CONTENT = 'deleted_content';

    /**
     * Name of category for deleted feeds.
     */
    const NAME_DELETED_CONTENT = 'Deleted Content';

    /**
     * Category for shared feeds.
     */
    const TYPE_SHARED_CONTENT = 'shared_content';

    /**
     * Name of category for shared feeds.
     */
    const NAME_SHARED_CONTENT = 'Shared Content';

    /**
     * User custom directories.
     */
    const TYPE_CUSTOM = 'custom';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *  targetEntity="CacheBundle\Entity\Feed\AbstractFeed",
     *  mappedBy="category",
     *  cascade={ "persist", "remove" }
     * )
     */
    protected $feeds;

    /**
     * @var User
     *
     * @ORM\ManyToOne(
     *  targetEntity="UserBundle\Entity\User",
     *  inversedBy="categories"
     * )
     */
    protected $user;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $type = self::TYPE_CUSTOM;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="CacheBundle\Entity\Category",
     *     mappedBy="parent",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $childes;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(
     *     targetEntity="CacheBundle\Entity\Category",
     *     inversedBy="childes"
     * )
     * @CategoryParent(groups={ "parent" })
     */
    protected $parent;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $exported = false;

    /**
     * @param User   $user A User entity instance, who create this category.
     * @param string $name Category name.
     */
    public function __construct(User $user, $name = '')
    {
        $user->addCategory($this);

        $this->name = $name;
        $this->feeds = new ArrayCollection();
        $this->childes = new ArrayCollection();
    }

    /**
     * @param Category $parent A parent Category entity instance.
     * @param User     $user   A User entity instance, who create this category.
     * @param string   $name   Category name.
     *
     * @return Category
     */
    public static function createChild(Category $parent, User $user, $name)
    {
        $category = new Category($user, $name);
        $parent->addChild($category);

        return $category;
    }

    /**
     * Create main category for specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return Category
     */
    public static function createMainCategory(User $user)
    {
        $category = new Category($user, self::NAME_MY_CONTENT);

        return $category->setType(self::TYPE_MY_CONTENT);
    }

    /**
     * Create main category for specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return Category
     */
    public static function createSharedCategory(User $user)
    {
        $category = new Category($user, self::NAME_SHARED_CONTENT);

        return $category->setType(self::TYPE_SHARED_CONTENT);
    }

    /**
     * Create trash category for specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return Category
     */
    public static function createTrashCategory(User $user)
    {
        $category = new Category($user, self::NAME_DELETED_CONTENT);

        return $category->setType(self::TYPE_DELETED_CONTENT);
    }

    /**
     * Add query
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return Category
     */
    public function addFeed(AbstractFeed $feed)
    {
        $this->feeds[] = $feed;
        $feed->setCategory($this);

        return $this;
    }

    /**
     * Remove query
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return Category
     */
    public function removeFeed(AbstractFeed $feed)
    {
        $this->feeds->removeElement($feed);
        $feed->setCategory(null);

        return $this;
    }

    /**
     * Get queries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFeeds()
    {
        return $this->feeds;
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
     * Set name
     *
     * @param string $name Category name.
     *
     * @return Category
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
     * Set type
     *
     * @param string $type Category type.
     *
     * @return Category
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return true if current category is my content, deleted content or shared
     * content.
     *
     * @return boolean
     */
    public function isInternal()
    {
        return $this->type !== self::TYPE_CUSTOM;
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return CategoryType::class;
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return CategoryType::class;
    }

    /**
     * Add child
     *
     * @param Category $child A child Category entity instance.
     *
     * @return Category
     */
    public function addChild(Category $child)
    {
        if ($child === $this) {
            $message = 'Try to put category inside itself.';
            throw new \InvalidArgumentException($message);
        }

        $this->childes[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * Remove child
     *
     * @param Category $child A child Category entity instance.
     *
     * @return Category
     */
    public function removeChild(Category $child)
    {
        $this->childes->removeElement($child);
        $child->setParent(null);

        return $this;
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection|array
     */
    public function getChildes()
    {
        return $this->childes;
    }

    /**
     * Set parent
     *
     * @param Category $parent A parent Category entity instance.
     *
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return boolean
     */
    public function isExported()
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
     * Return metadata for current entity.
     *
     * @return Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createString('name', [ 'category', 'category_tree' ]),
            PropertyMetadata::createString('subType', [ 'category', 'category_tree' ])
                ->setField('type'),
            PropertyMetadata::createCollection('childes', Category::class, [
                'category',
                'category_tree',
            ]),
            PropertyMetadata::createBoolean('exported', [ 'category', 'category_tree' ]),
            PropertyMetadata::createCollection('feeds', AbstractFeed::class, [
                'feed_tree',
            ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'feed_tree', 'category', 'id' ];
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'directory';
    }

    /**
     * @Assert\Callback(groups={ "unique" })
     *
     * @param ExecutionContextInterface $context A ExecutionContextInterface instance.
     */
    public function validateUnique(ExecutionContextInterface $context)
    {
        $categoriesWithSameName = $this->getParent()->getChildes()->filter(function (Category $category) {
            return $category->getName() === $this->getName();
        });

        if (count($categoriesWithSameName) > 0) {
            $context->buildViolation('Category with name \'{{ value }}\' is already exists')
                ->setParameter('{{ value }}', $this->getName())
                ->addViolation();
        }
    }
}
