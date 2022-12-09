<?php

namespace AppBundle\Form\Factory;

use AppBundle\Form\AbstractConnectionAwareType;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Class FilterFactoryAwareTypeFactory
 * @package AppBundle\Form\Factory
 */
class FilterFactoryAwareTypeFactory
{

    /**
     * Max documents per page.
     *
     * @var integer
     */
    private $perPage;

    /**
     * @var InternalIndexInterface
     */
    private $index;

    /**
     * SimpleQueryType constructor.
     *
     * @param IndexInterface $index   A IndexInterface interface.
     * @param integer        $perPage Max documents per page.
     */
    public function __construct(
        IndexInterface $index,
        $perPage
    ) {
        $this->index = $index;
        $this->perPage = $perPage;
    }

    /**
     * Create search type instance.
     *
     * @param string $class Concrete form fqcn.
     *
     * @return AbstractConnectionAwareType
     */
    public function create($class)
    {
        $form = new $class($this->index, $this->perPage);

        if (! $form instanceof AbstractConnectionAwareType) {
            $message = 'Invalid form class, expects: '
                . AbstractConnectionAwareType::class;
            throw new \InvalidArgumentException($message);
        }

        return $form;
    }
}
