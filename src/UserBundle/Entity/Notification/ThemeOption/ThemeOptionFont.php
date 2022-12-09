<?php

namespace UserBundle\Entity\Notification\ThemeOption;

use UserBundle\Enum\FontFamilyEnum;

/**
 * Class ThemeOptionFont
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionFont implements \Serializable
{

    /**
     * @var FontFamilyEnum
     */
    private $family;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var ThemeOptionFontStyle
     */
    private $style;

    /**
     * ThemeOptionFont constructor.
     *
     * @param FontFamilyEnum       $family Font family name.
     * @param integer              $size   Font size.
     * @param ThemeOptionFontStyle $style  A ThemeOptionFontStyle instance.
     */
    public function __construct(FontFamilyEnum $family, $size, ThemeOptionFontStyle $style = null)
    {
        $this->family = $family;
        $this->size = (int) trim($size);
        $this->style = $style === null ? new ThemeOptionFontStyle() : $style;
    }

    /**
     * @return FontFamilyEnum
     */
    public function getFamily()
    {
        return $this->family;
    }

    /**
     * @param FontFamilyEnum $family Font family name.
     *
     * @return ThemeOptionFont
     */
    public function setFamily(FontFamilyEnum $family)
    {
        $this->family = $family;

        return $this;
    }

    /**
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param integer $size Font size.
     *
     * @return ThemeOptionFont
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return ThemeOptionFontStyle
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param ThemeOptionFontStyle $style A ThemeOptionFontStyle instance.
     *
     * @return ThemeOptionFont
     */
    public function setStyle(ThemeOptionFontStyle $style)
    {
        $this->style = $style;

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
            $this->family,
            $this->size,
            $this->style,
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

        $this->family = $data[0];
        $this->size = $data[1];
        $this->style = $data[2];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'family' => $this->family->getCss(),
            'size' => $this->size,
            'style' => $this->style->toArray(),
        ];
    }
}
