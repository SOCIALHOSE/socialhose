<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\Count;

/**
 * Class SourceFilterType
 * @package AppBundle\Form\Type\Filter
 */
class SourceFilterType extends AbstractFilterType
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
            ->add('type', ChoiceType::class, [
                'choices' => [ 'include', 'exclude' ],
                'description' => 'Source filter type.',
            ])
            ->add('ids', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'constraints' => new Count([
                    'min' => 1,
                    'minMessage' => 'Expects at least one source title.',
                ]),
                'description' => 'Array of source\'s id\'s',
            ]);
    }

    /**
     * Make some manipulation with form and data before submit.
     *
     * @param FormEvent $event A FormEvent instance.
     *
     * @return void
     */
    protected function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // Remove 'sources' field to avoid unnecessary validation errors if
        // client not provide 'type' or provide invalid.
        if (! isset($data['type'])
            || (($data['type'] !== 'include') && ($data['type'] !== 'exclude'))) {
            $form->remove('ids');
        }
    }

    /**
     * Transform input values into proper filters.
     *
     * @param mixed                  $value   Value to be transformed.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\FilterInterface|null
     */
    protected function transform($value, FilterFactoryInterface $factory)
    {
        //
        // Don't make any transformation if 'type' is not provided or it's
        // invalid or 'sources' not provided or it's not an array.
        //
        // All validation errors will be added in form ValidationListener.
        //
        if (! isset($value['type'], $value['ids'])
            || ! is_array($value['ids'])
            || (($value['type'] !== 'include') && ($value['type'] !== 'exclude'))
        ) {
            return null;
        }

        //
        // $value['sources'] is array of source's id's assigned from index.
        // We use 'source_hashcode' field for it.
        //
        $filter = $factory->in(FieldNameEnum::SOURCE_HASHCODE, $value['ids']);

        return ($value['type'] === 'exclude') ? $factory->not($filter) : $filter;
    }
}
