<?php

namespace IndexBundle\SearchRequest;

use CacheBundle\Entity\Query\AbstractQuery;
use IndexBundle\Aggregation\AggregationInterface;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\Filter\FilterInterface;
use IndexBundle\Filter\GroupFilterInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Normalizer\Query\QueryNormalizerInterface;
use UserBundle\Entity\User;

/**
 * Class SearchRequestBuilder
 * @package IndexBundle\SearchRequest
 */
class SearchRequestBuilder implements SearchRequestBuilderInterface
{

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @var AggregationInterface[]
     */
    private $aggregation = [];

    /**
     * Who make this search request.
     *
     * @var User|null
     */
    private $user;

    /**
     * Raw search query typed by user.
     *
     * @var string
     */
    private $query = '';

    /**
     * Fields names.
     *
     * @var string[]
     */
    private $fields = [];

    /**
     * Fetched source fields.
     *
     * @var string[]
     */
    private $sources = [];

    /**
     * Requested page number.
     *
     * @var integer
     */
    private $page = 1;

    /**
     * Max document per page.
     *
     * @var integer
     */
    private $limit = IndexInterface::MAX_RESULT_COUNT;

    /**
     * Sorted field name.
     * This value is assoc array where key is field name and value us sort order.
     *
     * @var string[]
     */
    private $sortFields = [];

    /**
     * @var IndexInterface
     */
    private $index;

    /**
     * @var QueryNormalizerInterface
     */
    private $normalizer;

    /**
     * @param IndexInterface           $index      A IndexInterface
     *                                             or InternalIndexInterface
     *                                             Instance.
     * @param QueryNormalizerInterface $normalizer A QueryNormalizerInterface
     *                                             instance.
     */
    public function __construct(
        IndexInterface $index,
        QueryNormalizerInterface $normalizer
    ) {
        $this->index = $index;
        $this->normalizer = $normalizer;
    }

