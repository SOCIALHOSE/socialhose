<?php

namespace ApiBundle\DependencyInjection\Compiler;

use ApiBundle\Security\Inspector\Factory\LazyInspectorFactory;
use ApiBundle\Security\Inspector\InspectorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class InspectorsCollectCompilerPass
 * Register inspectors into LazyInspectorFactory.
 * All inspectors must be tagged by 'socialhose.inspector'.
 *
 * @package ApiBundle\DependencyInjection\Compiler
 */
class InspectorsCollectCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->hasDefinition('api.inspector_factory')) {
            // Work only if we have definition of inspector factory.
            return;
        }

        $factory = $container->getDefinition('api.inspector_factory');
        if ($factory->getClass() !== LazyInspectorFactory::class) {
            // Works only for lazy inspector factory.
            return;
        }

        // Get all tagged inspectors and create map between supported class and
        // inspector service id.
        $inspectorsIds = [];
        $inspectors = $container->findTaggedServiceIds('socialhose.inspector');
        $inspectors = array_keys($inspectors);

        foreach ($inspectors as $id) {
            /** @var InspectorInterface $class */
            $class = $container->getDefinition($id)->getClass();

            $reflection = new \ReflectionClass($class);
            if (! $reflection->implementsInterface(InspectorInterface::class)) {
                // Tagged service not implements inspector interface.
                $message = "Inspector {$id} must implements "
                    . InspectorInterface::class;
                throw new \InvalidArgumentException($message);
            }

            $supported = (array) $class::supportedClass();
            foreach ($supported as $item) {
                $inspectorsIds[$item] = $id;
            }
        }

        // Inject founded inspectors into factory.
        $factory->replaceArgument(1, $inspectorsIds);
    }
}
