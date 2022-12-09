<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use IndexBundle\Model\ArticleDocumentInterface;

/**
 * Interface FeedFormatterStrategyInterface
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 */
interface FeedFormatterStrategyInterface
{

    /**
     * Return list of required document fields.
     *
     * @param FormatterOptions $options Formatter options.
     *
     * @return string[]
     */
    public function requiredFields(FormatterOptions $options);

    /**
     * Serialize feed.
     *
     * @param AbstractFeed               $feed      A serialized feed entity
     *                                              instance.
     * @param ArticleDocumentInterface[] $documents Array of fetched documents
     *                                              which should by serialized.
     * @param FormatterOptions           $options   Formatter options.
     *
     * @return mixed
     */
    public function serialize(
        AbstractFeed $feed,
        array $documents,
        FormatterOptions $options
    );

    /**
     * Get format mime type.
     *
     * @return string
     */
    public function getMime();
}
