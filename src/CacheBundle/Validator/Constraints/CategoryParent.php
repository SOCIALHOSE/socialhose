<?php

namespace CacheBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class CategoryParent
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @package CacheBundle\Validator\Constraints
 */
class CategoryParent extends Constraint
{

    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * Constraint::CLASS_CONSTRAINT and Constraint::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values.
     */
    public function getTargets()
    {
        return [ self::PROPERTY_CONSTRAINT ];
    }
}
