<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionHighlightKeywords
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionHighlightKeywords implements \Serializable
{

    /**
     * @var boolean
     */
    private $highlight;

    /**
     * @var boolean
     */
    private $bold;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $color;

    /**
     * ThemeOptionHighlightKeywords constructor.
     *
     * @param boolean $highlight Should system highlight keywords or not.
     * @param boolean $bold      Should highlight keyword be bold or not.
     * @param string  $color     Highlight color.
     */
    public function __construct($highlight = true, $bold = true, $color = 'rgba(255, 255, 0, 1)')
    {
        $this->highlight = (bool) $highlight;
        $this->bold = (bool) $bold;
        $this->color = trim($color);
    }

    /**
     * @return boolean
     */
    public function isHighlight()
    {
        return $this->highlight;
    }

    /**
     * @param boolean $highlight Should system highlight keywords or not.
     *
     * @return ThemeOptionHighlightKeywords
     */
    public function setHighlight($highlight = true)
    {
        $this->highlight = $highlight;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBold()
    {
        return $this->bold;
    }

    /**
     * @param boolean $bold Should highlight keyword be bold or not.
     *
     * @return ThemeOptionHighlightKeywords
     */
    public function setBold($bold = true)
    {
        $this->bold = $bold;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color Highlight color.
     *
     * @return ThemeOptionHighlightKeywords
     */
    public function setColor($color)
    {
        $this->color = $color;

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
            $this->highlight,
            $this->bold,
            $this->color,
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

        $this->highlight = $data[0];
        $this->bold = $data[1];
        $this->color = $data[2];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'highlight' => $this->highlight,
            'bold' => $this->bold,
            'color' => $this->color,
        ];
    }
}
