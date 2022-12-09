<?php

namespace CacheBundle\Entity\Query;

use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use CacheBundle\Entity\DocumentCollectionInterface;
use CacheBundle\Entity\DocumentCollectionTrait;
use CacheBundle\Entity\Page;
use Common\Enum\CollectionTypeEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * AbstractQuery
 *
 * @ORM\Table(
 *  name="queries",
 *  indexes={
 *      @ORM\Index(name="hash_idx", columns={ "hash" })
 *  }
 * )
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "simple"="SimpleQuery",
 *  "stored"="StoredQuery"
 * })
 */
abstract class AbstractQuery implements EntityInterface, DocumentCollectionInterface
{

    use
        BaseEntityTrait,
        DocumentCollectionTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $raw;

    /**
     * Filters in the form in which they came to us from the client.
     *
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $rawFilters = [];

    /**
     * Advanced filters in the form in which they came to us from the client.
     *
     * @var array
     *
     * @ORM\Column(type="json_array")
     */
    protected $rawAdvancedFilters = [];

    /**
     * @var string[]
     *
     * @ORM\Column(type="array")
     */
    protected $fields = [];

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $normalized;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $hash;

    /**
     * Array of normalized actual used filters.
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $filters = [];

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="CacheBundle\Entity\Page", mappedBy="query")
     */
    protected $pages;

    /**
     * @var string
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->date = new \DateTime();
    }

    /**
     * Set raw
     *
     * @param string $raw Raw query string typed by user.
     *
     * @return static
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;

        return $this;
    }

    /**
     * Get raw
     *
     * @return string
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * Set rawFilters
     *
     * @param array $rawFilters Raw filters.
     *
     * @return static
     */
    public function setRawFilters(array $rawFilters)
    {
        $this->rawFilters = $rawFilters;

        return $this;
    }

    /**
     * Get rawFilters
     *
     * @return array
     */
    public function getRawFilters()
    {
        return $this->rawFilters;
    }

    /**
     * Set rawAdvancedFilters
     *
     * @param array $rawAdvancedFilters Raw filters.
     *
     * @return static
     */
    public function setRawAdvancedFilters(array $rawAdvancedFilters)
    {
        $this->rawAdvancedFilters = $rawAdvancedFilters;

        return $this;
    }

    /**
     * Get rawAdvancedFilters
     *
     * @return array
     */
    public function getRawAdvancedFilters()
    {
        return $this->rawAdvancedFilters;
    }

    /**
     * Set fields
     *
     * @param array $fields Array of field involved in search.
     *
     * @return static
     */
    public function setFields(array $fields = [])
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set normalized
     *
     * @param string $normalized Normalized query string.
     *
     * @return static
     */
    public function setNormalized($normalized)
    {
        $this->normalized = $normalized;

        return $this;
    }

    /**
     * Get normalized
     *
     * @return string
     */
    public function getNormalized()
    {
        return $this->normalized;
    }

    /**
     * Set hash
     *
     * @param string $hash Query hash.
     *
     * @return static
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set date
     *
     * @param \DateTime $date When query was requested.
     *
     * @return static
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add page
     *
     * @param Page $page A Page entity instance.
     *
     * @return static
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
        $page->setQuery($this);

        return $this;
    }

    /**
     * Remove page
     *
     * @param Page $page A Page entity instance.
     *
     * @return static
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
        $page->setQuery(null);

        return $this;
    }

    /**
     * Get pages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Set filters
     *
     * @param array $filters Array of filters.
     *
     * @return static
     */
    public function setFilters(array $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Create query entity instance from search request instance.
     *
     * @param SearchRequestInterface $searchRequest A SearchRequestInterface
     *                                              instance.
     *
     * @return static
     */
    public static function fromSearchRequest(SearchRequestInterface $searchRequest)
    {
        $instance = new static();

        return $instance
            ->setFilters($searchRequest->getFilters())
            ->setFields($searchRequest->getFields())
            ->setNormalized($searchRequest->getNormalizedQuery())
            ->setRaw($searchRequest->getQuery())
            ->setHash($searchRequest->getHash());
    }

    /**
     * Create proper Page entity instance for binding document and current
     * collection.
     *
     * @param integer $number Page number.
     *
     * @return Page
     */
    public function createPage($number)
    {
        return Page::create()
            ->setQuery($this)
            ->setNumber($number);
    }

    /**
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->id;
    }

    /**
     * @return CollectionTypeEnum
     */
    public function getCollectionType()
    {
        return CollectionTypeEnum::query();
    }
}
