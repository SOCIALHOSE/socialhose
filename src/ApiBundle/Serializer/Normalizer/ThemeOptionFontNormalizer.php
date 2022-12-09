<?php

namespace ApiBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionFont;

/**
 * Class ThemeOptionFontNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class ThemeOptionFontNormalizer implements
    NormalizerInterface,
    NormalizerAwareInterface
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
        return $data instanceof ThemeOptionFont;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object|ThemeOptionFont $object  Object to normalize.
     * @param string                 $format  Format the normalization result will
     *                                        be encoded as.
     * @param array                  $context Context options for the normalizer.
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'family' => $object->getFamily(),
            'size' => $object->getSize(),
            'style' => $this->normalizer->normalize($object->getStyle()),
        ];
    }
}
