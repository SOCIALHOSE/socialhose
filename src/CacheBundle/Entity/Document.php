<?php

namespace CacheBundle\Entity;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Document
 *
 * @ORM\Table(name="documents")
 * @ORM\Entity(repositoryClass="CacheBundle\Repository\DocumentRepository")
 */
class Document implements EntityInterface, NormalizableEntityInterface
{

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $platform;

    /**
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $data;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CacheBundle\Entity\Page", mappedBy="document")
     */
    protected $pages;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="CacheBundle\Entity\Comment", mappedBy="document")
     */
    protected $comments;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $commentsCount = 0;

    /**
     * Map Document entity fields to external document fields.
     *
     * Contains only fields which has different's.
     *
     * @var string[]
     */
    protected static $externalMap = [
        'mainLength' => 'main_length',
        'dateFound' => 'date_found',
        'sourceHashcode' => 'source_hashcode',
        'sourceLink' => 'source_link',
        'sourcePublisherType' => 'source_publisher_type',
        'sourcePublisherSubtype' => 'source_publisher_subtype',
        'sourceDateFound' => 'source_date_found',
        'sourceTitle' => 'source_title',
        'sourceDescription' => 'source_description',
        'sourceLocation' => 'source_location',
        'summaryText' => 'summary_text',
        'htmlLength' => 'html_length',
        'authorName' => 'author_name',
        'authorLink' => 'author_link',
        'authorGender' => 'author_gender',
        'imageSrc' => 'image_src',
        'country' => 'geo_country',
        'state' => 'geo_state',
        'city' => 'geo_city',
        'point' => 'geo_point',
        'duplicatesCount' => 'duplicates_count',
    ];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id New document id.
     *
     * @return Document
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param string $platform Platform name from which we get document.
     *
     * @return Document
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data A index document data.
     *
     * @return Document
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add page
     *
     * @param Page $page A Page entity instance.
     *
     * @return Document
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
        $page->setDocument($this);

        return $this;
    }

    /**
     * Remove page
     *
     * @param Page $page A Page entity instance.
     *
     * @return Document
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
        $page->setDocument(null);

        return $this;
    }

    /**
     * Get pages
     *
     * @return Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'document';
    }

    /**
     * Add comment
     *
     * @param Comment $comment A Comment entity instance.
     *
     * @return Document
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $comment->setDocument($this);

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment A Comment entity instance.
     *
     * @return Document
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
        $comment->setDocument(null);

        return $this;
    }

    /**
     * Set comments
     *
     * @param Comment[]|ArrayCollection $comments Array of Document entities.
     *
     * @return Document
     */
    public function setComments($comments)
    {
        if (is_array($comments)) {
            $comments = new ArrayCollection($comments);
        }

        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set commentsCount
     *
     * @param integer $count Comments count.
     *
     * @return Document
     */
    public function setCommentsCount($count)
    {
        $this->commentsCount = $count;

        return $this;
    }

    /**
     * Get commentsCount
     *
     * @return integer
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * Increment comments counts for this document
     *
     * @return Document
     */
    public function incCommentsCount()
    {
        $this->commentsCount++;

        return $this;
    }

    /**
     * Decrement comments counts for this document
     *
     * @return Document
     */
    public function decCommentsCount()
    {
        $this->commentsCount--;

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
            PropertyMetadata::createString('id', [ 'id' ]),
            PropertyMetadata::createString('title', [ 'document' ]),
            PropertyMetadata::createDate('dateFound', [ 'document' ]),
            PropertyMetadata::createDate('published', [ 'document' ]),
            PropertyMetadata::createString('permalink', [ 'document' ]),
            PropertyMetadata::createString('content', [ 'document' ]),
            PropertyMetadata::createString('language', [ 'document' ]),
            PropertyMetadata::createString('publisher', [ 'document' ]),
            PropertyMetadata::groupProperties('source', [
                PropertyMetadata::createString('title', [ 'document' ]),
                PropertyMetadata::createString('type', [ 'document' ]),
                PropertyMetadata::createString('link', [ 'document' ]),
                PropertyMetadata::createString('section', [ 'document' ]),
                PropertyMetadata::createString('country', [ 'document' ]),
                PropertyMetadata::createString('state', [ 'document' ]),
                PropertyMetadata::createString('city', [ 'document' ]),
            ], [ 'document' ]),
            PropertyMetadata::groupProperties('author', [
                PropertyMetadata::createString('name', [ 'document' ]),
                PropertyMetadata::createString('link', [ 'document' ]),
            ], [ 'document' ]),
            PropertyMetadata::createInteger('duplicates', [ 'document' ]),
            PropertyMetadata::createString('image', [ 'document' ])->setNullable(true),
            PropertyMetadata::createInteger('views', [ 'document' ]),
            PropertyMetadata::createString('sentiment', [ 'document' ]),
            PropertyMetadata::groupProperties('comments', [
                PropertyMetadata::createCollection('comments', Comment::class, [ 'document' ])
                    ->setName('data'),
                PropertyMetadata::createInteger('count', [ 'document' ]),
                PropertyMetadata::createInteger('limit', [ 'document' ]),
            ], [ 'document' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'document' ];
    }
}