    /**
     * @param IndexInterface           $index      A IndexInterface
     *                                             or InternalIndexInterface
     *                                             Instance.
     * @param QueryNormalizerInterface $normalizer A QueryNormalizerInterface
     *                                             instance.
     *
     * @return SearchRequestBuilder
     */
    public static function create(
        IndexInterface $index,
        QueryNormalizerInterface $normalizer
    ) {
        // @codingStandardsIgnoreStart
        return new self($index, $normalizer);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get filter factory for this search request builder.
     *
     * @return FilterFactoryInterface
     */
    public function getFilterFactory()
    {
        return $this->getIndex()->getFilterFactory();
    }

    /**
     * Set filters, override already exists.
     *
     * @param FilterInterface|FilterInterface[] $filters A FilterInterface
     *                                                   instance or array of
     *                                                   FilterInterface's
     *                                                   instances.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setFilters($filters)
    {
        if ($filters instanceof FilterInterface) {
            $filters = [ $filters ];
        }
        $this->filters = array_filter($filters);

        return $this;
    }

    /**
     * Add new filter.
     *
     * @param FilterInterface $filter A FilterInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Get all filters.
     *
     * @return FilterInterface[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set aggregation
     *
     * @param AggregationInterface|AggregationInterface[] $aggregation A
     *                                                                 AggregationInterface
     *                                                                 instance
     *                                                                 or array
     *                                                                 of instances.
     *
     * @return $this
     */
    public function setAggregation($aggregation)
    {
        if (! is_array($aggregation)) {
            $aggregation = [ $aggregation ];
        }
        $this->aggregation = $aggregation;

        return $this;
    }

    /**
     * Get aggregations.
     *
     * @return AggregationInterface[]
     */
    public function getAggregation()
    {
        return $this->aggregation;
    }

    /**
     * Set user.
     *
     * @param User $user A user who made search request.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set raw search query.
     *
     * @param string $query Raw search query.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get raw search query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set fields, override already exists.
     *
     * @param array<string> $fields Fields names.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Add new field name.
     *
     * @param string $field Field name.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addField($field)
    {
        $this->fields[] = $field;

        return $this;
    }

    /**
     * Set fetched source fields names.
     *
     * @param string[]|array $sources Array of fetched source fields. Fetch all if
     *                                empty.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setSources(array $sources)
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Get fetched source fields names.
     *
     * @return string[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set sort field name.
     *
     * @param string $fieldName Field name.
     * @param string $direction Sorting direction, must be 'asc' or 'desc'.
     *
     * @return SearchRequestBuilderInterface
     */
    public function addSort($fieldName, $direction = 'asc')
    {
        $this->sortFields[$fieldName] = $direction;

        return $this;
    }

    /**
     * Set new sorting fields.
     *
     * @param array $sortFields Assoc array where key is field name and value is
     *                          sorting direction.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setSorts(array $sortFields)
    {
        $this->sortFields = $sortFields;

        return $this;
    }

    /**
     * Get sorting fields.
     *
     * @return array Where key is field name and value is sorting direction.
     */
    public function getSorts()
    {
        return $this->sortFields;
    }

    /**
     * Get fields names.
     *
     * @return array<string>
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set request page number.
     *
     * @param integer $page Page number, start from 1.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setPage($page)
    {
        $this->page = (int) $page;

        if ($this->page < 1) {
            $message = 'Invalid page parameter, must be greater or equal to 1';
            throw new \InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * Get requested page number.
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set limit.
     *
     * @param integer $limit Max documents per page.
     *
     * @return SearchRequestBuilderInterface
     */
    public function setLimit($limit)
    {
        if ($limit !== null) {
            //
            // We should make type cast and check only if we got some value.
            // This condition is necessary for avoiding problems when we create
            // one request builder from another and then we got $limit === 0 instead
            // of null which leads to the fact that we get 0 results when we want
            // to get all.
            //
            $limit = (int) $limit;

            if ($limit < 0) {
                $message = 'Invalid limit parameter, must be greater or equal to 0';
                throw new \InvalidArgumentException($message);
            }
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Get max documents per page.
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Initialize builder parameters from specified search request.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromSearchRequest(SearchRequestInterface $request)
    {
        return $this
            ->setQuery($request->getQuery())
            ->setFields($request->getFields())
            ->setFilters($request->getFilters())
            ->setAggregation($request->getAggregation())
            ->setUser($request->getUser())
            ->setSorts($request->getSorts())
            ->setLimit($request->getLimit())
            ->setPage($request->getPage());
    }

    /**
     * Initialize builder parameters from specified search request.
     *
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromSearchRequestBuilder(SearchRequestBuilderInterface $builder)
    {
        return $this->fromSearchRequest($builder->build());
    }

    /**
     * Initialize builder parameters from specified query entity.
     *
     * @param AbstractQuery $query A AbstractQuery entity instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function fromQueryEntity(AbstractQuery $query)
    {
        return $this
            ->setQuery($query->getRaw())
            ->setFields($query->getFields())
            ->setFilters($query->getFilters());
    }

    /**
     * @return IndexInterface
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Build search request.
     *
     * @return SearchRequest
     */
    public function build()
    {
        //
        // Remove duplicate fields and sort they.
        //
        $fields = array_unique($this->getFields());
        sort($fields);
        $this
            ->setFilters($this->normalizeFilters($this->getFilters()))
            ->setFields($fields);

        return new SearchRequest($this->normalizer, clone $this);
    }

    /**
     * Remove empty filters group from filters.
     *
     * @param array $filters Array of filters.
     *
     * @return array
     */
    private function normalizeFilters(array $filters)
    {
        $normalizedFilters = [];

        foreach ($filters as $filter) {
            if ($filter instanceof GroupFilterInterface) {
                $filter->setFilters($this->normalizeFilters($filter->getFilters()));
                if (count($filter) === 0) {
                    continue;
                }
            }

            $normalizedFilters[] = $filter;
        }

        return $normalizedFilters;
    }
}
