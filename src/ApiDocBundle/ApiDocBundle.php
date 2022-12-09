<?php

namespace ApiDocBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ApiDocBundle
 * @package ApiDocBundle
 */
class ApiDocBundle extends Bundle
{

    /**
     * Returns the bundle parent name.
     *
     * @return string The Bundle parent name it overrides or null if no parent
     */
    public function getParent()
    {
        return 'NelmioApiDocBundle';
    }
}
