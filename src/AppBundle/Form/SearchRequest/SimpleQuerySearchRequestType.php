<?php

namespace AppBundle\Form\SearchRequest;

use AppBundle\Form\Type\Filter as QueryFilter;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SimpleQuerySearchRequestType
 * @package AppBundle\Form\SearchRequest
 */
class SimpleQuerySearchRequestType extends AbstractSearchRequestType
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
        $builder
            // Page field, default value - 1
            ->add('page', null, [
                'description' => 'Requested page number. Default value is 1.',
                'empty_data' => 1,
            ]);
        parent::buildForm($builder, $options);
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
        parent::mapFormsToData($forms, $data);

        $forms = iterator_to_array($forms);
        $data->setPage($forms['page']->getData());
    }
}
