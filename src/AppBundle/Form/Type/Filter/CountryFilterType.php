<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\CountryEnum;
use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CountryFilterType
 * @package AppBundle\Form\Type\Filter
 */
class CountryFilterType extends AbstractFilterType
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
            ->add('include', ChoiceType::class, [
                'choices' => CountryEnum::getAvailables(),
                'multiple' => true,
                'description' => 'Get document within specified countries',
            ])
            ->add('exclude', ChoiceType::class, [
                'choices' => CountryEnum::getAvailables(),
                'multiple' => true,
                'description' => 'Get document not within specified countries',
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
        if (! isset($value['include'], $value['exclude'])) {
            //
            // One of value is not valid, don't make any transformations.
            //
            return null;
        }

        $include = $value['include'];
        $exclude = $value['exclude'];

        $condition = $factory->andX();

        if (count($include) > 0) {
            $condition->add($factory->in(FieldNameEnum::COUNTRY, $include));
        }

        if (count($exclude) > 0) {
            $condition->add($factory->not($factory->in(FieldNameEnum::COUNTRY, $exclude)));
        }

        return $condition;
    }
}
