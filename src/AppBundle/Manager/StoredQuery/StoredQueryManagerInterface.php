<?php

namespace AppBundle\Manager\StoredQuery;

use AppBundle\Response\SearchResponseInterface;
use CacheBundle\Entity\Query\StoredQuery;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use UserBundle\Entity\User;

/**
 * Interface StoredQueryManagerInterface
 * Manage stored query throughout the application.
 *
 * @package AppBundle\Manager\StoredQuery
 */
interface StoredQueryManagerInterface
{

    /**
     * Fetch documents for specified stored query.
     *
     * @param StoredQuery $query A StoredQuery entity instance for which we should
     *                           fetch documents.
     *
     * @return StoredQuery
     */
    public function fetchDocuments(StoredQuery $query);

    /**
     * Create new stored query.
     *
     * @param SearchRequestBuilderInterface $builder            A SearchRequestBuilderInterface
     *                                                          instance.
     * @param array                         $rawFilters         A raw filters.
     * @param array                         $rawAdvancedFilters A raw advanced filters.
     *
     * @return StoredQuery
     */
    public function createQuery(
        SearchRequestBuilderInterface $builder,
        array $rawFilters,
        array $rawAdvancedFilters
    );

    /**
     * Get documents from cache.
     *
     * @param User                          $user    User who requested documents.
     * @param StoredQuery                   $query   A StoredQuery entity instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     *
     * @return SearchResponseInterface
     */
    public function get(
        User $user,
        StoredQuery $query,
        SearchRequestBuilderInterface $builder
    );

    /**
     * Get documents from cache.
     *
     * Get all matched document but from specified date.
     *
     * @param User        $user  User who requested documents.
     * @param StoredQuery $query A StoredQuery entity instance.
     *
     * @return SearchRequestBuilderInterface
     */
    public function createRequestBuilder(User $user, StoredQuery $query);

    /**
     * Get advanced filters for specified query.
     *
     * @param StoredQuery                   $query   A StoredQuery entity instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     *
     * @return array
     */
    public function getAdvancedFilters(
        StoredQuery $query,
        SearchRequestBuilderInterface $builder
    );

    /**
     * Get total of documents for specified query.
     *
     * @param SearchRequestBuilderInterface $builder
     * @param array                         $rawFilters
     * @param array                         $rawAdvancedFilters
     * @return integer
     */
    public function getTotal(SearchRequestBuilderInterface $builder, array $rawFilters, array $rawAdvancedFilters);
}
