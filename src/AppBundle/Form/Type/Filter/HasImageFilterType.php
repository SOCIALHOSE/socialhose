<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;

/**
 * Class HasImageFilterType
 *
 * If false we should search documents with images and without.
 * If true we should search documents only with images.
 *
 * @package AppBundle\Form\Type\Filter
 */
class HasImageFilterType extends AbstractFilterType
{

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return CheckboxType::class;
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
        $data = trim($event->getData());

        if (($data !== '0') && ($data !== '') && ($data !== '1')) {
            $event->getForm()->addError(
                new FormError('This value should be of type boolean.')
            );
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
        return $value === true
            ? $factory->eq(FieldNameEnum::IMAGE_SRC, '/.+/')
            : null;
    }
}
