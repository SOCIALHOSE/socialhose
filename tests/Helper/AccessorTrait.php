<?php

namespace Tests\Helper;

/**
 * Class AccessorTrait
 * @package Tests\Helper
 */
trait AccessorTrait
{

    /**
     * Call specified method of given object.
     *
     * @param object $object    Some object.
     * @param string $method    Called method name.
     * @param array  $arguments Method parameters.
     *
     * @return mixed
     */
    public function call($object, $method, array $arguments = [])
    {
        $caller = function () use ($method, $arguments) {
            return call_user_func_array([ $this, $method ], $arguments);
        };
        $caller = $caller->bindTo($object, $object);

        return $caller();
    }
}
