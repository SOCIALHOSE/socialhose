<?php

namespace AppBundle\Configuration;

/**
 * Interface ConfigurationParameterMutableInterface
 * @package AppBundle\Configuration
 */
interface ConfigurationParameterMutableInterface
{

    /**
     * Set section
     *
     * @param string $section Section name.
     *
     * @return ConfigurationParameterMutableInterface
     */
    public function setSection($section);

    /**
     * Set value
     *
     * @param mixed $value Parameter value.
     *
     * @return ConfigurationParameterMutableInterface
     */
    public function setValue($value);

    /**
     * Set title
     *
     * @param string $title Human readable parameter title.
     *
     * @return ConfigurationParameterMutableInterface
     */
    public function setTitle($title);
}
