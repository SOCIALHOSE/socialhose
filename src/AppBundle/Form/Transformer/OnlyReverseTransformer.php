<?php

namespace AppBundle\Form\Transformer;

use Symfony\Component\Form\CallbackTransformer;

/**
 * Class OnlyReverseTransformer
 * @package AppBundle\Form\Transformer
 */
class OnlyReverseTransformer extends CallbackTransformer
{

    /**
     * OnlyReverseTransformer constructor.
     *
     * @param callable $reverseTransform The reverse transform callback.
     */
    public function __construct(callable $reverseTransform)
    {
        parent::__construct(function ($value) {
            return $value;
        }, $reverseTransform);
    }
}
