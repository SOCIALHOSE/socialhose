<?php

namespace CacheBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use UserBundle\Entity\User;

/**
 * Class FeedInspector
 * @package CacheBundle\Security\Inspector
 */
class FeedInspector extends AbstractInspector
{

    /**
     * Return supported entity fqcn.
     *
     * @return string|string[]
     */
    public static function supportedClass()
    {
        return [ QueryFeed::class, ClipFeed::class ];
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param AbstractFeed|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create feed for other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param AbstractFeed|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canRead(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't read feed owned by other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param AbstractFeed|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canUpdate(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't update feed owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param AbstractFeed|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete feed owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }
}
