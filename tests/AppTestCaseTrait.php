<?php

namespace Tests;

use Common\Util\Matcher\AppMatcher;
use PHPUnit\Framework\TestCase;

/**
 * Trait AppTestCaseTrait
 * @package Tests
 */
trait AppTestCaseTrait
{

    /**
     * Call public, protected or private specified object method.
     *
     * @param object $object Object on which we should call method.
     * @param string $method Called method.
     * @param array  $params Called method parameters.
     *
     * @return mixed
     */
    protected function call($object, $method, array $params = [])
    {
        $methodReflection = new \ReflectionMethod($object, $method);
        $methodReflection->setAccessible(true);

        return $methodReflection->invokeArgs($object, $params);
    }

    /**
     * Get public, protected or private property of specified object.
     *
     * @param object $object   Object from which we should get property.
     * @param string $property Required property name.
     *
     * @return mixed
     */
    protected function getProperty($object, $property)
    {
        $propertyReflection = new \ReflectionProperty($object, $property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    /**
     * Assert that specified value matched to given pattern.
     * Uses coduo/php-matcher.
     *
     * @param mixed  $value   Matched value.
     * @param mixed  $pattern Expected pattern.
     * @param string $message Custom error message.
     *
     * @return void
     */
    protected static function assertMatch($value, $pattern, $message = null)
    {
        self::assertTrue(
            AppMatcher::match($value, $pattern, $error),
            $message ? $message . PHP_EOL . $error : $error
        );
    }

    /**
     * @param string $className Interface fqcn.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockForInterface($className)
    {
        if (! $this instanceof TestCase) {
            throw new \LogicException('AppTestCaseTrait should be used by subclasses od TestCase');
        }

        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods($this->getClassMethods($className))
            ->getMock();
    }

    /**
     * @param string $class A abstract class fqcn.
     *
     * @return string[]
     */
    protected function getClassMethods($class)
    {
        $reflection = new \ReflectionClass($class);

        return \nspl\a\map(\nspl\op\methodCaller('getName'), $reflection->getMethods());
    }
}
