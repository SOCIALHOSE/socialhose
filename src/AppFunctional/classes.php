<?php

namespace app\c;

/**
 * Convert specified entity FQCN into short style like 'AppBundle:Entity'.
 *
 * @param string $entityFqcn Entity fqcn.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function entityFqcnToShort($entityFqcn)
{
// @codingStandardsIgnoreEnd
    $delimiterPos = strrpos($entityFqcn, '\\');
    $namespace = substr($entityFqcn, 0, $delimiterPos);
    $className = getShortName($entityFqcn);

    // Create bundle name from namespace
    $bundleName = [];
    $namespace = explode('\\', $namespace);
    do {
        $bundleName[] = current($namespace);
    } while ((strpos(current($namespace), 'Bundle') === false) && next($namespace));

    return implode('', $bundleName) .':'. $className;
}

/**
 * Get class name from specified class instance or fqcn.
 *
 * @param string|object $class Class instance or fqcn.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function getShortName($class)
{
// @codingStandardsIgnoreEnd
    if (is_object($class)) {
        $class = get_class($class);
    }

    return substr($class, strrpos($class, '\\') + 1);
}
