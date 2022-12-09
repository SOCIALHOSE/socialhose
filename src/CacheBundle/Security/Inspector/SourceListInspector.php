<?php

namespace CacheBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use CacheBundle\Entity\SourceList;
use UserBundle\Entity\User;

/**
 * Class SourceListInspector
 * @package CacheBundle\Security\Inspector
 */
class SourceListInspector extends AbstractInspector
{

    const SHARE = 'share';
    const UNSHARE = 'unshare';

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return [ SourceList::class ];
    }

    /**
     * Checks that given user can make given action with specified entity.
     *
     * @param User              $user   A User entity instance.
     * @param object|SourceList $entity A Entity instance or array of instances.
     * @param string            $action Action name.
     *
     * @return string[] Array of restriction reasons.
     */
    public function inspect(User $user, $entity, $action)
    {
        parent::inspect($user, $entity, $action);

        if ($action === self::SHARE) {
            $this->addReasonIf(
                "Can't share source list owned by other user.",
                ! $entity->isOwnedBy($user)
            );
        } elseif ($action === self::UNSHARE) {
            $this->addReasonIf(
                "Can't unshare source list owned by other user.",
                ! $entity->isOwnedBy($user)
            );
        }

        return $this->reasons;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User              $user   A user who try to create entity.
     * @param SourceList|object $entity A Entity instance.
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
     * @param User              $user   A user who try to create entity.
     * @param SourceList|object $entity A Entity instance.
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
     * @param User              $user   A user who try to create entity.
     * @param SourceList|object $entity A Entity instance.
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
     * @param User              $user   A user who try to create entity.
     * @param SourceList|object $entity A Entity instance.
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
