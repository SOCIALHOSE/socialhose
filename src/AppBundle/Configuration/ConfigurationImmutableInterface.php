<?php

namespace AppBundle\Configuration;

/**
 * Interface ConfigurationImmutableInterface
 * @package AppBundle\Configuration
 */
interface ConfigurationImmutableInterface
{

    /**
     * Get parameter value by name.
     *
     * @param string $name    Parameter name.
     * @param mixed  $default Default value if parameter not found.
     *
     * @return mixed
     */
    public function getParameter($name, $default = null);

    /**
     * Sync current parameters with database.
     *
     * @return void
     */
    public function syncParameters();
}
