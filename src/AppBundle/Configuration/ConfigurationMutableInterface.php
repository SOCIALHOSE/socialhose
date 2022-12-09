<?php

namespace AppBundle\Configuration;

/**
 * Interface ConfigurationMutableInterface
 * @package AppBundle\Configuration
 */
interface ConfigurationMutableInterface
{

    /**
     * Get all available parameters.
     *
     * @return ConfigurationParameterInterface[]
     */
    public function getParameters();

    /**
     * Sync parameters with list of available.
     *
     * @return void
     */
    public function syncWithDefinitions();

    /**
     * Set parameter value by name.
     *
     * @param string $name  Parameter name.
     * @param mixed  $value New parameter value.
     *
     * @return void
     */
    public function setParameter($name, $value);

    /**
     * Set parameters.
     *
     * @param array $params Array where key is parameter name and value is new
     *                      value.
     *
     * @return void
     */
    public function setParameters(array $params);

    /**
     * Sync configuration with storage.
     *
     * @return void
     */
    public function sync();
}
