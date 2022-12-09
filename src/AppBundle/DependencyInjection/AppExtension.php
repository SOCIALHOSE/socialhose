<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class AppExtension
 * @package ApiDocBundle\DependencyInjection
 */
class AppExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values.
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in
     *                                   this extension.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $environment = $container->getParameter('kernel.environment');
        if ($environment === 'prod') {
            //
            // Inject NelmioApiDocBundle form extensions 'cause we don't load full
            // nelmio configuration on production and we got error.
            //
            $loader->load('nelmio_form.yml');
        }

        $loader->load('services.yml');
    }
}
