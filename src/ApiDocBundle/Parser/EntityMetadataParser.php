<?php

namespace ApiDocBundle\Parser;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;
use AppBundle\Enum\AbstractEnum;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Parser\ParserInterface;

/**
 * Class EntityMetadataParser
 * Process entities which implements NormalizableEntityInterface.
 *
 * @package Nelmio\ApiDocBundle\Parser
 */
class EntityMetadataParser implements ParserInterface
{

    /**
     * @var ClassMetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var array
     */
    private $processed = [];

    /**
     * @var array
     */
    private static $typeMap = [
        PropertyMetadata::TYPE_INTEGER => DataTypes::INTEGER,
        PropertyMetadata::TYPE_BOOLEAN => DataTypes::BOOLEAN,
        PropertyMetadata::TYPE_STRING => DataTypes::STRING,
        PropertyMetadata::TYPE_DOUBLE => DataTypes::FLOAT,
        PropertyMetadata::TYPE_ARRAY => DataTypes::COLLECTION,
        PropertyMetadata::TYPE_DATE => DataTypes::DATETIME,
        PropertyMetadata::TYPE_ENTITY => DataTypes::MODEL,
        PropertyMetadata::TYPE_COLLECTION => DataTypes::COLLECTION,
        PropertyMetadata::TYPE_OBJECT => DataTypes::MODEL,
    ];

