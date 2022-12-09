<?php

namespace UserBundle\Utils\RoleChecker;

use Symfony\Component\Security\Core\Role\RoleInterface;
use UserBundle\Entity\User;

/**
 * Interface RoleCheckerInterface
 * Checks user role by using role hierarchy.
 *
 * @package UserBundle\Utils\RoleChecker
 */
interface RoleCheckerInterface
{

    /**
     * Checks that specified user has necessary role.
     *
     * @param User                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function has(User $user, $role);

    /**
     * Checks that specified user has given role or lower.
     *
     * @param User                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function hasNotHigherThen(User $user, $role);
}
