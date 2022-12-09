<?php

namespace Common\Util\Index;

use AppBundle\AdvancedFilters\AFResolverInterface;
use AppBundle\Response\SearchResponseInterface;
use IndexBundle\Aggregation\AggregationFacadeInterface;
use IndexBundle\Aggregation\Factory\AggregationFactoryInterface;
use IndexBundle\Index\External\ExternalIndexInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Class AbstractTestIndexConnection
 *
 * @package Common\Util\Index
 */
abstract class AbstractTestIndexConnection implements TestIndexConnectionInterface
{

    /**
     * @var InternalIndexInterface
     */
    private $index;

    /**
     * AbstractIndexConnection constructor.
     *
     * @param InternalIndexInterface $index A InternalIndexInterface interface.
     */
    public function __construct(InternalIndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * Update specified document.
     *
     * Make partial update so in data must be placed only changed properties.
     *
     * @param string|integer $id   Updated document id.
     * @param array          $data Array of changed data where key is property
     *                             name and value is new property value.
     *
     * @return void
     */
    public function update($id, array $data)
    {
        $this->index->update($id, $data);
    }

    /**
     * Update array of documents.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param array $config Array of arrays where key is updated document id and
     *                      value is array of updated fields same as $data in
     *                      `update` method.
     *
     * @return void
     */
    public function updateBulk(array $config)
    {
        $this->index->updateBulk($config);
    }

    /**
     * Update array of documents with filtering.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     * @param string                 $script  Updating script.
     * @param array                  $params  Script parameters.
     *
     * @return void
     */
    public function updateByQuery(SearchRequestInterface $request, $script, array $params = [])
    {
        $this->index->updateByQuery($request, $script, $params);
    }

    /**
     * Remove document by specified id or array of ids.
     *
     * @param string|string[] $id Document id or array of document ids.
     *
     * @return void
     */
    public function remove($id)
    {
        $this->index->remove($id);
    }

    /**
     * Search information in index.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return SearchResponseInterface
     */
    public function search(SearchRequestInterface $request)
    {
        return $this->index->search($request);
    }

    /**
     * Fetch all relevant documents.
     *
     * @param SearchRequestInterface $request Internal representation of search
     *                                        request.
     *
     * @return \Traversable
     */
    public function fetchAll(SearchRequestInterface $request)
    {
        return $this->index->fetchAll($request);
    }

    /**
     * Create search request builder for this index connection.
     *
     * @return SearchRequestBuilderInterface
     */
    public function createRequestBuilder()
    {
        return $this->index->createRequestBuilder();
    }

    /**
     * Get filter factory instance.
     *
     * @return \IndexBundle\Filter\Factory\FilterFactoryInterface
     */
    public function getFilterFactory()
    {
        return $this->index->getFilterFactory();
    }

    /**
     * Get aggregation factory instance
     *
     * @return AggregationFactoryInterface
     */
    public function getAggregationFactory()
    {
        return $this->index->getAggregationFactory();
    }

    /**
     * Get aggregation instance
     *
     * @return AggregationFacadeInterface
     */
    public function getAggregation()
    {
        return $this->index->getAggregation();
    }

    /**
     * Return advanced filters aggregator.
     *
     * @return AFResolverInterface
     */
    public function getAFResolver()
    {
        return $this->index->getAFResolver();
    }

    /**
     * Get strategy used by this index.
     *
     * @return IndexStrategyInterface
     */
    public function getStrategy()
    {
        return $this->index->getStrategy();
    }

    /**
     * Create new index.
     *
     * @param array $mapping  Index mapping.
     * @param array $settings Index settings.
     *
     * @return void
     */
    public function createIndex(array $mapping, array $settings = [])
    {
        if ($this->index instanceof InternalIndexInterface) {
            $this->index->createIndex($mapping, $settings);
        } else {
            throw new \LogicException('Can\'t create index on '. get_class($this->index));
        }
    }

    /**
     * Index given document or array of documents.
     *
     * @param DocumentInterface|DocumentInterface[] $data DocumentInterface instance
     *                                                    or array of instances.
     *
     * @return void
     */
    public function index($data)
    {
        if ($this->index instanceof InternalIndexInterface) {
            $this->index->index($data);
        } else {
            throw new \LogicException('Can\'t index documents on '. get_class($this->index));
        }
    }

    /**
     * Purge index.
     *
     * @return void
     */
    public function purge()
    {
        if ($this->index instanceof InternalIndexInterface) {
            $this->index->purge();
        } else {
            throw new \LogicException('Can\'t purge index on '. get_class($this->index));
        }
    }

    /**
     * Get documents by it ids.
     *
     * @param integer|integer[] $ids    Array of document ids or single id.
     * @param string|string[]   $fields Array of requested fields of single
     *                                  field.
     *
     * @return \IndexBundle\Model\DocumentInterface[]
     */
    public function get($ids, $fields = [])
    {
        return $this->index->get($ids, $fields);
    }

    /**
     * Check that specified documents is exists.
     *
     * @param integer|array $ids Array of document ids or single id.
     *
     * @return array Contains all ids which not found in index.
     */
    public function has($ids)
    {
        return $this->index->has($ids);
    }

    /**
     * @return IndexInterface|InternalIndexInterface|ExternalIndexInterface|SourceIndexInterface
     */
    public function getIndex()
    {
        return $this->index;
    }
}
