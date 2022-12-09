<?php

namespace UserBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\User;

/**
 * Class GroupRecipientInspector
 * @package CacheBundle\Security\Inspector
 */
class PersonRecipientInspector extends AbstractInspector
{

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return [ PersonRecipient::class ];
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User                   $user   A user who try to create entity.
     * @param PersonRecipient|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create recipient for other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User                   $user   A user who try to create entity.
     * @param PersonRecipient|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canRead(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't read recipient owned by other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User                   $user   A user who try to create entity.
     * @param PersonRecipient|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canUpdate(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't update recipient owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User                   $user   A user who try to create entity.
     * @param PersonRecipient|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete recipient owned by other user.",
                ! $entity->isOwnedBy($user)
            )
            ->addReasonIf(
                "Can't delete recipient which has associations to user",
                $entity->getAssociatedUser() !== null
            );
    }
}
