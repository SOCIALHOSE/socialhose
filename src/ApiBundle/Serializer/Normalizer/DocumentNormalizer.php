<?php

namespace ApiBundle\Serializer\Normalizer;

use CacheBundle\Comment\Manager\CommentManagerInterface;
use IndexBundle\Model\AbstractDocument;
use IndexBundle\Model\ArticleDocumentInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class DocumentNormalizer
 * @package ApiBundle\Serializer\Normalizer
 */
class DocumentNormalizer implements NormalizerInterface, NormalizerAwareInterface
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
        return $data instanceof AbstractDocument;
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
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if ($object instanceof ArticleDocumentInterface) {
            $object->addNormalizerListener(function (array $data) use ($format) {
                $data['comments'] = [
                    'data' => $this->normalizer->normalize($data['comments'], $format, [
                        'id',
                        'comment',
                    ]),
                    'count' => count($data['comments']),
                    'totalCount' => $data['commentsCount'],
                    'limit' => CommentManagerInterface::NEW_COMMENT_POOL_SIZE,
                ];
                unset($data['commentsCount']);

                return \nspl\a\map(function ($value) {
                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('c');
                    }

                    return $value;
                }, $data);
            });
        }

        return $object->getNormalizedData();
    }
}
