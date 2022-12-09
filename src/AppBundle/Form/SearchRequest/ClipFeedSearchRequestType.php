<?php

namespace AppBundle\Form\SearchRequest;

use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ClipFeedSearchRequestType
 * @package AppBundle\Form\SearchRequest
 */
class ClipFeedSearchRequestType extends AbstractSearchRequestType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('query')
            ->remove('filters');
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
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'cascade_validation' => true,
            'key' => 'search',
        ]);
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param mixed|SearchRequestBuilderInterface        $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $data->setFilters($forms['advancedFilters']->getData());
    }
}