    /**
     * EntityMetadataParser constructor.
     *
     * @param ClassMetadataFactory $metadataFactory A ClassMetadataFactory
     *                                              instance.
     */
    public function __construct(ClassMetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Return true/false whether this class supports parsing the given class.
     *
     * @param array $item Containing the following fields: class, groups.
     *                    Of which groups is optional.
     *
     * @return boolean
     */
    public function supports(array $item)
    {
        try {
            $className = $item['class'];
            $reflection = new \ReflectionClass($className);
        } catch (\Exception $e) {
            return false;
        }

        return $reflection->implementsInterface(NormalizableEntityInterface::class)
            && isset($item['groups']);
    }

    /**
     * Returns an array of class property metadata where each item is a key (the property name) and
     * an array of data with the following keys:
     *  - dataType          string
     *  - required          boolean
     *  - description       string
     *  - readonly          boolean
     *  - children          (optional) array of nested property names mapped to arrays
     *                      in the format described here
     *  - class             (optional) the fully-qualified class name of the item, if
     *                      it is represented by an object
     *
     * @param array $item The string type of input to parse.
     *
     * @return array
     */
    public function parse(array $item)
    {
        $this->processed = [];

        return $this->process($item['class'], $item['groups']);
    }

    /**
     * Recursively process specified class for given serialization groups.
     *
     * @param string  $class  Entity fqcn.
     * @param array   $groups Serialization groups.
     * @param integer $level  Current nesting level.
     *
     * @return array
     */
    protected function process($class, array $groups, $level = 0)
    {
        // Create new instance of entity in order to get metadata properties.
        $reflection = new \ReflectionClass($class);

        if ($reflection->isAbstract()) {
            // Process abstract class.
            return $this->processAbstractClass($class, $groups, $level);
        }

        // Process concrete class.
        return $this->processClass($class, $groups, $level);
    }

    /**
     * Process founded abstract class.
     * Check that it have discriminator column mapping and process all mapped
     * entity one by one and merge all parsed data in one.
     *
     * @param string  $class  Entity fqcn.
     * @param array   $groups Serialization groups.
     * @param integer $level  Current nesting level.
     *
     * @return array
     */
    protected function processAbstractClass($class, array $groups, $level)
    {
        /** @var ClassMetadataInfo $doctrineMetadata */
        $doctrineMetadata = $this->metadataFactory->getMetadataFor($class);
        $map = $doctrineMetadata->discriminatorMap;

        if (! is_array($map) || (count($map) === 0)) {
            // Parsed abstract class don't has discriminator column.
            $message = 'Abstract class without discriminator column not allowed';
            throw new \InvalidArgumentException($message);
        }

        // Parse each mapped entity.
        $result = [];
        foreach ($map as $fqcn) {
            // We process all mapped entities at the same level where we process
            // current abstract class.
            $result[] = $this->process($fqcn, $groups, $level);
        }

        return call_user_func_array('array_merge', $result);
    }

    /**
     * Process class.
     * Get all serialization metadata for specified groups and prepare proper
     * result structure for api doc.
     *
     * @param string  $class  Entity fqcn.
     * @param array   $groups Serialization groups.
     * @param integer $level  Current nesting level.
     *
     * @return array
     */
    protected function processClass($class, array $groups, $level)
    {
        // Create new instance of entity in order to get metadata properties.
        $reflection = new \ReflectionClass($class);

        /** @var NormalizableEntityInterface $entity */
        $entity = $reflection->newInstanceWithoutConstructor();
        $normalizationMetadata = $entity->getMetadata();

        // Add information about 'type' property if current class is entity class.
        if ($reflection->implementsInterface(EntityInterface::class)) {
            $result['type'] = [
                'dataType' => DataTypes::STRING,
                'actualType' => null,
                'subType' => null,
                'required' => true,
                'readonly' => true,
            ];
        }

        // Get all properties and form in proper api doc structure.
        $result = [];
        $properties = $normalizationMetadata->getProperties($groups);
        foreach ($properties as $property) {
            $result[$property->getName()] = $this
                ->processProperty($property, $groups, $level);
        }

        return $result;
    }

    /**
     * Process concrete property.
     *
     * @param PropertyMetadata $property A PropertyMetadata instance.
     * @param array            $groups   Serialization groups.
     * @param integer          $level    Current nesting level.
     *
     * @return array
     */
    private function processProperty(
        PropertyMetadata $property,
        array $groups,
        $level
    ) {
        // Attach processed data to result.
        $actualType = $property->getActualType();

        if ($actualType !== null) {
            $this->processed[$actualType] = true;
        }

        // Check property type.
        switch ($property->getType()) {
            // Process associated entity and associated entities collection
            // metadata.
            case PropertyMetadata::TYPE_COLLECTION:
            case PropertyMetadata::TYPE_ENTITY:
            case PropertyMetadata::TYPE_OBJECT:
                $processed = $this->processComplexProperty($property, $groups, $level);
                break;

            case PropertyMetadata::TYPE_ENUM:
                /** @var AbstractEnum $class */
                $class = $property->getActualType();

                $processed = [
                    'dataType' => DataTypes::STRING,
                    'required' => ! $property->isNullable(),
                    'readonly' => true,
                    'choices' => $class::getAvailables(),
                ];
                break;

            case PropertyMetadata::TYPE_GROUP:
                $subProperties = $property->getSubProperties();
                $children = [];

                foreach ($subProperties as $subProperty) {
                    $children[$subProperty->getName()] = $this
                        ->processProperty($subProperty, $groups, $level + 1);
                }

                $processed = [
                    'dataType' => DataTypes::MODEL,
                    'required' => ! $property->isNullable(),
                    'readonly' => true,
                    'children' => $children,
                ];
                break;

            // Scalar values.
            default:
                $processed = [
                    'dataType' => self::$typeMap[$property->getType()],
                    'actualType' => null,
                    'subType' => null,
                    'required' => ! $property->isNullable(),
                    'readonly' => true,
                ];
        }

        return $processed;
    }

    /**
     * Process concrete entity or collection property.
     *
     * @param PropertyMetadata $property A PropertyMetadata instance.
     * @param array            $groups   Serialization groups.
     * @param integer          $level    Current nesting level.
     *
     * @return array
     */
    private function processComplexProperty(
        PropertyMetadata $property,
        array $groups,
        $level
    ) {
        $actualType = $property->getActualType();
        $shortName = \app\c\getShortName($property->getActualType());

        switch ($property->getType()) {
            case PropertyMetadata::TYPE_COLLECTION:
                $dataType = 'Collection of '. $shortName;
                break;

            case PropertyMetadata::TYPE_OBJECT:
            case PropertyMetadata::TYPE_ENTITY:
                $dataType = $shortName .' entity';
                break;

            default:
                throw new \InvalidArgumentException('Invalid property type: '. $property->getType());
        }

        // Add main information.
        $processed = [
            'dataType' => $dataType,
            'actualType' => self::$typeMap[$property->getType()],
            'subType' => $property->getActualType(),
            'required' => ! $property->isNullable(),
            'readonly' => true,
        ];

        $supported = $this->supports([
            'class' => $actualType,
            'groups' => $groups,
        ]);
        $canProcess = ! isset($this->processed[$actualType])
            || ($level === 0);

        if ($canProcess && $supported) {
            // Process associated entity only if it not processed
            // already.
            $this->processed[$actualType] = true;
            $processed['children'] = $this
                ->process($actualType, $groups, $level + 1);
        }

        return $processed;
    }
}
