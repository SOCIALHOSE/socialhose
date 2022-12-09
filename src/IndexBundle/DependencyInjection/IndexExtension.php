<?php

namespace IndexBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class IndexExtension
 * @package IndexBundle\DependencyInjection
 */
class IndexExtension extends Extension
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
            new FileLocator(__DIR__ .'/../Resources/config')
        );

        // Get environment.
        $environment = $container->getParameter('kernel.environment');

        $loader->load('indices.yml');
        $loader->load('indices_'. $environment . '.yml');
    }
}
