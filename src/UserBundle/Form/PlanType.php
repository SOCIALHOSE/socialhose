<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\Plan;

/**
 * Class PlanType
 * @package UserBundle\Form
 */
class PlanType extends AbstractType
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [
                'constraints' => new NotBlank(),
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'USD',
            ])
            ->add('searchesPerDay', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('savedFeeds', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('masterAccounts', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('subscriberAccounts', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('alerts', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('newsletters', IntegerType::class, [ 'attr' => [ 'min' => 0 ] ])
            ->add('analytics', CheckboxType::class, [ 'required' => false ]);
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
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
