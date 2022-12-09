<?php

namespace ApiBundle\Response;

use AppBundle\HttpFoundation\AppResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class View
 * Abstract under controller results and api response.
 *
 * @package ApiBundle\Response
 */
class View implements ViewInterface
{

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string[]
     */
    private $groups;

    /**
     * @var integer
     */
    private $code;

    /**
     * View constructor.
     *
     * @param mixed   $data   Response data.
     * @param array   $groups Serialization groups.
     * @param integer $code   HTTP status code.
     */
    public function __construct(
        $data,
        array $groups = [],
        $code = null
    ) {
        if (($data === null) && ($code === null)) {
            $code = AppResponse::HTTP_NO_CONTENT;
        }

        $this->data = $data;
        $this->groups = $groups;
        $this->code = $code ?: AppResponse::HTTP_OK;
    }

    /**
     * Serialize this response into proper response.
     *
     * @param NormalizerInterface $normalizer A NormalizerInterface instance.
     *
     * @return AppResponse
     */
    public function serialize(NormalizerInterface $normalizer)
    {
        if (($this->data === null)
            || (is_array($this->data) && (count($this->data) === 0))) {
            // We got empty response, send without serialization.
            return AppResponse::create(null, $this->code);
        }

        if (is_array($this->data) || is_object($this->data)) {
            //
            // TODO: refactor it. Low priority.
            //
            if (($this->code >= 400) && ! is_array($this->data)
                && (! $this->data instanceof FormInterface)) {
                $this->data = [ $this->data ];
            }

            return AppResponse::create(
                $normalizer->normalize($this->data, null, $this->groups),
                $this->code
            );
        }

        // Scalar values we just return.
        return AppResponse::create($this->data, $this->code);
    }
}
