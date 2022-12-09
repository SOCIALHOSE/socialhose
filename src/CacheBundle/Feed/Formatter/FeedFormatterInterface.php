<?php

namespace CacheBundle\Feed\Formatter;

use CacheBundle\Entity\Feed\AbstractFeed;

/**
 * Interface FeedFormatterInterface
 *
 * @package CacheBundle\Feed\Formatter
 */
interface FeedFormatterInterface
{

    /**
     * Format feed documents.
     *
     * @param AbstractFeed     $feed    A formatted feed entity instance.
     * @param FormatterOptions $options Used format options.
     *
     * @return FormattedData
     */
    public function formatFeed(AbstractFeed $feed, FormatterOptions $options);
}
