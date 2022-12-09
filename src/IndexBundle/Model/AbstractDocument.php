<?php

namespace IndexBundle\Model;

use IndexBundle\Index\Strategy\IndexStrategyInterface;

/**
 * Class AbstractDocument
 *
 * @package IndexBundle\Model
 */
abstract class AbstractDocument implements DocumentInterface
{

    /**
     * @var IndexStrategyInterface
     */
    protected $strategy;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    private $normalizedData;

    /**
     * @var array
     */
    private $indexableData;

    /**
     * @var callable[]|\Closure[]
     */
    private $normalizerListeners = [];

    /**
     * ExternalDocument constructor.
     *
     * @param IndexStrategyInterface $strategy Index strategy instance which
     *                                         is used for manipulating documents.
     * @param array                  $data     Data from external index.
     */
    public function __construct(IndexStrategyInterface $strategy, array $data)
    {
        $this->strategy = $strategy;
        $this->data = $data;
    }

    /**
     * @param callable|\Closure $listener Listener callback.
     *
     * @return $this
     */
    public function addNormalizerListener($listener)
    {
        $this->normalizerListeners[] = $listener;

        return $this;
    }

    /**
     * Get normalized data from document as array.
     *
     * @return array
     */
    public function getNormalizedData()
    {
        if ($this->normalizedData === null) {
            $this->normalizedData = $this->strategy->normalizeDocumentData($this->data);

            foreach ($this->normalizerListeners as $listener) {
                $this->normalizedData = $listener($this->normalizedData);
            }
        }

        return $this->normalizedData;
    }

    /**
     * Normalize inner data.
     *
     * @return $this
     */
    public function normalize()
    {
        $this->data = $this->getNormalizedData();

        return $this;
    }

    /**
     * Get data used for indexing.
     *
     * @return array
     */
    public function getIndexableData()
    {
        if ($this->indexableData === null) {
            $this->indexableData = $this->strategy->getIndexableData($this->data);
        }

        return $this->indexableData;
    }

    /**
     * Map data inside document.
     *
     * Callback signature:
     * ```php
     * function (array $data): array { ... }
     * ```
     *
     * @param callable|\Closure $callback Data mapper callback.
     *
     * @return static
     */
    public function mapRawData($callback)
    {
        $this->data = $callback($this->data);
        $this->normalizedData = null;
        $this->indexableData = null;

        return $this;
    }

    /**
     * Add listener which is called after normalization process.
     *
     * Callback signature:
     * ```php
     * function (array $data): array { ... }
     * ```
     *
     * @param callable|\Closure $callback Listener.
     *
     * @return static
     */
    public function mapNormalizedData($callback)
    {
        $this->normalizerListeners[] = $callback;
        $this->normalizedData = null;
        $this->indexableData = null;

        return $this;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (! isset($this->data[$offset])) {
            throw new \InvalidArgumentException('Unknown property \''. $offset .'\'');
        }

        return $this->data[$offset];
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
        $this->normalizedData = null;
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('Can\'t unset \''. $offset .'\'');
    }

    /**
     * Retrieve an external iterator
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Is utilized for reading data from inaccessible members.
     *
     * @param string $name Property name.
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * Run when writing data to inaccessible members.
     *
     * @param string $name  Property name.
     * @param mixed  $value New value.
     *
     * @return void
     *
     * @deprecated Use mapData instead.
     * todo remove it
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
        $this->normalizedData = null;
    }

    /**
     * Is triggered by calling isset() or empty() on inaccessible members.
     *
     * @param string $name Property name.
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}
