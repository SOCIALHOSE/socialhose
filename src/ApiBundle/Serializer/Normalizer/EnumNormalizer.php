<?php

namespace ApiBundle\Serializer\Normalizer;

use AppBundle\Enum\AbstractEnum;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class EnumNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class EnumNormalizer implements NormalizerInterface
{

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
        return $data instanceof AbstractEnum;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object|AbstractEnum $object  Object to normalize.
     * @param string              $format  Format the normalization result will
     *                                     be encoded as.
     * @param array               $context Context options for the normalizer.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $object->getValue();
    }
}
