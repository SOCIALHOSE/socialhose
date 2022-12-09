<?php

namespace CacheBundle\Entity;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Form\CommentType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\User;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="CacheBundle\Repository\CommentRepository")
 */
class Comment implements EntityInterface, NormalizableEntityInterface, ManageableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="CacheBundle\Entity\Document", inversedBy="comments")
     */
    private $document;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $title = '';

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     * @Assert\Length(max=5000)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * Only last of specified number of comments is marked as 'new'.
     * We use this marker for simplify fetching comments while we get list of
     * document.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $new = true;

    /**
     * Comment constructor.
     *
     * @param User   $user    A comment author.
     * @param string $content Comment content.
     * @param string $title   Comment title.
     */
    public function __construct(User $user, $content, $title = '')
    {
        $this
            ->setAuthor($user)
            ->setContent($content)
            ->setTitle($title);

        $this->createdAt = new \DateTime();
    }

    /**
     * Set title
     *
     * @param string $title Comment title.
     *
     * @return Comment
     */
    public function setTitle($title)
    {
        $this->title = trim($title);

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content Comment content.
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = trim($content);

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt When comment was created.
     *
     * @return Comment
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
     * Set document
     *
     * @param Document $document A Document entity instance.
     *
     * @return Comment
     */
    public function setDocument(Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set author
     *
     * @param User $author A User entity instance.
     *
     * @return Comment
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set new
     *
     * @param boolean $new Flag, true if this comment is new.
     *
     * @return Comment
     */
    public function setNew($new = true)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Is new
     *
     * @return boolean
     */
    public function isNew()
    {
        return $this->new;
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
            PropertyMetadata::createString('title', [ 'comment' ]),
            PropertyMetadata::createString('content', [ 'comment' ]),
            PropertyMetadata::createEntity('author', User::class, [ 'comment' ]),
            PropertyMetadata::createDate('createdAt', [ 'comment' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'comment' ];
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return CommentType::class;
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return CommentType::class;
    }
}
