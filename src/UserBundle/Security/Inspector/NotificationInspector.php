<?php

namespace UserBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;

/**
 * Class NotificationInspector
 * @package CacheBundle\Security\Inspector
 */
class NotificationInspector extends AbstractInspector
{

    const SUBSCRIBE = 'subscribe';
    const UNSUBSCRIBE = 'unsubscribe';

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return [ Notification::class ];
    }

    /**
     * Checks that given user can make given action with specified entity.
     *
     * @param User                $user   A User entity instance.
     * @param object|Notification $entity A Entity instance or array of instances.
     * @param string              $action Action name.
     *
     * @return string[] Array of restriction reasons.
     */
    public function inspect(User $user, $entity, $action)
    {
        parent::inspect($user, $entity, $action);

        if ($action === self::SUBSCRIBE) {
            $this->addReasonIf(
                "Can't subscribe to notification owned by other user and not published.",
                ! $entity->isPublished() && ! $entity->isOwnedBy($user)
            );
        } elseif ($action === self::UNSUBSCRIBE) {
            $this->addReasonIf(
                "Can't unsubscribe from notification owned by other user and not published.",
                ! $entity->isPublished() && ! $entity->isOwnedBy($user)
            );

            if (count($this->reasons) === 0) {
                $this->addReasonIf(
                    "You can't unsubscribe from notification.",
                    !$entity->isAllowUnsubscribe()
                );
            }
        }

        return $this->reasons;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param Notification|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create notification for other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param Notification|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canRead(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't read notification owned by other user and not published.",
            ! $entity->isPublished() && ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param Notification|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canUpdate(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't update notification owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User                $user   A user who try to create entity.
     * @param Notification|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete notification owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }
}
