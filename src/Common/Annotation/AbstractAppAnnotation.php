<?php

namespace Common\Annotation;

/**
 * Class AbstractAppAnnotation
 * @package Common\Annotation
 */
abstract class AbstractAppAnnotation implements AppAnnotationInterface
{

    /**
     * AbstractAppAnnotation constructor.
     *
     * @param array $arguments Array of annotation arguments.
     */
    public function __construct(array $arguments = [])
    {
        $defaultName = $this->getDefault();
        if (isset($arguments['value']) && $defaultName) {
            // Set default value if we have it.
            $this->{$defaultName} = $arguments['value'];
            unset($arguments['value']);
        }

        foreach ($arguments as $name => $value) {
            $this->{$name} = $value;
        }

        $this->normalize();
    }

    /**
     * Return name of default property.
     *
     * @return string
     */
    public function getDefault()
    {
        return null;
    }

    /**
     * Normalize annotation parameters.
     * Called after all parameters set in constrictor.
     *
     * @return void
     */
    protected function normalize()
    {
        // Implements in derived class if it necessary.
    }
}
