<?php

namespace AppBundle\Manager\Feed;

use CacheBundle\Entity\Feed\AbstractFeed;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Interface FeedManagerInterface
 *
 * @package AppBundle\Manager\Feed
 */
interface FeedManagerInterface
{

    /**
     * Clip document to specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     * @param array        $ids  Array for Document entities ids.
     *
     * @return void
     */
    public function clip(AbstractFeed $feed, array $ids);

    /**
     * Delete documents from specified feed.
     *
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     * @param array        $ids  Array of Document entities ids.
     *
     * @return void
     */
    public function deleteDocuments(AbstractFeed $feed, array $ids = []);

    /**
     * Get index used by feed manager.
     *
     * @return InternalIndexInterface
     */
    public function getIndex();
}
