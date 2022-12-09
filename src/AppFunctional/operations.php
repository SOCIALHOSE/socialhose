<?php

namespace app\op;

/**
 * Checks that given object is instance of specified class.
 *
 * @param object $object Checked object.
 * @param string $fqcn   Required class fqcn.
 *
 * @return boolean
 */
// @codingStandardsIgnoreStart
function isInstanceOf($object, $fqcn)
{
// @codingStandardsIgnoreEnd
    return $object instanceof  $fqcn;
}

// @codingStandardsIgnoreStart
const isInstanceOf = '\app\op\isInstanceOf';
// @codingStandardsIgnoreEnd

/**
 * Calls the method named by $methodName on $object. Any extra arguments passed to invoke_if will be
 * forwarded on to the method invocation. If $method is not callable on $object, $defaultValue is returned.
 *
 * @param mixed  $object          A object instance or class.
 * @param string $methodName      Called method name.
 * @param array  $methodArguments Called method argument.
 * @param mixed  $defaultValue    Default value if method not callable.
 *
 * @return mixed
 */
// @codingStandardsIgnoreStart
function invokeIf($object, $methodName, array $methodArguments = [], $defaultValue = null)
{
// @codingStandardsIgnoreEnd
    $callback = array($object, $methodName);
    if (is_callable($callback)) {
        return call_user_func_array($callback, $methodArguments);
    }

    return $defaultValue;
}

/**
 * Get printable type of value.
 *
 * @param mixed $value Some value.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function getPrintableType($value)
{
// @codingStandardsIgnoreEnd
    return is_object($value) ? get_class($value) : gettype($value);
}

/**
 * Convert underscore name to camelCase.
 *
 * @param string $name Some name in underscore format.
 *
 * @return mixed
 */
// @codingStandardsIgnoreStart
function underscoreToCamelCase($name)
{
// @codingStandardsIgnoreEnd
    $name = ucwords(str_replace('_', ' ', strtolower($name)));

    return lcfirst(str_replace(' ', '', $name));
}

/**
 * Convert from camelCase to underscore.
 *
 * @param string $name Some name in camelCase format.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function camelCaseToUnderscore($name)
{
// @codingStandardsIgnoreEnd
    return strtolower(preg_replace('/(?<=\w)[A-Z]/', '_$0', $name));
}
