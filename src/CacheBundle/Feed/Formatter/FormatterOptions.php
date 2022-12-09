<?php

namespace CacheBundle\Feed\Formatter;

use Common\Enum\FormatNameEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class FormatterOptions
 *
 * @package CacheBundle\Feed\Formatter
 */
class FormatterOptions
{

    /**
     * @var FormatNameEnum
     */
    private $format;

    /**
     * @var integer
     */
    private $numberOfDocuments;

    /**
     * @var ThemeOptionExtractEnum
     */
    private $extract;

    /**
     * @var boolean
     */
    private $showImages;

    /**
     * @var boolean
     */
    private $asPlain;

    /**
     * @var boolean
     */
    private $highlight;

    /**
     * FormatterOptions constructor.
     *
     * @param FormatNameEnum              $format            A FormatNameEnum instance.
     * @param integer                     $numberOfDocuments A required number of
     *                                                       documents.
     * @param ThemeOptionExtractEnum|null $extract           A ThemeOptionExtractEnum
     *                                                       instance. No extract
     *                                                       if null.
     * @param boolean                     $showImages        Should fetch image
     *                                                       url or not.
     * @param boolean                     $asPlain           Show content as plain
     *                                                       text.
     * @param boolean                     $highlight         Should highlight
     *                                                       matched keywords or
     *                                                       not.
     */
    public function __construct(
        FormatNameEnum $format,
        $numberOfDocuments = 1,
        ThemeOptionExtractEnum $extract = null,
        $showImages = false,
        $asPlain = false,
        $highlight = false
    ) {
        $this->format = $format;
        $this->numberOfDocuments = $numberOfDocuments;
        $this->extract = $extract ?: ThemeOptionExtractEnum::no();
        $this->showImages = $showImages;
        $this->asPlain = $asPlain;
        $this->highlight = $highlight;
    }

    /**
     * @return FormatNameEnum
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return integer
     */
    public function getNumberOfDocuments()
    {
        return $this->numberOfDocuments;
    }

    /**
     * @return ThemeOptionExtractEnum
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * @return boolean
     */
    public function isShowImages()
    {
        return $this->showImages;
    }

    /**
     * @return boolean
     */
    public function isAsPlain()
    {
        return $this->asPlain;
    }

    /**
     * @return boolean
     */
    public function isHighlight()
    {
        return $this->highlight;
    }
}
