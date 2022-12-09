<?php

namespace IndexBundle\Util\Initializer;

/**
 * Interface IndexInitializerInterface
 *
 * Initialize index mapping.
 *
 * @package IndexBundle\Util\Initializer
 */
interface IndexInitializerInterface
{

    /**
     * Initialize index mapping.
     *
     * @return void
     */
    public function initializeIndex();
}
