<?php

namespace CacheBundle;

use CacheBundle\DependencyInjection\Compiler\CollectFeedFetchersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class CacheBundle
 * @package CacheBundle
 */
class CacheBundle extends Bundle
{

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
        $container->addCompilerPass(new CollectFeedFetchersCompilerPass());
    }
}
