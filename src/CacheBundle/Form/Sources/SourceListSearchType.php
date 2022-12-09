<?php

namespace CacheBundle\Form\Sources;

use AppBundle\Form\Transformer\OnlyReverseTransformerTrait;
use CacheBundle\Form\Sources\Type\SortType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SourceListSearchType
 * @package CacheBundle\Form\Sources
 */
class SourceListSearchType extends AbstractType implements DataTransformerInterface
{

    use OnlyReverseTransformerTrait;

    public static $fields = [
        'name' => 'name',
        'sources' => 'sourceNumber',
        'createdBy' => 'user',
        'lastUpdated' => 'updatedAt',
        'lastUpdatedBy' => 'updatedBy',
    ];

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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page', null, [
                'description' => 'Requested page number, should start from 1. Default value 1.',
                'empty_data' => 1,
            ])
            ->add('limit', null, [
                'description' => 'Max sources per page. Default 20.',
                'empty_data' => 20,
            ])
            ->add('sort', SortType::class, [
                'fields' => self::$fields,
                'default_field' => 'name',
                'default_direction' => 'asc',
            ])
            ->add('onlyShared', CheckboxType::class, [
                'description' => 'Show only shared source lists.',
                'required' => false,
            ])
            ->addModelTransformer($this);
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
        $resolver->setDefault('key', 'searchSourceList');
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform
     * the requests tainted data into an acceptable format for your data
     * processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $data The value in the transformed representation.
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($data)
    {
        if (count($data['sort']) === 0) {
            $data['sort'] = [ 'name' => 'asc' ];
        }

        if (! isset($data['onlyShared'])) {
            $data['onlyShared'] = false;
        }

        return $data;
    }
}
