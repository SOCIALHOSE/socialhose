<?php

namespace ApiBundle\Serializer\Normalizer;

use IndexBundle\Model\AbstractDocument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class EmptyObjectNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class EmptyObjectNormalizer implements NormalizerInterface
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
        if (! $data instanceof \stdClass) {
            return false;
        }

        return count(get_object_vars($data)) === 0;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object|AbstractDocument $object  Object to normalize.
     * @param string                  $format  Format the normalization
     *                                         result will be encoded as.
     * @param array                   $context Context options for the
     *                                         normalizer.
     *
     * @return object
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return (object) [];
    }
}
