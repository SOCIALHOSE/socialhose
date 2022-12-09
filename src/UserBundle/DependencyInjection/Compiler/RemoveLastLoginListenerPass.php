<?php

namespace UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class RemoveLastLoginListenerPass
 *
 * Remove standard LastLoginListener.
 * We use stateless authentication so last login will be update on each request
 * to api and this is not right.
 *
 * @package UserBundle\DependencyInjection\Compiler
 */
class RemoveLastLoginListenerPass implements CompilerPassInterface
{

    const ID = 'fos_user.security.interactive_login_listener';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition(self::ID)) {
            $container->removeDefinition(self::ID);
        }
    }
}
