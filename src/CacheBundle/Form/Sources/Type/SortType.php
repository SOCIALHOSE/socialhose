<?php

namespace CacheBundle\Form\Sources\Type;

use AppBundle\Form\Transformer\OnlyReverseTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SortType
 * @package CacheBundle\Form\Sources\Type
 */
class SortType extends AbstractType
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
        $fields = $options['fields'];

        $builder
            ->add('field', ChoiceType::class, [
                'description' => 'Field name on which we should sort.',
                'choices' => array_keys($options['fields']),
                'empty_data' => $options['default_field'],
            ])
            ->add('direction', ChoiceType::class, [
                'description' => 'Sorting direction.',
                'choices' => [ 'asc', 'desc' ],
                'empty_data' => $options['default_direction'],
            ]);

        $transformer = new OnlyReverseTransformer(function (array $sortObject) use ($fields) {
            if (! is_array($sortObject)) {
                throw new TransformationFailedException('Expect array got '. gettype($sortObject));
            }

            if (! isset($sortObject['field'], $sortObject['direction'])) {
                return [];
            }

            return [ $fields[$sortObject['field']] => $sortObject['direction'] ];
        });

        $builder->addModelTransformer($transformer);
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
            ->setRequired('fields')
            ->setDefined('default_field')
            ->setDefined('default_direction')
            ->setAllowedTypes('fields', 'array');
    }
}
