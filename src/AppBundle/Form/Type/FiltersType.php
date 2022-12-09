<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Traits\CleanFormTrait;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FiltersType
 * @package AppBundle\Form\Type
 */
class FiltersType extends AbstractType
{

    use CleanFormTrait;

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
        $availableFilters = array_filter($options['filters']);

        //
        // Add all available filters to form.
        //
        foreach ($availableFilters as $name => $params) {
            $builder->add($name, $params['type'], [
                'filter_factory' => $options['filter_factory'],
                'description' => $params['description'],
            ]);
        }

        /**
         * Normalize filters.
         *
         * @param FormEvent $event A FormEvent instance.
         *
         * @return void
         */
        $postSubmit = function (FormEvent $event) {
            $filters = $event->getData();

            if (count($filters)) {
                $filters = array_values(array_filter($filters));
                $event->setData($filters);
            }
        };

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, [ $this, 'clean' ])
            ->addEventListener(FormEvents::POST_SUBMIT, $postSubmit);
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

        $resolver
            ->setRequired([ 'filter_factory', 'filters' ])
            ->setAllowedTypes('filter_factory', FilterFactoryInterface::class)
            ->setAllowedTypes('filters', 'array')
            ->setDefault('cascade_validation', true);
    }
}
