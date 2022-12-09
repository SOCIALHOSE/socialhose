<?php

namespace CacheBundle\Security\Inspector;

use ApiBundle\Security\Inspector\AbstractInspector;
use CacheBundle\Entity\Analytic\Analytic;
use UserBundle\Entity\User;

/**
 * Class AnalyticInspector
 * @package CacheBundle\Security\Inspector
 */
class AnalyticInspector extends AbstractInspector
{

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass()
    {
        return Analytic::class;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Analytic|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canCreate(User $user, $entity)
    {
        $this->addReasonIf(
            "Can't create analytics 'cause you don't have permissions for it.",
            ! $user->getBillingSubscription()->getPlan()->isAnalytics()
        );
    }

    /**
     * Check that user can read specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Analytic|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canRead(User $user, $entity)
    {
        // todo implement check
    }

    /**
     * Check that user can update specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Analytic|object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function canUpdate(User $user, $entity)
    {
        // todo implement check
    }

    /**
     * Check that user can delete specified entity.
     *
     * @param User            $user   A user who try to create entity.
     * @param Analytic|object $entity A Entity instance.
     *
     * @return void
     */
    protected function canDelete(User $user, $entity)
    {
        $this
            ->addReasonIf(
                "Can't delete analytic owned by other user.",
                ! $entity->isOwnedBy($user)
            );
    }


}
