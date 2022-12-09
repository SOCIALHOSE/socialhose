<?php

namespace CacheBundle\Feed\Response;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface FeedResponseInterface
 * @package CacheBundle\Feed\Response
 */
interface FeedResponseInterface
{

    /**
     * Get response.
     *
     * @return \AppBundle\Response\SearchResponseInterface
     */
    public function getResponse();

    /**
     * Get response meta information.
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function getMeta(Request $request);

    /**
     * Get available advanced filters values.
     *
     * @return array
     */
    public function getAdvancedFilters();
}
