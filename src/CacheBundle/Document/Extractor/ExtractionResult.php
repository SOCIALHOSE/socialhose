<?php

namespace CacheBundle\Document\Extractor;

/**
 * Class ExtractionResult
 *
 * @package CacheBundle\Document\Extractor
 */
class ExtractionResult
{

    /**
     * @var string
     */
    private $text;

    /**
     * @var integer
     */
    private $start;

    /**
     * @var integer
     */
    private $length;

    /**
     * ExtractionResult constructor.
     *
     * @param string  $text   Extracted text.
     * @param integer $start  The position with which the extraction began.
     * @param integer $length How much extracts.
     */
    public function __construct($text, $start, $length)
    {
        $this->text = $text;
        $this->start = $start;
        $this->length = $length;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }
}
