<?php

namespace CacheBundle\Entity;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Entity\Query\AbstractQuery;
use Common\Enum\CollectionTypeEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Page
 *
 * @ORM\Table(name="pages")
 * @ORM\Entity
 */
class Page implements EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var AbstractQuery
     *
     * @ORM\ManyToOne(
     *  targetEntity="CacheBundle\Entity\Query\AbstractQuery",
     *  inversedBy="pages"
     * )
     */
    protected $query;

    /**
     * @var ClipFeed
     *
     * @ORM\ManyToOne(
     *     targetEntity="CacheBundle\Entity\Feed\ClipFeed",
     *     inversedBy="pages"
     * )
     */
    protected $clipFeed;

    /**
     * Page number, starts from 1.
     *
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $number;

    /**
     * @var Document
     *
     * @ORM\ManyToOne(
     *  targetEntity="CacheBundle\Entity\Document",
     *  inversedBy="pages"
     * )
     */
    protected $document;

    /**
     * Set number
     *
     * @param integer $number A page number.
     *
     * @return Page
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set query
     *
     * @param AbstractQuery $query A AbstractQuery entity instance.
     *
     * @return Page
     */
    public function setQuery(AbstractQuery $query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return AbstractQuery
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set document
     *
     * @param Document $document A Document entity instance.
     *
     * @return Page
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
     * Set clipFeed
     *
     * @param ClipFeed $clipFeed A ClipFeed entity instance.
     *
     * @return Page
     */
    public function setClipFeed(ClipFeed $clipFeed = null)
    {
        $this->clipFeed = $clipFeed;

        return $this;
    }

    /**
     * Get clipFeed
     *
     * @return ClipFeed
     */
    public function getClipFeed()
    {
        return $this->clipFeed;
    }

    /**
     * Get associated document collection type.
     *
     * @return string
     */
    public function getCollectionType()
    {
        return $this->query ? CollectionTypeEnum::QUERY : CollectionTypeEnum::FEED;
    }

    /**
     * Get associated document collection entity id.
     *
     * @return string
     */
    public function getCollectionId()
    {
        return \app\op\invokeIf($this->query, 'getId') ?: $this->clipFeed->getId();
    }
}
