<?php

namespace UserBundle\Entity\Notification;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * NotificationTheme
 *
 * @ORM\Table(name="notification_themes")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\NotificationThemeRepository")
 */
class NotificationTheme implements EntityInterface, NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * Theme name.
     *
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var NotificationThemeOptions
     *
     * @ORM\Embedded(
     *     class="UserBundle\Entity\Notification\NotificationThemeOptions",
     *     columnPrefix="enhanced_"
     * )
     */
    private $enhanced;

    /**
     * @var NotificationThemeOptions
     *
     * @ORM\Embedded(
     *     class="UserBundle\Entity\Notification\NotificationThemeOptions",
     *     columnPrefix="plain_"
     * )
     */
    private $plain;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="`default`")
     */
    private $default = false;

    /**
     * Set name
     *
     * @param string $name Theme name.
     *
     * @return NotificationTheme
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set enhanced
     *
     * @param NotificationThemeOptions $enhanced A NotificationThemeOptions instance.
     *
     * @return NotificationTheme
     */
    public function setEnhanced(NotificationThemeOptions $enhanced)
    {
        $this->enhanced = $enhanced;

        return $this;
    }

    /**
     * Get enhanced
     *
     * @return NotificationThemeOptions
     */
    public function getEnhanced()
    {
        return $this->enhanced;
    }

    /**
     * Set plain
     *
     * @param NotificationThemeOptions $plain A NotificationThemeOptions instance.
     *
     * @return NotificationTheme
     */
    public function setPlain(NotificationThemeOptions $plain)
    {
        $this->plain = $plain;

        return $this;
    }

    /**
     * Get plainOptions
     *
     * @return NotificationThemeOptions
     */
    public function getPlain()
    {
        return $this->plain;
    }

    /**
     * @param boolean $published Should this notification be published or not.
     *
     * @return NotificationTheme
     */
    public function setPublished($published = true)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Set default
     *
     * @param boolean $default Set theme as default.
     *
     * @return NotificationTheme
     */
    public function setDefault($default = true)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get default
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createString('name', [ 'notification_theme', 'notification_theme_list' ]),
            PropertyMetadata::createBoolean('published', [ 'notification_theme' ]),
            PropertyMetadata::createObject('enhanced', [ 'notification_theme' ])
                ->setField('enhanced')
                ->setActualType(NotificationThemeOptions::class),
            PropertyMetadata::createObject('plain', [ 'notification_theme' ])
                ->setField('plain')
                ->setActualType(NotificationThemeOptions::class),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'notification_theme' ];
    }
}
