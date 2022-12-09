<?php

namespace CacheBundle\Comment\Manager;

use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;

/**
 * Interface CommentManagerInterface
 * @package CacheBundle\Comment\Manager
 */
interface CommentManagerInterface
{

    /**
     * Size of new comments pool.
     */
    const NEW_COMMENT_POOL_SIZE = 1;

    /**
     * Add new comment to specified entity.
     *
     * @param Comment  $comment  A Comment entity instance.
     * @param Document $document A Document entity instance.
     *
     * @return Comment
     */
    public function addComment(Comment $comment, Document $document);
}
