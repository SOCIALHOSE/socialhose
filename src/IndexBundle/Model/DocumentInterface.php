<?php

namespace IndexBundle\Model;

/**
 * Interface DocumentInterface
 *
 * @package IndexBundle\Model
 */
interface DocumentInterface extends \ArrayAccess, \IteratorAggregate
{

    /**
     * Get normalized data from document as array.
     *
     * @return array
     */
    public function getNormalizedData();

    /**
     * Normalize inner data.
     *
     * @return $this
     */
    public function normalize();

    /**
     * Get data used for indexing.
     *
     * @return array
     */
    public function getIndexableData();

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
    public function mapRawData($callback);

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
    public function mapNormalizedData($callback);
}
