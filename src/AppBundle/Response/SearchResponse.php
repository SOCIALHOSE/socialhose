<?php

namespace AppBundle\Response;

use IndexBundle\Model\DocumentInterface;

/**
 * Class SearchResponse
 *
 * @package AppBundle\Response
 */
class SearchResponse implements SearchResponseInterface
{

    /**
     * @var DocumentInterface[]
     */
    private $documents;

    /**
     * @var array
     */
    private $aggregationResults;

    /**
     * @var integer
     */
    private $totalCount;

    /**
     * @var integer
     */
    private $uniqueCount;

    /**
     * @var boolean
     */
    private $fromCache;

    /**
     * @param array   $documents          Array of results.
     * @param array   $aggregationResults Array of results of aggregation.
     * @param integer $totalCount         Total available counts.
     * @param integer $uniqueCount        Count of unique documents added
     *                                    to cache.
     * @param boolean $fromCache          True if data fetched from cache.
     */
    public function __construct(
        array $documents = [],
        array $aggregationResults = [],
        $totalCount = 0,
        $uniqueCount = 0,
        $fromCache = false
    ) {
        if (! \nspl\a\all($documents, \nspl\f\rpartial(\app\op\isInstanceOf, DocumentInterface::class))) {
            throw new \InvalidArgumentException(sprintf(
                'All documents should instance of %s',
                DocumentInterface::class
            ));
        }

        $this->documents = $documents;
        $this->aggregationResults = $aggregationResults;
        $this->totalCount = $totalCount;
        $this->uniqueCount = $uniqueCount;
        $this->fromCache = $fromCache;
    }

    /**
     * Get documents from response.
     *
     * @return DocumentInterface[]
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * @param callable|\Closure $callback Mapper callback.
     *
     * @return SearchResponse
     */
    public function mapDocuments($callback)
    {
        $this->documents = array_map($callback, $this->documents);

        return $this;
    }

    /**
     * Get response aggregation results.
     *
     * @return array
     */
    public function getAggregationResults()
    {
        return $this->aggregationResults;
    }

    /**
     * Get total count of available results.
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }

    /**
     * Get unique documents count.
     *
     * @return integer
     */
    public function getUniqueCount()
    {
        return $this->uniqueCount;
    }

    /**
     * @return boolean
     */
    public function isFromCache()
    {
        return $this->fromCache;
    }

    /**
     * Return count of results in current response.
     *
     * @return integer
     */
    public function count()
    {
        return count($this->documents);
    }

    /**
     * @param integer $offset The offset to retrieve.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->documents[$offset];
    }

    /**
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Can\'t change result set.');
    }

    /**
     * @param mixed $offset The offset to unset.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Can\'t change result set.');
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->documents);
    }
}
