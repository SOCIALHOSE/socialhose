<?php

namespace AppBundle\HttpFoundation;

/**
 * Class AppMergeableResponse
 * Response from app api which merge new data instead or resetting.
 *
 * @package AuthenticationBundle\HttpFoundation
 */
class AppMergeableResponse extends AppResponse
{

    /**
     * @var boolean
     */
    protected $isOriginalPriority = false;

    /**
     * @param boolean $flag Flag, if set original data which set in constructor
     *                      or by first call of setData method have higher
     *                      priority than new data.
     *
     * @return AppMergeableResponse
     */
    public function setOriginalPriority($flag)
    {
        $this->isOriginalPriority = $flag;

        return $this;
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data New data.
     *
     * @return AppMergeableResponse
     */
    public function setData($data = [])
    {
        if ($this->isOriginalPriority) {
            $data = $this->data + (array) $data;
        } else {
            $data = (array) $data + $this->data;
        }
        parent::setData($data);

        return $this;
    }
}
