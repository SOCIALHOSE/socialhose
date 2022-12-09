<?php

namespace ApiBundle\Security\Inspector\Factory;

use ApiBundle\Security\Inspector\InspectorInterface;

/**
 * Interface InspectorFactoryInterface
 * Create entity inspector instance.
 *
 * @package ApiBundle\Security\Inspector\Factory
 */
interface InspectorFactoryInterface
{

    /**
     * Create proper inspector for given entity instance.
     *
     * @param object|string $class A Entity instance or fqcn.
     *
     * @return InspectorInterface
     */
    public function create($class);
}
