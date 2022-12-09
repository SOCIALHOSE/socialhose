<?php

namespace ApiBundle\EventListener;

use ApiBundle\Controller\Annotation\Roles;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class RolesListener
 * @package ApiBundle\EventListener
 */
class RolesListener
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var RoleHierarchyInterface
     */
    private $hierarchy;

    /**
     * RolesListener constructor.
     *
     * @param TokenStorageInterface  $storage   A TokenStorageInterface instance.
     * @param RoleHierarchyInterface $hierarchy A RoleHierarchyInterface instance.
     */
    public function __construct(
        TokenStorageInterface $storage,
        RoleHierarchyInterface $hierarchy
    ) {
        $this->storage = $storage;
        $this->hierarchy = $hierarchy;
    }

    /**
     * @param FilterControllerEvent $event A FilterControllerEvent instance.
     *
     * @return void
     */
    public function handle(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $roles = $request->attributes->get('_roles');
        if (! $roles instanceof Roles) {
            return;
        }

        $token = $this->storage->getToken();

        $expected = (array) $roles->roles;
        $actual = array_map(function (RoleInterface $role) {
            return $role->getRole();
        }, $this->hierarchy->getReachableRoles($token->getRoles()));

        if (count(array_diff($expected, $actual)) > 0) {
            $message = 'You don\'t have enough roles to call this method. Required '
                . implode(', ', $expected) .'.';
            throw new AccessDeniedHttpException($message);
        }
    }
}
