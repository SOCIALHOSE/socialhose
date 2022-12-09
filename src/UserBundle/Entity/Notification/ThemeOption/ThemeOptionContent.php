<?php

namespace UserBundle\Entity\Notification\ThemeOption;

use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class ThemeOptionContent
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionContent implements \Serializable
{

    /**
     * @var ThemeOptionHighlightKeywords
     */
    private $highlightKeywords;

    /**
     * @var ThemeOptionShowInfo
     */
    private $showInfo;

    /**
     * @var string
     */
    private $language;

    /**
     * @var ThemeOptionExtractEnum
     */
    private $extract;

    /**
     * ThemeOptionContent constructor.
     *
     * @param ThemeOptionHighlightKeywords $highlightKeywords A ThemeOptionHighlightKeywords
     *                                                        instance.
     * @param ThemeOptionShowInfo          $showInfo          A ThemeOptionShowInfo
     *                                                        instance.
     * @param string                       $language          Selected notification
     *                                                        language.
     * @param ThemeOptionExtractEnum       $extract           A ThemeOptionExtractEnum
     *                                                        instance.
     */
    public function __construct(
        ThemeOptionHighlightKeywords $highlightKeywords,
        ThemeOptionShowInfo $showInfo,
        $language,
        ThemeOptionExtractEnum $extract
    ) {
        $this->highlightKeywords = $highlightKeywords;
        $this->showInfo = $showInfo;
        $this->language = trim($language);
        $this->extract = $extract;
    }

    /**
     * @return ThemeOptionHighlightKeywords
     */
    public function getHighlightKeywords()
    {
        return $this->highlightKeywords;
    }

    /**
     * @param ThemeOptionHighlightKeywords $highlightKeywords A ThemeOptionHighlightKeywords
     *                                                        instance.
     *
     * @return ThemeOptionContent
     */
    public function setHighlightKeywords(ThemeOptionHighlightKeywords $highlightKeywords)
    {
        $this->highlightKeywords = $highlightKeywords;

        return $this;
    }

    /**
     * @return ThemeOptionShowInfo
     */
    public function getShowInfo()
    {
        return $this->showInfo;
    }

    /**
     * @param ThemeOptionShowInfo $showInfo A ThemeOptionShowInfo instance.
     *
     * @return ThemeOptionContent
     */
    public function setShowInfo(ThemeOptionShowInfo $showInfo)
    {
        $this->showInfo = $showInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language Selected theme language.
     *
     * @return ThemeOptionContent
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return ThemeOptionExtractEnum
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * @param ThemeOptionExtractEnum $extract A ThemeOptionExtractEnum
     *                                        instance.
     *
     * @return ThemeOptionContent
     */
    public function setExtract(ThemeOptionExtractEnum $extract)
    {
        $this->extract = $extract;

        return $this;
    }

    /**
     * String representation of object.
     *
     * @return string the string representation of the object or null.
     */
    public function serialize()
    {
        return serialize([
            $this->highlightKeywords,
            $this->showInfo,
            $this->language,
            $this->extract,
        ]);
    }

    /**
     * Constructs the object
     *
     * @param string $serialized The string representation of the object.
     *
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->highlightKeywords = $data[0];
        $this->showInfo = $data[1];
        $this->language = $data[2];
        $this->extract = $data[3];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'highlightKeywords' => $this->highlightKeywords->toArray(),
            'showInfo' => $this->showInfo->toArray(),
            'language' => $this->language,
            'extract' => $this->extract->getValue(),
        ];
    }
}
