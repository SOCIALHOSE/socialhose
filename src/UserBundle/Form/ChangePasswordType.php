<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Entity\User;

/**
 * Class ChangePasswordType
 * @package UserBundle\Form
 */
class ChangePasswordType extends AbstractType
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * ChangePasswordType constructor.
     *
     * @param UserPasswordEncoderInterface $userPasswordEncoder A UserPasswordEncoderInterface
     *                                                          instance.
     */
    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

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
            ->add('password', PasswordType::class, [
                'trim' => true,
                'property_path' => 'plainPassword',
                'constraints' => new NotBlank(),
            ])
            ->add('oldPassword', PasswordType::class, [
                'trim' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(),
                    new Callback([ $this, 'checkPassword' ]),
                ],
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
        ]);
    }

    /**
     * @param string|null               $value   Old user password.
     * @param ExecutionContextInterface $context A ExecutionContextInterface
     *                                           instance.
     *
     * @return void
     */
    public function checkPassword($value, ExecutionContextInterface $context)
    {
        if ($value === null) {
            return;
        }

        /** @var FormInterface $form */
        $form = $context->getRoot();
        /** @var User $user */
        $user = $form->getData();

        if (! $this->userPasswordEncoder->isPasswordValid($user, $value)) {
            $context->buildViolation('Old password don\'t match to current password')
                ->addViolation();
        }
    }
}
