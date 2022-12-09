<?php

namespace AppBundle\Form;

use IndexBundle\Index\IndexInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractConnectionAwareType
 * @package AppBundle\Form
 */
abstract class AbstractConnectionAwareType extends AbstractType
{

    /**
     * @var IndexInterface
     */
    protected $index;

    /**
     * Max documents per page.
     *
     * @var integer
     */
    protected $perPage;

    /**
     * SimpleQueryType constructor.
     *
     * @param IndexInterface $index   A IndexInterface instance.
     * @param integer        $perPage Max documents per page.
     */
    public function __construct(IndexInterface $index, $perPage)
    {
        $this->index = $index;
        $this->perPage = $perPage;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', SearchRequestBuilderInterface::class);
    }

    /**
     * Create initial search request builder
     *
     * @return \IndexBundle\SearchRequest\SearchRequestBuilderInterface
     */
    protected function createSearchRequestBuilder()
    {
        return $this->index
            ->createRequestBuilder()
            ->setLimit($this->perPage);
    }
}
