<?php

namespace ApiBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ActivatedEntitiesBatchType
 *
 * @package ApiBundle\Form
 */
class ActivatedEntitiesBatchType extends EntitiesBatchType
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
        $builder->add('active', CheckboxType::class);
    }
}
