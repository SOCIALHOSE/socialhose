<?php

namespace IndexBundle\Util\Initializer;

use IndexBundle\Index\IndexInterface;

/**
 * Interface AbstractIndexInitializer
 * @package IndexBundle\Util\Initializer
 */
abstract class AbstractIndexInitializer implements IndexInitializerInterface
{

    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * AbstractIndexInitializer constructor.
     *
     * @param IndexInterface $index A IndexInterface instance.
     */
    public function __construct(IndexInterface $index)
    {
        $this->index = $index;
    }

    /**
     * @param IndexInterface $index A IndexInterface instance.
     *
     * @return void
     */
    public static function initialize(IndexInterface $index)
    {
        // @codingStandardsIgnoreStart
        // phpcs says to us that we don't use parentheses when instantiating classes
        // But we do it.
        $instance = new static($index);
        // @codingStandardsIgnoreEnd
        $instance->initializeIndex();
    }
}
