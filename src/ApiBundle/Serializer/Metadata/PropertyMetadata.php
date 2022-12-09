<?php

namespace ApiBundle\Serializer\Metadata;

use Doctrine\Common\Collections\Collection;

/**
 * Class PropertyMetadata
 * @package ApiBundle\Serializer\Metadata
 */
class PropertyMetadata
{

    /**
     * Integer scalar value.
     */
    const TYPE_INTEGER = 'integer';

    /**
     * Float (double) scalar value.
     */
    const TYPE_DOUBLE = 'double';

    /**
     * String scalar value.
     */
    const TYPE_STRING = 'string';

    /**
     * Boolean scalar value.
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Array value.
     * Elements maybe other array or any scalar values.
     */
    const TYPE_ARRAY = 'array';

    /**
     * Array of associated entities.
     * Metadata must contains associated entity fqcn in 'actualType' field.
     */
    const TYPE_COLLECTION = 'collection';

    /**
     * Single associated entity.
     * Metadata must contains associated entity fqcn in 'actualType' field.
     */
    const TYPE_ENTITY = 'entity';

    /**
     * One of enum instance.
     *
     * @see \AppBundle\Enum\AbstractEnum
     */
    const TYPE_ENUM = 'enum';

    /**
     * \DateTime instance.
     */
    const TYPE_DATE = 'date';

    /**
     * Some object.
     */
    const TYPE_OBJECT = 'object';

    /**
     * Custom object.
     */
    const TYPE_GROUP = 'group';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * Actual property type, used for object type.
     *
     * @var string
     */
    private $actualType;

    /**
     * @var string
     */
    private $field;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var boolean
     */
    private $nullable = false;

    /**
     * @var PropertyMetadata[]
     */
    private $subProperties = [];

    /**
     * PropertyMetadata constructor.
     *
     * @param string                   $name   Property name.
     * @param string                   $type   Property type.
     * @param \Closure|callable|string $field  Entity field name or function for
     *                                         fetching data.
     * @param array                    $groups Serialized group names.
     */
    public function __construct($name, $type, $field, array $groups)
    {
        $this->name = $name;
        $this->type = $type;
        $this->field = $field;
        $this->groups = array_map('trim', $groups);
    }

    /**
     * Create property metadata for integer field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createInteger($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_INTEGER, $name, $groups);
    }

    /**
     * Create property metadata for string field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createString($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_STRING, $name, $groups);
    }

    /**
     * Create property metadata for string field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createDouble($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_DOUBLE, $name, $groups);
    }

    /**
     * Create property metadata for object field.
     *
     * @param string $name       Property name.
     * @param string $actualType Entity fqcn.
     * @param array  $groups     Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createEntity($name, $actualType, array $groups)
    {
        $property = new PropertyMetadata($name, self::TYPE_ENTITY, $name, $groups);

        return $property->setActualType($actualType);
    }

    /**
     * Create property metadata for string field.
     *
     * @param string $name      Property name.
     * @param string $enumClass Enum class.
     * @param array  $groups    Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createEnum($name, $enumClass, array $groups)
    {
        $property = new PropertyMetadata($name, self::TYPE_ENUM, $name, $groups);

        return $property->setActualType($enumClass);
    }

    /**
     * Create property metadata for array field.
     *
     * @param string $name       Property name.
     * @param string $actualType Entity fqcn.
     * @param array  $groups     Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createCollection($name, $actualType, array $groups)
    {
        $property = new PropertyMetadata($name, self::TYPE_COLLECTION, $name, $groups);

        return $property->setActualType($actualType);
    }

    /**
     * Create property metadata for array field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createArray($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_ARRAY, $name, $groups);
    }

    /**
     * Create property metadata for boolean field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createBoolean($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_BOOLEAN, $name, $groups);
    }

    /**
     * Create property metadata for date field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createDate($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_DATE, $name, $groups);
    }

    /**
     * Create property metadata for date field.
     *
     * @param string $name   Property name.
     * @param array  $groups Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function createObject($name, array $groups)
    {
        return new PropertyMetadata($name, self::TYPE_OBJECT, $name, $groups);
    }

    /**
     * Create property metadata for date field.
     *
     * @param string $name          Property name.
     * @param array  $subProperties Sub properties metadata.
     * @param array  $groups        Serialized group names.
     *
     * @return PropertyMetadata
     */
    public static function groupProperties($name, array $subProperties, array $groups)
    {
        $instance = new PropertyMetadata($name, self::TYPE_GROUP, null, $groups);

        return $instance->setSubProperties($subProperties);
    }

    /**
     * Set name.
     *
     * @param string $name Property name.
     *
     * @return PropertyMetadata
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param string $type Property type.
     *
     * @return PropertyMetadata
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set actual type.
     *
     * @param string $actualType Actual property type.
     *
     * @return PropertyMetadata
     */
    public function setActualType($actualType)
    {
        $this->actualType = $actualType;

        return $this;
    }

    /**
     * Get actual type.
     *
     * @return string
     */
    public function getActualType()
    {
        return $this->actualType;
    }

    /**
     * @return boolean
     */
    public function isScalar()
    {
        return ($this->type !== self::TYPE_COLLECTION)
            && ($this->type !== self::TYPE_ENTITY)
            && ($this->type !== self::TYPE_GROUP)
            && ($this->type !== self::TYPE_OBJECT);
    }

    /**
     * Set field.
     *
     * @param \Closure|callable|string $field Entity field name or function for
     *                                        fetching data.
     *
     * @return PropertyMetadata
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field.
     *
     * @return \Closure|callable|string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param object $object Object instance on with we need getter.
     *
     * @return \Closure
     */
    public function getGetter($object)
    {
        $getter = $this->field;

        if (is_string($getter)) {
            //
            // We got concrete field name.
            // Create getter for it.
            //
            $getter = function () use ($getter) {
                $value = $this->{$getter};

                if ($value instanceof Collection) {
                    // Convert doctrine collection into array.
                    $value = $value->toArray();
                } elseif ($value instanceof \DateTimeInterface) {
                    // Format date time instances.
                    $value = $value->format('c');
                }

                return $value;
            };
        } elseif ($this->type === self::TYPE_GROUP) {
            //
            // For object we should iterate other sub properties and get values
            // from it.
            //
            // We should store current sub properties in order to inject them
            // into closure.
            //
            $subProperties = $this->subProperties;
            $getter = function () use ($object, $subProperties) {
                $results = [];

                foreach ($subProperties as $subProperty) {
                    $getter = $subProperty->getGetter($object);
                    $results[$subProperty->getName()] = $getter();
                }

                return $results;
            };
        }

        // Field is function.
        return $getter->bindTo($object, $object);
    }

    /**
     * Set groups.
     *
     * @param array $groups Serialization groups.
     *
     * @return PropertyMetadata
     */
    public function setGroups(array $groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups.
     *
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set nullable.
     *
     * @param boolean $nullable Can property value be null.
     *
     * @return PropertyMetadata
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Get nullable
     *
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * Set sub properties
     *
     * @param array $properties Array of PropertyMetadata instances.
     *
     * @return PropertyMetadata
     */
    public function setSubProperties(array $properties)
    {
        $this->subProperties = $properties;

        return $this;
    }

    /**
     * Get sub properties
     *
     * @return PropertyMetadata[]
     */
    public function getSubProperties()
    {
        return $this->subProperties;
    }
}
