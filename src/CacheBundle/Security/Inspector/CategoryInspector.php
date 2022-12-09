<?php

namespace CacheBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use CacheBundle\Entity\Category;
use UserBundle\Entity\User;

/**
 * Class CategoryInspector
 * @package CacheBundle\Security\Inspector
 */
class CategoryInspector extends AbstractInspector
{

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return Category::class;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Category|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create category for other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Category|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canRead(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't read category owned by other user.",
            ! $entity->isOwnedBy($user)
        );
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Category|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canUpdate(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't update category owned by other user.",
                ! $entity->isOwnedBy($user)
            )
            ->addReasonIf(
                "Can't update internal category.",
                $entity->isInternal()
            );
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Category|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete category owned by other user.",
                ! $entity->isOwnedBy($user)
            )
            ->addReasonIf(
                "Can't delete internal category.",
                $entity->isInternal()
            );
    }
}
