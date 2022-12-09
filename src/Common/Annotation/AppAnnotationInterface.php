<?php

namespace Common\Annotation;

/**
 * Interface AppAnnotationInterface
 * @package Common\Annotation
 */
interface AppAnnotationInterface
{

    /**
     * Return name of default property.
     *
     * @return string
     */
    public function getDefault();
}
