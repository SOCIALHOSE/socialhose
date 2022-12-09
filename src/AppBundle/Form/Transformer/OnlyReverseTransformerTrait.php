<?php

namespace AppBundle\Form\Transformer;

use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Trait OnlyReverseTransformer
 * @package AppBundle\Form\Transformer
 */
trait OnlyReverseTransformerTrait
{

    /**
     * Transforms a value from the original representation to a transformed
     * representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the
     * datasource (object or array).
     * 2. When data from a request is submitted using {@link Form::submit()} to
     * transform the new input data back into the renderable format. For
     * example if you have a date field and submit '2009-10-10' you might
     * accept this value because its easily parsed, but the transformer still
     * writes back
     *    "2009/10/10" onto the form field (for further displaying or other
     *    purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value
     * transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation.
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        return $value;
    }
}
