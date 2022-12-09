<?php

namespace CacheBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SourceToSourceList
 * Many to many relation between source list in database and sources in index.
 *
 * @ORM\Table(name="cross_sources_source_lists")
 * @ORM\Entity
 */
class SourceToSourceList
{

    /**
     * Source id from cache index.
     *
     * @var string|resource
     *
     * @ORM\Id
     * @ORM\Column(type="binary")
     */
    private $source;

    /**
     * @var SourceList
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CacheBundle\Entity\SourceList", inversedBy="sources")
     */
    private $list;

    /**
     * @param string     $source Source id from cache index.
     * @param SourceList $list   A SourceList entity instance.
     *
     * @return SourceToSourceList
     */
    public static function create($source, SourceList $list)
    {
        $instance = new self();

        return $instance
            ->setSource($source)
            ->setList($list);
    }

    /**
     * Set source
     *
     * @param string $source Source id from cache index.
     *
     * @return SourceToSourceList
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        if (! is_string($this->source)) {
            //
            // Because of Doctrine.
            //
            $this->source = stream_get_contents($this->source);
        }

        return $this->source;
    }

    /**
     * Set list
     *
     * @param SourceList $list A SourceList entity instance.
     *
     * @return SourceToSourceList
     */
    public function setList(SourceList $list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return SourceList
     */
    public function getList()
    {
        return $this->list;
    }
}
