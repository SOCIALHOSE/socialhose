<?php

namespace AdminBundle\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;

/**
 * Class AbstractUserType
 * @package AdminBundle\Form\User
 */
abstract class AbstractUserType extends AbstractType
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
            ->add('email', EmailType::class)
            ->add('firstName')
            ->add('lastName')
            ->add('plainPassword', PasswordType::class, [ 'required' => false ])
            ->add('notifyAboutPassword', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Notify the user about password change',
                'required' => false,
                'data' => true,
            ]);
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
            'data_class' => User::class,
            'validation_groups' => [ 'admin_users_creation' ],
        ]);
    }
}
