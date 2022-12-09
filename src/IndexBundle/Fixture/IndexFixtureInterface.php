<?php

namespace IndexBundle\Fixture;

use IndexBundle\Index\IndexInterface;

/**
 * Interface IndexFixtureInterface
 * Interface for index fixtures.
 *
 * @package IndexBundle
 */
interface IndexFixtureInterface
{

    /**
     * Data from fixture should load into external index which looks like hose.
     */
    const INDEX_EXTERNAL = 'external';

    /**
     * Data from fixture should load into internal index which used for indexing
     * cached data.
     */
    const INDEX_INTERNAL = 'internal';

    /**
     * Data from fixture should load into source index which will be used for
     * searching source data.
     */
    const INDEX_SOURCE = 'source';

    /**
     * Load fixtures into index.
     *
     * @param IndexInterface $index A IndexInterface instance.
     *
     * @return void
     */
    public function load(IndexInterface $index);

    /**
     * Return index type for this fixture.
     *
     * @return string
     */
    public function getIndex();
}
