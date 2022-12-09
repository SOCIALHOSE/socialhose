<?php

namespace ApiBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EntitiesBatchType
 *
 * Base form type for batch processing form types.
 *
 * @package ApiBundle\Form
 */
class EntitiesBatchType extends AbstractType implements DataMapperInterface
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
        $builder
            ->add('ids', EntityType::class, [
                'class' => $options['class'],
                'multiple' => true,
            ])
            ->setDataMapper($this);
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
        $resolver->setRequired('class');
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed                 $data  Structured data.
     * @param FormInterface[]|array $forms A list of {@link FormInterface} instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing because it's not necessary.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Traversable $forms A list of {@link FormInterface}
     *                                            instances.
     * @param mixed                        $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $data = [];
        $data['entities'] = $forms['ids']->getData();
        unset($forms['ids']);

        foreach ($forms as $form) {
            $data[$form->getName()] = $form->getData();
        }
    }
}
