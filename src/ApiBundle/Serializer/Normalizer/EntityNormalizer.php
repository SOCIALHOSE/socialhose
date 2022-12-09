<?php

namespace ApiBundle\Serializer\Normalizer;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\EntityInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class EntityNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class EntityNormalizer implements NormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;

    /**
     * Checks whether the given class is supportedClass for normalization by this
     * normalizer.
     *
     * @param mixed  $data   Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return boolean
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof NormalizableEntityInterface;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object|NormalizableEntityInterface $object  Object to normalize.
     * @param string                             $format  Format the normalization
     *                                                    result will be encoded as.
     * @param array                              $context Context options for the
     *                                                    normalizer.
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $metadata = $object->getMetadata()->getProperties($context);
        $result = [];

        // If we normalize entity we should add 'type' property to it.
        if ($object instanceof EntityInterface) {
            $result['type'] = $object->getEntityType();
        }

        foreach ($metadata as $property) {
            $getter = $property->getGetter($object);
            $value = $getter();

            //
            // Normalize non scalar value such as:
            //  - Collection of associated entity.
            //  - Single associated entity.
            //  - Array of scalar values. Just in case.
            //
            if (! $property->isScalar()) {
                $value = $this->normalizer->normalize($value, $format, $context);
                if ($property->getType() === PropertyMetadata::TYPE_OBJECT) {
                    $value = (object) $value;
                }
            } elseif ($property->getType() === PropertyMetadata::TYPE_ENUM) {
                $value = (string) $value;
            }

            $result[$property->getName()] = $value;
        }

        return $result;
    }
}
