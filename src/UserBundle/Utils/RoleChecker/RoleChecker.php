<?php

namespace UserBundle\Utils\RoleChecker;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;
use UserBundle\Entity\User;

/**
 * Class RoleChecker
 * @package UserBundle\Utils\RoleChecker
 */
class RoleChecker implements RoleCheckerInterface
{

    /**
     * @var RoleHierarchyInterface
     */
    private $hierarchy;

    /**
     * @var array
     */
    private $raw;

    /**
     * RoleChecker constructor.
     *
     * @param RoleHierarchyInterface $hierarchy A RoleHierarchyInterface instance.
     * @param array                  $raw       A Raw role hierarchy.
     */
    public function __construct(RoleHierarchyInterface $hierarchy, array $raw)
    {
        $this->hierarchy = $hierarchy;
        $this->raw = $raw;
    }

    /**
     * Checks that specified user has necessary role.
     *
     * @param User                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function has(User $user, $role)
    {
        return in_array($role, $this->getUserRole($user), true);
    }

    /**
     * Checks that specified user has given role or lower.
     *
     * @param User                 $user A checked User entity instance.
     * @param string|RoleInterface $role A role name or Role instance.
     *
     * @return boolean
     */
    public function hasNotHigherThen(User $user, $role)
    {
        $actual = $this->getUserRole($user);
        $actualOrder = max(array_map(function ($role) {
            return $this->computeRoleOrder($role);
        }, $actual));
        $expectedOrder = $this->computeRoleOrder($role);

        return $actualOrder <= $expectedOrder;
    }

    /**
     * Get reachable roles for specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return array
     */
    private function getUserRole(User $user)
    {
        return array_map(function (RoleInterface $role) {
            return $role->getRole();
        }, $this->hierarchy->getReachableRoles(array_map(function ($role) {
            return new Role($role);
        }, $user->getRoles())));
    }

    /**
     * @param string  $role  Role name.
     * @param integer $order Current order.
     *
     * @return integer
     */
    private function computeRoleOrder($role, $order = 0)
    {
        $roles = $this->raw[$role];
        if (count($roles) === 0) {
            return $order;
        }

        return max(array_map(function ($role) use ($order) {
            return $this->computeRoleOrder($role, $order + 1);
        }, $this->raw[$role]));
    }
}
