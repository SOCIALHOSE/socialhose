<?php

namespace Common\Util\Matcher;

/**
 * Class AppMatcher
 * Facade for matching process.
 *
 * @package Common\Util\Matcher
 */
class AppMatcher
{

    /**
     * Array of entities metadata.
     * Make static because for some reason ExpanderInitializer from
     * coduo/php-Matcher mark as final and haven't interface, so we can't override
     * or decorate it and pass this entities into it. Therefore we can't pass
     * this map into concrete expander ... :-(
     *
     * @var array
     */
    private static $entities;

    /**
     * @param array $entities Array of entities pattern.
     *
     * @return void
     */
    public static function registerEntities(array $entities = [])
    {
        self::$entities = $entities;
    }

    /**
     * @param string $entityName Entity name.
     *
     * @return \Common\Util\Metadata\EntityMetadata
     */
    public static function getEntityMetadata($entityName)
    {
        return self::$entities[$entityName];
    }

    /**
     * @param string      $value   Checked value.
     * @param string      $pattern Pattern.
     * @param null|string $error   Error.
     *
     * @return boolean
     */
    public static function match($value, $pattern, &$error = null)
    {
        $factory = new MatcherFactory();
        $matcher = $factory->createMatcher();

        if (! $matcher->match($value, $pattern)) {
            $error = $matcher->getError();
            return false;
        }

        return true;
    }
}
