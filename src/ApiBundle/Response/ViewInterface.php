<?php

namespace ApiBundle\Response;

use AppBundle\HttpFoundation\AppResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Interface ViewInterface
 * @package ApiBundle\Response
 */
interface ViewInterface
{

    /**
     * Serialize this response into proper response.
     *
     * @param NormalizerInterface $normalizer A NormalizerInterface instance.
     *
     * @return AppResponse
     */
    public function serialize(NormalizerInterface $normalizer);
}
