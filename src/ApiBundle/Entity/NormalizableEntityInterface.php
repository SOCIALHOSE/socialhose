<?php

namespace ApiBundle\Entity;

/**
 * Interface NormalizableEntityInterface
 * @package ApiBundle\Entity
 */
interface NormalizableEntityInterface
{

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata();

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups();
}
