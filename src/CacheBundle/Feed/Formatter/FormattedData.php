<?php

namespace CacheBundle\Feed\Formatter;

/**
 * Class FormattedData
 *
 * @package CacheBundle\Feed\Formatter
 */
class FormattedData
{

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var string
     */
    private $mime;

    /**
     * FormattedData constructor.
     *
     * @param mixed  $data Formatted data.
     * @param string $mime Data mime type.
     */
    public function __construct($data, $mime)
    {
        $this->data = $data;
        $this->mime = $mime;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }
}
