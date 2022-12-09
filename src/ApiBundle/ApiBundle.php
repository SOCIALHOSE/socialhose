<?php

namespace ApiBundle;

use ApiBundle\DependencyInjection\Compiler\InspectorsCollectCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ApiBundle
 * @package ApiBundle
 */
class ApiBundle extends Bundle
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
        parent::build($container);
        $container->addCompilerPass(new InspectorsCollectCompilerPass());
    }
}
