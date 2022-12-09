<?php

namespace IndexBundle\SearchRequest;

use AppBundle\Response\SearchResponseInterface;
use IndexBundle\Aggregation\AggregationInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Normalizer\Query\QueryNormalizerInterface;
use UserBundle\Entity\User;

/**
 * Class SearchRequest
 *
 * @package IndexBundle\SearchRequest
 */
class SearchRequest implements SearchRequestInterface
{

    /**
     * @var QueryNormalizerInterface
     */
    protected $normalizer;

    /**
     * @var SearchRequestBuilderInterface
     */
    protected $builder;

    /**
     * @var array
     */
    protected $normalizedFilters;

    /**
     * Normalized search query.
     *
     * @var string
     */
    protected $normalizedQuery;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var SearchResponseInterface|null
     *
     * @see SearchRequest::execute()
     */
    private $response;

    /**
     * @var integer|null
     *
     * @see SearchRequest::count()
     */
    private $count;

    /**
     * @var array[]|null
     *
     * @see SearchRequest::getAvailableAdvancedFilters()
     */
    private $aggregationRatings;

    /**
     * @param QueryNormalizerInterface      $normalizer A
     *                                                  QueryNormalizerInterface
     *                                                  instance.
     * @param SearchRequestBuilderInterface $builder    A
     *                                                  SearchRequestBuilderInterface
     *                                                  instance.
     */
    public function __construct(
        QueryNormalizerInterface $normalizer,
        SearchRequestBuilderInterface $builder
    ) {
        $this->normalizer = $normalizer;
        $this->builder = $builder;
    }

    /**
     * @return IndexInterface
     */
    public function getIndex()
    {
        return $this->builder->getIndex();
    }

    /**
     * Return filters.
     *
     * @return \IndexBundle\Filter\FilterInterface[]
     */
    public function getFilters()
    {
        return $this->builder->getFilters();
    }

    /**
     * Get fetched source fields names.
     *
     * @return string[]
     */
    public function getSources()
    {
        return $this->builder->getSources();
    }

    /**
     * Return aggregation
     *
     * @return AggregationInterface[]
     */
    public function getAggregation()
    {
        return $this->builder->getAggregation();
    }

    /**
     * Get user who made this search request.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->builder->getUser();
    }

    /**
     * Get fields names.
     *
     * @return string[]
     */
    public function getFields()
    {
        return $this->builder->getFields();
    }

    /**
     * Get sorting fields.
     *
     * @return array Where key is field name and value is sorting direction.
     */
    public function getSorts()
    {
        return $this->builder->getSorts();
    }

    /**
     * Get raw search query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->builder->getQuery();
    }

    /**
     * Get normalized query.
     *
     * @return string
     */
    public function getNormalizedQuery()
    {
        if ($this->normalizedQuery === null) {
            $this->normalizedQuery = $this->normalizer
                ->normalize($this->getQuery());
        }

        return $this->normalizedQuery;
    }

    /**
     * Set requested page number.
     *
     * @param integer $page Page number, start from 1.
     *
     * @return SearchRequest
     */
    public function setPage($page)
    {
        //
        // Because of page changing affects on set of documents that we get from
        // index we should remove cached response. But it not affects on total
        // count and advanced filters.
        //
        $this->response = null;
        $this->builder->setPage($page);

        return $this;
    }

    /**
     * Get requested page number.
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->builder->getPage();
    }

    /**
     * Set limit of documents per page.
     *
     * @param integer $limit Max document per page.
     *
     * @return SearchRequest
     */
    public function setLimit($limit)
    {
        //
        // Because of page changing affects on set of documents that we get from
        // index we should remove cached response. But it not affects on total
        // count and advanced filters.
        //
        $this->response = null;
        $this->builder->setLimit($limit);

        return $this;
    }

    /**
     * Get limit of documents per page.
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->builder->getLimit();
    }

    /**
     * Compute this response hash.
     *
     * @return string
     */
    public function getHash()
    {
        if (! $this->hash) {
            $this->hash = md5(
                $this->getNormalizedQuery()
                . serialize($this->getFields())
                . serialize($this->getFilters())
            );
        }

        return $this->hash;
    }

    /**
     * Execute this search request and get response from server.
     *
     * @return SearchResponseInterface
     */
    public function execute()
    {
        if ($this->response === null) {
            $this->response = $this->getIndex()->search($this);
            //
            // Also in response we get total count, so we may use this value and
            // reduce request count.
            //
            $this->count = $this->response->getTotalCount();
        }

        return $this->response;
    }

    /**
     * Get available advanced filters for this request.
     *
     * @return array
     */
    public function getAvailableAdvancedFilters()
    {
        if ($this->aggregationRatings === null) {
            $this->aggregationRatings = $this->getIndex()
                ->getAFResolver()
                ->getAvailables($this);
        }

        return $this->aggregationRatings;
    }
}
