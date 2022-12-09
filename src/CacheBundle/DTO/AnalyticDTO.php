<?php

namespace CacheBundle\DTO;

use CacheBundle\Entity\Feed\AbstractFeed;
use IndexBundle\Filter\FilterInterface;
use UserBundle\Entity\User;

/**
 * Class AnalyticDTO
 *
 * Contains all data which is used for creating Analytic and AnalyticContext entities.
 *
 * @package CacheBundle\DTO
 */
class AnalyticDTO
{

    /**
     * Used feeds as source of data for analyzes.
     *
     * @var AbstractFeed[]
     */
    public $feeds;

    /**
     * Analytic owner.
     *
     * @var User
     */
    public $owner;


    /**
     * Additional filters for data from feeds.
     *
     * @var FilterInterface[]
     */
    public $filters;

    /**
     * Additional filters as is it's passed from frontend.
     *
     * @var array
     */
    public $rawFilters;

    /**
     * AnalyticDTO constructor.
     *
     * @param array             $feeds      Used feeds as source of data for
     *                                      analyzes.
     * @param User              $owner      Analytic owner.

     * @param FilterInterface[] $filters    Additional filters for data from feeds.
     * @param array             $rawFilters Additional filters as is it's passed
     *                                      from frontend.
     */
    public function __construct(
        array $feeds = [],
        User $owner = null,
        array $filters = [],
        array $rawFilters = []
    ) {
        $this->feeds = $feeds;
        $this->owner = $owner;
        $this->filters = $filters;
        $this->rawFilters = $rawFilters;
    }
}
