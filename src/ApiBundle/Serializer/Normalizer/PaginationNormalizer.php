<?php

namespace ApiBundle\Serializer\Normalizer;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class PaginationNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class PaginationNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        return ($data instanceof SlidingPagination) || ($data instanceof Paginator);
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object  Object to normalize.
     * @param string $format  Format the normalization result
     *                        will be encoded as.
     * @param array  $context Context options for the normalizer.
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if ($object instanceof Paginator) {
            $data = iterator_to_array($object);

            $normalizedData = [
                'data' => $this->normalizer->normalize($data, $format, $context),
                'count' => count($data),
                'totalCount' => count($object),
                'limit' => $object->getQuery()->getMaxResults(),
            ];
        } elseif ($object instanceof SlidingPagination) {
            $normalizedData = [
                'data' => $this->normalizer
                    ->normalize(iterator_to_array($object), $format, $context),
                'count' => count($object),
                'totalCount' => $object->getTotalItemCount(),
                'page' => $object->getCurrentPageNumber(),
                'limit' => $object->getItemNumberPerPage(),
            ];
        } else {
            throw new \InvalidArgumentException('Expect one of paginator.');
        }

        return $normalizedData;
    }
}
