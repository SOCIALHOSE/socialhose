<?php

namespace Common\Util\Metadata;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;

/**
 * Class EntityMetadata
 * Contains all entity metadata for matcher.
 *
 * @package Common\Util\Metadata
 */
class EntityMetadata
{

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var array[]
     */
    private $cache = [];

    /**
     * @param Metadata $metadata A Metadata instance.
     */
    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get pattern for specified groups.
     *
     * @param array $groups Serialization groups.
     *
     * @return array
     */
    public function getPattern(array $groups)
    {
        sort($groups);
        $key = serialize($groups);
        if (! isset($this->cache[$key])) {
            $properties = $this->metadata->getProperties($groups);

            $this->cache[$key] = [];

            if ($this->metadata->implementsInterface(EntityInterface::class)) {
                $this->cache[$key]['type'] = '@string@';
            }

            foreach ($properties as $property) {
                $this->cache[$key][$property->getName()] =
                    $this->generateProperty($property, $groups);
            }
        }

        return $this->cache[$key];
    }

    /**
     * Convert from entity metadata type to PHPMatcher type.
     *
     * @param PropertyMetadata $property A PropertyMetadata instance.
     * @param array            $groups   A serialization groups.
     *
     * @return string
     */
    private function generateProperty(PropertyMetadata $property, array $groups)
    {
        $type = $property->getType();

        switch ($type) {
            // Associated object - another entity.
            // Use entity expander with same serialization groups.
            case PropertyMetadata::TYPE_ENTITY:
                $type = \app\c\entityFqcnToShort($property->getActualType());
                $expander = "entity('{$type}', '". implode(',', $groups) ."')";
                $type = "@object@.{$expander}";
                if ($property->isNullable()) {
                    $type = "@wildcard@.oneOf(isEmpty(), {$expander})";
                }
                break;

            // Enum type.
            case PropertyMetadata::TYPE_ENUM:
                $type = '@string@';
                break;

            // Collection of associated entities.
            case PropertyMetadata::TYPE_COLLECTION:
                $type = \app\c\entityFqcnToShort($property->getActualType());
                $reflection = new \ReflectionClass($property->getActualType());
                if ($reflection->isAbstract()) {
                    $type = '@array@';
                } else {
                    $expander = "entity('{$type}', '" . implode(',', $groups) . "')";
                    $type = "@array@.every({$expander})";
                    if ($property->isNullable()) {
                        $type = "@wildcard@.oneOf(isEmpty(), every({$expander}))";
                    }
                }
                break;

            // For double type also use integer type checker.
            case PropertyMetadata::TYPE_DOUBLE:
                $type = "@wildcard@.oneOf(type('integer'), type('double'))";
                if ($property->isNullable()) {
                    $type = "@wildcard@.oneOf(isEmpty(), type('integer'), type('double'))";
                }
                break;

            // For DateTime instances use 'string' matcher with 'isDateTime'
            // expander.
            case PropertyMetadata::TYPE_DATE:
                $type = '@string@.isDateTime()';
                if ($property->isNullable()) {
                    $type = '@wildcard@.oneOf(isEmpty(), isDateTime())';
                }
                break;

            // Process inline objects.
            case PropertyMetadata::TYPE_GROUP:
                $type = '@object@';
                if ($property->isNullable()) {
                    $type = '@wildcard@';
                }
                break;

            // Other types like string, integer and etc.
            default:
                if ($property->isNullable()) {
                    $type = "@wildcard@.oneOf(isEmpty(), type('$type'))";
                } else {
                    $type = '@'. $type .'@';
                }
        }

        return $type;
    }
}
