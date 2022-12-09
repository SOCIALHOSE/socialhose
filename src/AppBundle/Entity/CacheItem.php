<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Psr\Cache\CacheItemInterface;

/**
 * Class CacheItem
 *
 * @ORM\Table(name="cache_items")
 * @ORM\Entity
 *
 * @package AppBundle\Entity
 */
class CacheItem implements CacheItemInterface
{

    /**
     * @var string
     *
     * @ORM\Column(name="`key`")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $key;

    /**
     * @var mixed
     *
     * @ORM\Column(type="json_array")
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $lifetime;

    /**
     * @var integer
     *
     * @ORM\Column(type="bigint")
     */
    private $expiresAt;

    /**
     * @var boolean
     */
    private $isHit = true;

    /**
     * CacheItem constructor.
     *
     * @param string  $key      Cache item key.
     * @param mixed   $value    Cached value.
     * @param integer $lifetime Value lifetime in seconds.
     * @param boolean $isHit    Is item fetched.
     */
    public function __construct(
        $key = null,
        $value = null,
        $lifetime = null,
        $isHit = true
    ) {
        $this->key = $key;
        $this->value = $value;
        $this->lifetime = $lifetime;
        $this->expiresAt = time() + $lifetime;
        $this->isHit = $isHit;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key Item key.
     *
     * @return CacheItem
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value A cached value.
     *
     * @return CacheItem
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Retrieves the value of the item from the cache associated with this
     * object's key.
     *
     * The value returned must be identical to the value originally stored by
     * set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not
     *   found.
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value The value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return boolean
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        return $this->isHit;
    }

    /**
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param integer $lifetime How long this item is valid in seconds.
     *
     * @return CacheItem
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * @return integer
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface|null $expiration The point in time after which
     *                                            the item MUST be considered expired.
     *                                            If null is passed explicitly,
     *                                            a default value MAY be used.
     *                                            If none is set, the value should
     *                                            be stored permanently or for as
     *                                            long as the implementation allows.
     *
     * @return static
     */
    public function expiresAt($expiration)
    {
        if (null === $expiration) {
            $this->expiresAt = $this->lifetime > 0 ? time() + $this->lifetime : null;
        } elseif ($expiration instanceof \DateTimeInterface) {
            $this->expiresAt = (int) $expiration->format('U');
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expiration date must implement DateTimeInterface or be null, "%s" given',
                is_object($expiration) ? get_class($expiration) : gettype($expiration)
            ));
        }

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param integer|\DateInterval|null $time The period of time from the present
     *                                         after which the item MUST be considered
     *                                         expired. An integer parameter is
     *                                         understood to be the time in seconds
     *                                         until expiration. If null is passed
     *                                         explicitly, a default value MAY be
     *                                         used. If none is set, the value
     *                                         should be stored permanently or for
     *                                         as long as the implementation allows.
     *
     * @return static
     */
    public function expiresAfter($time)
    {
        if (null === $time) {
            $this->expiresAt = $this->lifetime > 0 ? time() + $this->lifetime : null;
        } elseif ($time instanceof \DateInterval) {
            $this->expiresAt = (int) \DateTime::createFromFormat('U', time())->add($time)->format('U');
        } elseif (is_int($time)) {
            $this->expiresAt = $time + time();
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expiration date must be an integer, a DateInterval or null, "%s" given',
                is_object($time) ? get_class($time) : gettype($time)
            ));
        }

        return $this;
    }
}
