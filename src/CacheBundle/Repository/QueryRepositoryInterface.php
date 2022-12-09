<?php

namespace CacheBundle\Repository;

/**
 * Interface QueryRepositoryInterface
 * @package CacheBundle\Repository
 */
interface QueryRepositoryInterface
{

    /**
     * Get query entity by internal representation of search query.
     *
     * @param string  $hash A search query hash.
     * @param integer $user A User entity id, who made search request.
     *
     * @return \CacheBundle\Entity\Query\AbstractQuery|null
     */
    public function get($hash, $user = null);
}
