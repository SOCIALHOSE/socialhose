<?php

namespace AuthenticationBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AuthenticationFactory
 * @package AuthenticationBundle\Security\Factory
 */
class AuthenticationFactory implements SecurityFactoryInterface
{
    /**
     * Configures the container services required to use the authentication
     * listener.
     *
     * @param ContainerBuilder $container         A ContainerBuilder instance.
     * @param string           $id                The unique id of the firewall.
     * @param mixed            $config            The options array for the
     *                                            listener.
     * @param string           $userProvider      The service id of the user
     *                                            provider.
     * @param string           $defaultEntryPoint Default entry point.
     *
     * @return array containing three values:
     *               - the provider id
     *               - the listener id
     *               - the entry point id
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(
        ContainerBuilder $container,
        $id,
        $config,
        $userProvider,
        $defaultEntryPoint
    ) {
        $providerId = 'security.authentication.provider.get.jwt.'.$id;
        $container
            ->setDefinition(
                $providerId,
                new DefinitionDecorator('security.authentication.provider.dao')
            )
            ->replaceArgument(0, new Reference($userProvider))
            ->replaceArgument(1, new Reference('security.user_checker'))
            ->replaceArgument(2, $id);

        $listenerId = 'security.authentication.listener.get.jwt.'.$id;
        $container
            ->setDefinition(
                $listenerId,
                new DefinitionDecorator('authentication_bundle.authentication.listener')
            )
            ->replaceArgument(4, $id);

        return [ $providerId, $listenerId, $defaultEntryPoint ];
    }

    /**
     * Defines the position at which the provider is called.
     * Possible values: pre_auth, form, http, and remember_me.
     *
     * @return string
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * Defines the configuration key used to reference the provider
     * in the firewall configuration.
     *
     * @return string
     */
    public function getKey()
    {
        return 'socialhose_auth';
    }

    /**
     * @param NodeDefinition $builder A NodeDefinition instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function addConfiguration(NodeDefinition $builder)
    {
        // Do nothing.
    }
}
