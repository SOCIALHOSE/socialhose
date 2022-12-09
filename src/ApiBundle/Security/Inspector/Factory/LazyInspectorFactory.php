<?php

namespace ApiBundle\Security\Inspector\Factory;

use ApiBundle\Security\Inspector\InspectorInterface;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LazyInspectorFactory
 * Default implementation of InspectorFactoryInterface.
 * Use lazy loading for creating inspectors.
 *
 * @package ApiBundle\Security\Inspector\Factory
 */
class LazyInspectorFactory implements InspectorFactoryInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $inspectorsIds;

    /**
     * InspectorFactory constructor.
     *
     * @param ContainerInterface $container     A ContainerInterface instance.
     * @param array              $inspectorsIds Registered inspectors services
     *                                          ids.
     */
    public function __construct(ContainerInterface $container, array $inspectorsIds)
    {
        $this->container = $container;
        $this->inspectorsIds = $inspectorsIds;
    }

    /**
     * Create proper inspector for given entity instance.
     *
     * @param object|string $class A Entity instance or fqcn.
     *
     * @return InspectorInterface
     */
    public function create($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $class = ClassUtils::getRealClass($class);

        if (!is_string($class) || ! class_exists($class)) {
            throw new \InvalidArgumentException('Expects object or valid fqcn.');
        }

        if (! array_key_exists($class, $this->inspectorsIds)) {
            $message = "Can't find inspector for entity '{$class}'";
            throw new \InvalidArgumentException($message);
        }

        $inspector = $this->container->get($this->inspectorsIds[$class]);
        if (! $inspector instanceof InspectorInterface) {
            $message = 'Inspector must implements '. InspectorInterface::class;
            throw new \RuntimeException($message);
        }

        return $inspector;
    }
}
