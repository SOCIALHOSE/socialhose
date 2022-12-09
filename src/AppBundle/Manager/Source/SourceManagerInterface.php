<?php

namespace AppBundle\Manager\Source;

use AppBundle\Response\SearchResponse;
use CacheBundle\Entity\Query\AbstractQuery;
use CacheBundle\Entity\SourceList;
use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use UserBundle\Entity\User;

/**
 * Interface SourceManagerInterface
 * @package AppBundle\Manager\Source
 */
interface SourceManagerInterface
{

    /**
     * Find all sources matched to specified builder.
     * If $sourceList is not null that make additional filter by specified source
     * list id.
     *
     * @param SearchRequestBuilderInterface $builder    A
     *                                                  SearchRequestBuilderInterface
     *                                                  instance.
     * @param SourceList                    $sourceList A SourceList entity instance.
     *
     * @return SearchResponse
     */
    public function find(
        SearchRequestBuilderInterface $builder,
        SourceList $sourceList = null
    );

    /**
     * Place all specified sources into specified lists.
     *
     * @param User              $user    A User entity instance.
     * @param string|string[]   $sources Array of source id or single id.
     * @param integer|integer[] $lists   Array of SourceList entity id or single
     *                                   id.
     *
     * @return void
     */
    public function bindSourcesToLists(User $user, $sources, $lists);

    /**
     * Add specified sources to specific source list.
     *
     * @param array   $sources Array of updates sources ids.
     * @param integer $id      A SourceList entity id.
     *
     * @return void
     */
    public function addSourcesToList(array $sources, $id);

    /**
     * Get all source's which used in filter's of specified query.
     *
     * @param AbstractQuery $query  A AbstractQuery entity instance.
     * @param array         $fields Array of requested fields.
     *
     * @return array[]
     */
    public function getSourcesForQuery(AbstractQuery $query, array $fields);

    /**
     * Get all source list's which used in filter's of specified query.
     *
     * @param AbstractQuery $query  A AbstractQuery entity instance.
     * @param array         $fields Array of requested fields.
     *
     * @return array[]
     */
    public function getSourceListsForQuery(AbstractQuery $query, array $fields);

    /**
     * Get available advanced filters.
     *
     * @param SearchRequestBuilderInterface $builder    A SearchRequestBuilderInterface
     *                                                  instance.
     * @param SourceList|null               $sourceList A SourceList entity instance.
     *
     * @return mixed
     */
    public function getAvailableFilters(
        SearchRequestBuilderInterface $builder,
        SourceList $sourceList = null
    );

    /**
     * Make relation between specified source and source lists.
     * All exists source relation will be overridden.
     *
     * @param integer $source Source id.
     * @param array   $lists  Array of SourceList entity ids.
     *
     * @return void
     */
    public function replaceRelation($source, array $lists);

    /**
     * Unbind all binded sources from specified lists.
     *
     * @param integer|integer[] $lists Array of SourceList entity id or single id.
     *
     * @return void
     */
    public function unbindSourcesFromLists($lists);

    /**
     * Update source cache.
     *
     * Fetch source from external index and store it into our cache. If we
     * already got sources in our cache se try to get source occurred after
     * oldest source.
     *
     * If our source cache is empty, we just get all available source. This should
     * be done in background.
     *
     * @return void
     */
    public function pullFromExternal();

    /**
     * @return SourceIndexInterface
     */
    public function getIndex();
}
