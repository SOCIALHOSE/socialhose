<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use Common\Enum\LanguageEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LanguageFilterType
 * @package AppBundle\Form\Type\Filter
 */
class LanguageFilterType extends AbstractFilterType
{

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
            'choices' => LanguageEnum::getAvailables(),
            'multiple' => true,
        ]);
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return ChoiceType::class;
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
        if (! is_array($value) || (count($value) === 0)) {
            return null;
        }

        return $factory->in(FieldNameEnum::LANG, $value);
    }
}
