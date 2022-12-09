<?php

namespace Common\Util\Matcher\Expander;

use Common\Util\Matcher\AppMatcher;

/**
 * Class EntityExpander
 * Check that expanded object is serialized application entity.
 *
 * Example: object.entity('AppBundle:User', 'user, post')
 *
 * @package Util\Matcher\Expander
 */
class EntityExpander extends AbstractExpander
{

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var string[]
     */
    private $serializationGroups;

    /**
     * @param string       $entityName          Entity name like
     *                                          'BundleName:EntityName'.
     * @param string|array $serializationGroups Serialization groups in string
     *                                          format delimited by ','.
     */
    public function __construct($entityName, $serializationGroups = [])
    {
        $this->entityName = $entityName;

        // Split serialization groups string into array.
        $serializationGroups = explode(',', $serializationGroups);
        // Trim values and remove empty.
        $serializationGroups = array_filter(
            array_map('trim', $serializationGroups)
        );

        $this->serializationGroups = $serializationGroups;
    }

    /**
     * @param mixed $value Value to match.
     *
     * @return boolean
     */
    public function match($value)
    {
        // Get entity metadata for specified entity.
        $metadata = AppMatcher::getEntityMetadata($this->entityName);
        // Get patter fot specified entity with given serialization group.
        $pattern = $metadata->getPattern($this->serializationGroups);

        if ($pattern && ! AppMatcher::match($value, $pattern, $this->error)) {
            $this->error =
                "Invalid entity {$this->entityName}: {$this->error}";

            return false;
        }

        return true;
    }
}
