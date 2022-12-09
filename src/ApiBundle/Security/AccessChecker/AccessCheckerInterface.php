<?php

namespace ApiBundle\Security\AccessChecker;

/**
 * Interface AccessCheckerInterface
 * Check access to entity for current user.
 *
 * @package ApiBundle\Security\AccessChecker
 */
interface AccessCheckerInterface
{

    /**
     * Checks that given user can make given action with specified entity.
     *
     * @param string $action Action name.
     * @param object $entity A Entity instance.
     *
     * @return string[] Array of restriction reasons.
     */
    public function isGranted($action, $entity);
}
