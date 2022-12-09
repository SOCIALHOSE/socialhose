<?php

namespace CacheBundle\DependencyInjection\Compiler;

use CacheBundle\Feed\Fetcher\Factory\LazyFeedFetcherFactory;
use CacheBundle\Feed\Fetcher\FeedFetcherInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class CollectFeedFetchersCompilerPass
 * @package CacheBundle\DependencyInjection\Compiler
 */
class CollectFeedFetchersCompilerPass implements CompilerPassInterface
{

    const LAZY_FACTORY_ID = 'cache.feed_fetcher_factory.lazy';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance.
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (! $container->has(self::LAZY_FACTORY_ID)) {
            $this->throwException('Lazy factory not registered.');
        }

        $lazyFactory = $container->getDefinition(self::LAZY_FACTORY_ID);
        if ($lazyFactory->getClass() !== LazyFeedFetcherFactory::class) {
            $this->throwException(
                'Invalid factory, expected '. LazyFeedFetcherFactory::class
                .' but got '. $lazyFactory->getClass()
            );
        }

        $fetchers = array_keys($container->findTaggedServiceIds('socialhose.feed_fetcher'));

        $map = [];
        foreach ($fetchers as $id) {
            $class = $container->getDefinition($id)->getClass();
            $reflection = new \ReflectionClass($class);
            if (! $reflection->implementsInterface(FeedFetcherInterface::class)) {
                $this->throwException('');
            }

            $map[$class::support()] = $id;
        }

        $lazyFactory->replaceArgument(1, $map);
    }

    /**
     * @param string $message A additional exception message.
     *
     * @return void
     */
    private function throwException($message = '')
    {
        throw new \RuntimeException('Can\'t register feed fetchers in lazy factory. '. $message);
    }
}
