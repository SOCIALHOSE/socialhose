<?php

namespace ApiBundle\Controller\Annotation;

use Common\Annotation\AbstractAppAnnotation;

/**
 * Class Roles
 * @package ApiBundle\Controller\Annotation
 *
 * @Annotation
 */
class Roles extends AbstractAppAnnotation
{

    /**
     * Expected role or array of roles.
     *
     * @var array
     */
    public $roles;

    /**
     * Return name of default property.
     *
     * @return string
     */
    public function getDefault()
    {
        return 'roles';
    }

    /**
     * Normalize annotation parameters.
     * Called after all parameters set in constrictor.
     *
     * @return void
     */
    protected function normalize()
    {
        $this->roles = (array) $this->roles;
    }
}
