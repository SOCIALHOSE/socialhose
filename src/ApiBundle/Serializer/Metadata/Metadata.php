<?php

namespace ApiBundle\Serializer\Metadata;

/**
 * Class Metadata
 * @package ApiBundle\Serializer\Metadata
 */
class Metadata
{

    /**
     * @var string
     */
    private $fqcn;

    /**
     * @var PropertyMetadata[]
     */
    private $properties;

    /**
     * Metadata constructor.
     *
     * @param string $fqcn       Entity fqcn.
     * @param array  $properties Array of PropertyMetadata's.
     */
    public function __construct($fqcn, array $properties = [])
    {
        $this->fqcn = $fqcn;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getFqcn()
    {
        return $this->fqcn;
    }

    /**
     * @param string $interfaceName Full qualified interface name.
     *
     * @return boolean
     */
    public function implementsInterface($interfaceName)
    {
        return in_array($interfaceName, class_implements($this->fqcn), true);
    }

    /**
     * Get all properties metadata or filter by it specified groups if it's set.
     *
     * @param array|string|null $groups A serialized group.
     *
     * @return PropertyMetadata[]
     */
    public function getProperties($groups = null)
    {
        if ($groups === null) {
            return $this->properties;
        }

        $groups = (array) $groups;
        return array_filter(
            $this->properties,
            function (PropertyMetadata $metadata) use ($groups) {
                return count(array_intersect($metadata->getGroups(), $groups)) > 0;
            }
        );
    }

    /**
     * Add to this metadata properties from specified metadata which not exists
     * in this.
     *
     * @param Metadata $metadata A Metadata instance.
     *
     * @return Metadata
     */
    public function admix(Metadata $metadata)
    {
        $registeredProperties = array_map(function (PropertyMetadata $property) {
            return $property->getName();
        }, $this->properties);

        $filter = function (PropertyMetadata $property) use ($registeredProperties) {
            return ! in_array($property->getName(), $registeredProperties, true);
        };

        $uniqueProperties = array_filter($metadata->getProperties(), $filter);
        $this->properties = array_merge($this->properties, $uniqueProperties);

        return $this;
    }

    /**
     * @param array $metadataList Array of Metadata instances.
     *
     * @return Metadata
     */
    public function admixList(array $metadataList)
    {
        /** @var Metadata $metadata */
        foreach ($metadataList as $metadata) {
            $this->admix($metadata);
        }

        return $this;
    }
}
