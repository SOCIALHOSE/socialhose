<?php

namespace IndexBundle\Fixture\Loader;

use IndexBundle\Fixture\IndexFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class IndexFixtureLoader
 * Default implementation of IndexFixtureLoaderInterface.
 *
 * @package IndexBundle\Fixture
 */
class IndexFixtureLoader implements IndexFixtureLoaderInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var IndexFixtureInterface[]
     */
    private $fixtures = [];

    /**
     * IndexFixtureLoader constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get all loaded fixtures.
     *
     * @return \IndexBundle\Fixture\IndexFixtureInterface[]
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * Load single fixture.
     *
     * @param string $path Path to fixture file.
     *
     * @return void
     */
    public function load($path)
    {
        if (! is_readable($path)) {
            throw new \InvalidArgumentException("Can't read file {$path}.");
        }

        $this->loadFromIterator(new \ArrayIterator([ new \SplFileInfo($path) ]));
    }

    /**
     * Load fixtures from directory.
     *
     * @param string $path Path to directory.
     *
     * @return void
     */
    public function loadFromDirectory($path)
    {
        if (! is_dir($path)) {
            throw new \InvalidArgumentException(sprintf("$path does not exist"));
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $this->loadFromIterator($iterator);
    }

    /**
     * Load fixtures from iterator.
     *
     * @param \Iterator $iterator A Iterator instance. Must iterate through
     *                            SplFileInfo.
     *
     * @return void
     */
    private function loadFromIterator(\Iterator $iterator)
    {
        $files = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if ((! $file->isDir()) && ($file->getExtension() === 'php')) {
                $filePath = realpath($file->getPathname());
                require_once $filePath;
                $files[] = $filePath;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $reflection = new \ReflectionClass($className);
            $sourceFile = $reflection->getFileName();

            if (in_array($sourceFile, $files, true) && $this->checkFixture($reflection)) {
                /** @var IndexFixtureInterface $fixture */
                $fixture = new $className();
                if ($fixture instanceof ContainerAwareInterface) {
                    $fixture->setContainer($this->container);
                }

                $this->fixtures[] = $fixture;
            }
        }
    }

    /**
     * @param \ReflectionClass $reflection A ReflectionClass instance.
     *
     * @return boolean True if class represented by specified reflection is
     * valid fixture.
     */
    private function checkFixture(\ReflectionClass $reflection)
    {
        if ($reflection->isAbstract()) {
            return false;
        }

        return in_array(
            IndexFixtureInterface::class,
            $reflection->getInterfaceNames(),
            true
        );
    }
}
