<?php

namespace IndexBundle\Fixture\Loader;

/**
 * Interface IndexFixtureLoaderInterface
 * Load fixtures from files.
 *
 * @package IndexBundle\Fixture
 */
interface IndexFixtureLoaderInterface
{

    /**
     * Get all loaded fixtures.
     *
     * @return \IndexBundle\Fixture\IndexFixtureInterface[]
     */
    public function getFixtures();

    /**
     * Load single fixture.
     *
     * @param string $path Path to fixture file.
     *
     * @return void
     */
    public function load($path);

    /**
     * Load fixtures from directory.
     *
     * @param string $path Path to directory.
     *
     * @return void
     */
    public function loadFromDirectory($path);
}
