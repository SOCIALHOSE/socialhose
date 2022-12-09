<?php

namespace ApiBundle;

/**
 * Class ApiBundleServices
 * @package ApiBundle
 */
class ApiBundleServices
{

    /**
     * Check access to entity for current user.
     *
     * Must implements {@see \ApiBundle\Security\AccessChecker\AccessCheckerInterface}
     * interface.
     */
    const ACCESS_CHECKER = 'api.access_checker';
}
