<?php

namespace IndexBundle\Index;

use AppBundle\AdvancedFilters\AFResolver;
use AppBundle\AdvancedFilters\AFResolverInterface;
use AppBundle\AdvancedFilters\Aggregator\AFAggregatorInterface;
use IndexBundle\Filter\Factory\FilterFactory;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use IndexBundle\Filter\Resolver\FilterResolverInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Normalizer\Query\QueryNormalizer;
use IndexBundle\Normalizer\Query\QueryNormalizerInterface;
use IndexBundle\SearchRequest\SearchRequestBuilder;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;

/**
 * Class AbstractIndex
 *
 * @package IndexBundle\Index
 */
abstract class AbstractIndex implements IndexInterface
{

    /**
     * @var QueryNormalizerInterface
     */
    private $queryNormalizer;

    /**
     * @var FilterFactoryInterface
     */
    private $filterFactory;

    /**
     * @var FilterResolverInterface
     */
    private $filterResolver;

    /**
     * @var AFResolverInterface
     */
    private $afResolver;

    /**
     * @var IndexStrategyInterface
     */
    private $strategy;

    /**
     * Create request builder instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function createRequestBuilder()
    {
        return new SearchRequestBuilder($this, $this->getQueryNormalizer());
    }

    /**
     * Get document normalizer for this index.
     *
     * @return IndexStrategyInterface
     */
    public function getStrategy()
    {
        if ($this->strategy === null) {
            $this->strategy = $this->createStrategy();
        }

        return $this->strategy;
    }

    /**
     * Get filter resolver.
     *
     * @return FilterFactoryInterface
     */
    public function getFilterFactory()
    {
        if ($this->filterFactory === null) {
            $this->filterFactory = new FilterFactory();
        }

        return $this->filterFactory;
    }

    /**
     * Get aggregation filter resolver.
     *
     * @return AFResolverInterface
     */
    public function getAFResolver()
    {
        if ($this->afResolver === null) {
            $this->afResolver = new AFResolver(
                $this->createAggregator(),
                $this->getFilterFactory()
            );
        }

        return $this->afResolver;
    }

    /**
     * Get query normalizer which will be used for this index.
     *
     * @return QueryNormalizerInterface
     */
    protected function getQueryNormalizer()
    {
        if ($this->queryNormalizer === null) {
            $this->queryNormalizer = new QueryNormalizer();
        }

        return $this->queryNormalizer;
    }

    /**
     * @return FilterResolverInterface
     */
    protected function getFilterResolver()
    {
        if ($this->filterResolver === null) {
            $this->filterResolver = $this->createFilterResolver();
        }

        return $this->filterResolver;
    }

    /**
     * Create concrete filter resolver instance.
     *
     * @return FilterResolverInterface
     */
    abstract protected function createFilterResolver();

    /**
     * @return AFAggregatorInterface
     */
    abstract protected function createAggregator();

    /**
     * Create concrete strategy instance.
     *
     * @return IndexStrategyInterface
     */
    abstract protected function createStrategy();
}
