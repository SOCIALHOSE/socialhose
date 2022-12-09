<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class HeadlineFilterType
 * @package AppBundle\Form\Type\Filter
 */
class HeadlineFilterType extends AbstractFilterType
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
            ->add('include', null, [
                'description' => 'Comma separated list of words which should be in title.',
            ])
            ->add('exclude', null, [
                'description' => 'Comma separated list of words which should not be in title.',
            ]);
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
        if (! isset($value['include']) && !isset($value['exclude'])) {
            //
            // One of value is not valid, don't make any transformations.
            //
            return null;
        }

        $include = array_filter(array_map('trim', explode(',', $value['include'])));
        $exclude = array_filter(array_map('trim', explode(',', $value['exclude'])));

        $condition = $factory->andX();

        if (count($include) > 0) {
            $condition->add($factory->in(FieldNameEnum::TITLE, $include));
        }

        if (count($exclude) > 0) {
            $condition->add($factory->not($factory->in(FieldNameEnum::TITLE, $exclude)));
        }

        return $condition;
    }
}
