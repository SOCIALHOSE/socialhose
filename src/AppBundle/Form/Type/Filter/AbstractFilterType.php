<?php

namespace AppBundle\Form\Type\Filter;

use AppBundle\Form\Transformer\OnlyReverseTransformer;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractFilterType
 * @package AppBundle\Form\Type\Filter
 */
abstract class AbstractFilterType extends AbstractType
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
        /** @var FilterFactoryInterface $factory */
        $factory = $options['filter_factory'];
        if (! $factory instanceof FilterFactoryInterface) {
            throw new \RuntimeException(sprintf(
                '\'filter_factory\' option should be instance of %s',
                FilterFactoryInterface::class
            ));
        }

        $builder
            ->addModelTransformer(new OnlyReverseTransformer(function ($value) use ($factory) {
                return $this->transform($value, $factory);
            }))
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $this->preSubmit($event);
            });
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
        $resolver
            ->setRequired('filter_factory')
            ->setAllowedTypes('filter_factory', FilterFactoryInterface::class);
    }

    /**
     * Make some manipulation with form and data before submit.
     *
     * @param FormEvent $event A FormEvent instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preSubmit(FormEvent $event)
    {
        // do nothing.
    }

    /**
     * Transform input values into proper filters.
     *
     * @param mixed                  $value   Value to be transformed.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\FilterInterface|null
     */
    abstract protected function transform($value, FilterFactoryInterface $factory);
}
