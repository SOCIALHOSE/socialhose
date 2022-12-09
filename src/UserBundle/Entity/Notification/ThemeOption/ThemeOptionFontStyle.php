<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionFontStyle
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionFontStyle implements \Serializable
{

    /**
     * @var boolean
     */
    private $bold;

    /**
     * @var boolean
     */
    private $italic;

    /**
     * @var boolean
     */
    private $underline;

    /**
     * ThemeOptionFontStyle constructor.
     *
     * @param boolean $bold      Should be text bold or not.
     * @param boolean $italic    Should be text italic or not.
     * @param boolean $underline Should be text underlined or not.
     */
    public function __construct($bold = false, $italic = false, $underline = false)
    {
        $this->bold = (bool) $bold;
        $this->italic = (bool) $italic;
        $this->underline = (bool) $underline;
    }

    /**
     * @return boolean
     */
    public function isBold()
    {
        return $this->bold;
    }

    /**
     * @param boolean $bold Should be text bold or not.
     *
     * @return static
     */
    public function setBold($bold = true)
    {
        $this->bold = $bold;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isItalic()
    {
        return $this->italic;
    }

    /**
     * @param boolean $italic Should be text italic or not.
     *
     * @return static
     */
    public function setItalic($italic = true)
    {
        $this->italic = $italic;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnderline()
    {
        return $this->underline;
    }

    /**
     * @param boolean $underline Should be text underlined or not.
     *
     * @return static
     */
    public function setUnderline($underline = true)
    {
        $this->underline = $underline;

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
            $this->bold,
            $this->italic,
            $this->underline,
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

        $this->bold = $data[0];
        $this->italic = $data[1];
        $this->underline = $data[2];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'bold' => $this->bold,
            'italic' => $this->italic,
            'underline' => $this->underline,
        ];
    }
}
