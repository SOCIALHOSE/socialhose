<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionColors
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionColors implements \Serializable
{

    /**
     * @var ThemeOptionColorsBackground
     */
    private $background;

    /**
     * @var ThemeOptionColorsText
     */
    private $text;

    /**
     * ThemeOptionColors constructor.
     *
     * @param ThemeOptionColorsBackground $background A ThemeOptionColorsBackground
     *                                                instance.
     * @param ThemeOptionColorsText       $text       A ThemeOptionColorsText
     *                                                instance.
     */
    public function __construct(
        ThemeOptionColorsBackground $background,
        ThemeOptionColorsText $text
    ) {
        $this->background = $background;
        $this->text = $text;
    }

    /**
     * @return ThemeOptionColorsBackground
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param ThemeOptionColorsBackground $background A ThemeOptionColorsBackground
     *                                                instance.
     *
     * @return ThemeOptionColors
     */
    public function setBackground(ThemeOptionColorsBackground $background)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * @return ThemeOptionColorsText
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param ThemeOptionColorsText $text A ThemeOptionColorsText instance.
     *
     * @return ThemeOptionColors
     */
    public function setText(ThemeOptionColorsText $text)
    {
        $this->text = $text;

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
            $this->background,
            $this->text,
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

        $this->background = $data[0];
        $this->text = $data[1];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'background' => $this->background->toArray(),
            'text' => $this->text->toArray(),
        ];
    }
}
