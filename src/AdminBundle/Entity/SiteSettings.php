<?php

namespace AdminBundle\Entity;

use AppBundle\Configuration\ConfigurationParameterInterface;
use AppBundle\Configuration\ConfigurationParameterMutableInterface;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SiteSettings
 *
 * @ORM\Table(name="site_settings")
 * @ORM\Entity
 */
class SiteSettings implements
    ConfigurationParameterInterface,
    ConfigurationParameterMutableInterface,
    EntityInterface
{

    use BaseEntityTrait;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $section;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank
     */
    protected $value;

    /**
     * Set section
     *
     * @param string $section Section name.
     *
     * @return SiteSettings
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * Get section
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set title
     *
     * @param string $title Human readable parameter title.
     *
     * @return SiteSettings
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set name
     *
     * @param string $name Parameter name.
     *
     * @return SiteSettings
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
     * Set value
     *
     * @param mixed $value Parameter value.
     *
     * @return SiteSettings
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'config_parameter';
    }
}
