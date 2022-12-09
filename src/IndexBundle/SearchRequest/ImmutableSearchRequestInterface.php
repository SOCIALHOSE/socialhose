<?php

namespace IndexBundle\SearchRequest;

use UserBundle\Entity\User;

/**
 * Interface ImmutableSearchRequestInterface
 *
 * Internal representation of search request (only getters).
 * Also contains common methods which must be in request and builder.
 *
 * @package IndexBundle\SearchRequest
 */
interface ImmutableSearchRequestInterface
{

    /**
     * Return filters.
     *
     * @return \IndexBundle\Filter\FilterInterface[]
     */
    public function getFilters();

    /**
     * Return Aggregations.
     *
     * @return \IndexBundle\Aggregation\AggregationInterface[]
     */
    public function getAggregation();

    /**
     * Get user who made this search request.
     *
     * @return User
     */
    public function getUser();

    /**
     * Get fields names.
     *
     * @return string[]
     */
    public function getFields();

    /**
     * Get fetched source fields names.
     *
     * @return string[]
     */
    public function getSources();

    /**
     * Get raw search query.
     *
     * @return string
     */
    public function getQuery();

    /**
     * Set requested page number.
     *
     * @param integer $page Page number, start from 1.
     *
     * @return static
     */
    public function setPage($page);

    /**
     * Get requested page number.
     *
     * @return integer
     */
    public function getPage();

    /**
     * Set limit of documents per page.
     *
     * @param integer $limit Max document per page.
     *
     * @return static
     */
    public function setLimit($limit);

    /**
     * Get limit of documents per page.
     *
     * @return integer
     */
    public function getLimit();

    /**
     * Get sorting fields.
     *
     * @return array Where key is field name and value is sorting direction.
     */
    public function getSorts();
}
