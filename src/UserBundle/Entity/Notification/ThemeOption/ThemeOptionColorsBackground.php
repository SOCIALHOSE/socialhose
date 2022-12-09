<?php

namespace UserBundle\Entity\Notification\ThemeOption;

/**
 * Class ThemeOptionColorsBackground
 * @package UserBundle\Entity\Notification\ThemeOption
 */
class ThemeOptionColorsBackground implements \Serializable
{

    const DEFAULT_HEADER = 'rgba(36, 37, 37, 1)';
    const DEFAULT_EMAIL_BODY = 'rgba(244, 244, 245, 1)';
    const DEFAULT_ACCENT = 'rgba(109, 110, 113, 1)';

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $header;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $emailBody;

    /**
     * CSS RGBA.
     *
     * ```
     * rgba(125, 125, 125, 0.5);
     * ```
     *
     * @var string
     */
    private $accent;

    /**
     * ThemeOptionColorsBackground constructor.
     *
     * @param string $header    Header background color.
     * @param string $emailBody Email body color.
     * @param string $accent    Accent color.
     */
    public function __construct(
        $header = self::DEFAULT_HEADER,
        $emailBody = self::DEFAULT_EMAIL_BODY,
        $accent = self::DEFAULT_ACCENT
    ) {
        $this->header = trim($header);
        $this->emailBody = trim($emailBody);
        $this->accent = trim($accent);
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header Header background color.
     *
     * @return ThemeOptionColorsBackground
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailBody()
    {
        return $this->emailBody;
    }

    /**
     * @param string $emailBody Email body color.
     *
     * @return ThemeOptionColorsBackground
     */
    public function setEmailBody($emailBody)
    {
        $this->emailBody = $emailBody;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccent()
    {
        return $this->accent;
    }

    /**
     * @param string $accent Accent background color.
     *
     * @return ThemeOptionColorsBackground
     */
    public function setAccent($accent)
    {
        $this->accent = $accent;

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
            $this->header,
            $this->emailBody,
            $this->accent,
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

        $this->header = $data[0];
        $this->emailBody = $data[1];
        $this->accent = $data[2];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'header' => $this->header,
            'emailBody' => $this->emailBody,
            'accent' => $this->accent,
        ];
    }
}
