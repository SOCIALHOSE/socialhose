<?php

namespace ApiBundle\Security\Inspector;

use UserBundle\Entity\User;

/**
 * Interface InspectorInterface
 * Inspect entity and decides to give access or not to inspected entity.
 *
 * @package ApiBundle\Security\Inspector
 */
interface InspectorInterface
{

    const CREATE = 'create';
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';

    /**
     * Checks that given user can make given action with specified entity.
     *
     * @param User   $user   A User entity instance.
     * @param object $entity A Entity instance or array of instances.
     * @param string $action Action name.
     *
     * @return string[] Array of restriction reasons.
     */
    public function inspect(User $user, $entity, $action);

    /**
     * Return supported entity fqcn.
     *
     * @return string
     */
    public static function supportedClass();
}
