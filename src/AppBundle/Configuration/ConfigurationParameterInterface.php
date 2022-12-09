<?php

namespace AppBundle\Configuration;

/**
 * Interface ConfigurationParameterInterface
 * @package AppBundle\Configuration
 */
interface ConfigurationParameterInterface
{

    /**
     * Get parameter section.
     *
     * @return string
     */
    public function getSection();

    /**
     * Get parameter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get parameter title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get parameter value.
     *
     * @param mixed $value Parameter value.
     *
     * @return ConfigurationParameterInterface
     */
    public function setValue($value);

    /**
     * Get parameter value.
     *
     * @return mixed
     */
    public function getValue();
}
