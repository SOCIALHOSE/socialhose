<?php

namespace UserBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use UserBundle\DependencyInjection\Compiler\RemoveLastLoginListenerPass;

/**
 * Class UserBundle
 * @package UserBundle
 */
class UserBundle extends Bundle
{

    /**
     * Returns the bundle name that this bundle overrides.
     *
     * Despite its name, this method does not imply any parent/child relationship
     * between the bundles, just a way to extend and override an existing
     * bundle.
     *
     * @return string The Bundle name it overrides or null if no parent
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }

    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new RemoveLastLoginListenerPass());
    }
}
