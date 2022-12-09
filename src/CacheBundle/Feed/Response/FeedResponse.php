<?php

namespace CacheBundle\Feed\Response;

use AppBundle\Response\SearchResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FeedResponse
 * @package CacheBundle\Feed\Response
 */
class FeedResponse implements FeedResponseInterface
{

    /**
     * @var SearchResponseInterface
     */
    private $response;

    /**
     * @var array
     */
    private $advancedFilters;

    /**
     * @var array
     */
    private $meta;

    /**
     * FeedResponse constructor.
     *
     * @param SearchResponseInterface $response        A SearchResponseInterface
     *                                              instance.
     * @param array                   $advancedFilters Advanced filters.
     * @param array                   $meta            Response meta information.
     */
    public function __construct(
        SearchResponseInterface $response,
        array $advancedFilters = [],
        array $meta = []
    ) {
        $this->response = $response;
        $this->advancedFilters = $advancedFilters;
        $this->meta = $meta;
    }

    /**
     * Get response.
     *
     * @return SearchResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get available advanced filters values.
     *
     * @return array
     */
    public function getAdvancedFilters()
    {
        return $this->advancedFilters;
    }

    /**
     * Get response meta information.
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function getMeta(Request $request)
    {
        $currentAdvancedFilters = $request->request->get('advancedFilters', []);

        //
        // Add currently selected advanced filters if they are provided.
        //
        if (count($currentAdvancedFilters)) {
            $this->meta['search']['advancedFilters'] = $currentAdvancedFilters;
        }

        return $this->meta;
    }
}
