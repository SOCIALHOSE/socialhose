<?php

namespace Tests\IndexBundle\Model;

use IndexBundle\Model\AbstractDocument;

/**
 * Class TestModel
 * @package Tests\IndexBundle\Model
 *
 * @property integer $first
 * @property string  $second
 * @property array   $third
 */
class TestModel extends AbstractDocument
{

    /**
     * Check that this document is normalized or not.
     *
     * @return boolean
     */
    public function isNormalized()
    {
        return true;
    }
}
