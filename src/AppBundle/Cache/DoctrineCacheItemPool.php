<?php

namespace AppBundle\Cache;

use AppBundle\Entity\CacheItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class DoctrineCacheItemPool
 *
 * @package AppBundle\Cache
 */
class DoctrineCacheItemPool implements CacheItemPoolInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var object[]
     */
    private $deferred = [];

    /**
     * DoctrineCacheItemPool constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case
     * of a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     *
     * @return CacheItemInterface
     */
    public function getItem($key)
    {
        $item = $this->em->find(CacheItem::class, $key);
        if (! $item instanceof CacheItem) {
            $item = new CacheItem($key, null, null, false);
        } elseif (time() > $item->getExpiresAt()) {
            $this->em->remove($item);
            $this->em->flush($item);

            $item = new CacheItem($key, null, null, false);
        }

        $this->garbageCollector();

        return $item;
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param string[] $keys An indexed array of keys of items to retrieve.
     *
     * @return array|\Traversable
     */
    public function getItems(array $keys = [])
    {
        $items = $this->em->getRepository(CacheItem::class)
            ->findBy([ 'key' => $keys ]);

        $this->garbageCollector();

        //
        // todo add proper code here!
        //

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance
     * reasons. This could result in a race condition with
     * CacheItemInterface::get(). To avoid such situation use
     * CacheItemInterface::isHit() instead.
     *
     * @param string $key The key for which to check existence.
     *
     * @return boolean
     */
    public function hasItem($key)
    {
        return $this->getItem($key)->isHit();
    }

    /**
     * Deletes all items in the pool.
     *
     * @return boolean
     */
    public function clear()
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository(CacheItem::class);

        $repository->createQueryBuilder('Item')
            ->delete()
            ->getQuery()
            ->execute();

        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key The key to delete.
     *
     * @return boolean
     */
    public function deleteItem($key)
    {
        $this->garbageCollector();

        return $this->deleteItems([ $key ]);
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param string[] $keys An array of keys that should be removed from the pool.
     *
     * @return boolean
     */
    public function deleteItems(array $keys)
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository(CacheItem::class);

        $repository->createQueryBuilder('Item')
            ->delete()
            ->where('Item.key IN ('. implode(', ', \nspl\a\map(function ($key) {
                return "'{$key}'";
            }, $keys)) .')')
            ->getQuery()
            ->execute();

        $this->garbageCollector();

        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return boolean
     */
    public function save(CacheItemInterface $item)
    {
        if (! $item instanceof CacheItem) {
            return false;
        }

        $this->em->persist($item);
        $this->em->flush($item);

        $this->garbageCollector();

        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return boolean
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return boolean
     */
    public function commit()
    {
        foreach ($this->deferred as $item) {
            $this->em->persist($item);
        }
        $this->em->flush($this->deferred);
        $this->deferred = [];

        $this->garbageCollector();

        return true;
    }

    /**
     * @return void
     */
    private function garbageCollector()
    {
        if (mt_rand(0, 10) <= 1) {
            $this->em->createQueryBuilder()
                ->delete()
                ->from(CacheItem::class, 'Item')
                ->where('Item.expiresAt < CURRENT_TIMESTAMP()')
                ->getQuery()
                ->execute();
        }
    }
}
