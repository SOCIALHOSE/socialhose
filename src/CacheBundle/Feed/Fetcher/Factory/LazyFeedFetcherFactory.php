<?php

namespace CacheBundle\Feed\Fetcher\Factory;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Fetcher\FeedFetcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LazyFeedFetcherFactory
 *
 * Return feed fetcher factory.
 *
 * @package CacheBundle\Feed\Fetcher\Factory
 */
class LazyFeedFetcherFactory implements FeedFetcherFactoryInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $map;

    /**
     * LazyFeedFetcherFactory constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     * @param array              $map       Map between feed fqcn and proper
     *                                      fetcher service id.
     */
    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    /**
     * Get feed fetcher for specified feed.
     *
     * @param string|AbstractFeed $feedClass Feed fqcn.
     *
     * @return FeedFetcherInterface
     */
    public function get($feedClass)
    {
        if (is_object($feedClass)) {
            $feedClass = get_class($feedClass);
        }

        if (! is_string($feedClass)) {
            throw new \InvalidArgumentException(
                'Invalid parameter feedClass. Should be string or instance of AbstractFeed.'
            );
        }

        $fetcher = $this->container->get($this->map[$feedClass]);
        if (! $fetcher instanceof FeedFetcherInterface) {
            throw new \RuntimeException('Got invalid fetcher.');
        }

        return $fetcher;
    }
}
