<?php

namespace CacheBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use CacheBundle\Entity\Comment;
use UserBundle\Entity\User;

/**
 * Class CommentInspector
 * @package CacheBundle\Security\Inspector
 */
class CommentInspector extends AbstractInspector
{

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return Comment::class;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User           $user   A user who try to create entity.
     * @param Comment|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create comment for other user.",
            $entity->getAuthor()->getId() !== $user->getId()
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User           $user   A user who try to create entity.
     * @param Comment|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canRead(User $user, $entity)
    {
        // Do nothing.
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User           $user   A user who try to create entity.
     * @param Comment|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canUpdate(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't update comment owned by other user.",
                $entity->getAuthor()->getId() !== $user->getId()
            );
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User           $user   A user who try to create entity.
     * @param Comment|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete comment owned by other user.",
                $entity->getAuthor()->getId() !== $user->getId()
            );
    }
}
