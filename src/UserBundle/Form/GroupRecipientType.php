<?php

namespace UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\User;
use UserBundle\Repository\NotificationRepository;
use UserBundle\Repository\PersonRecipientRepository;

/**
 * Class GroupRecipientType
 * @package UserBundle\Form
 */
class GroupRecipientType extends AbstractType
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * PersonRecipientType constructor.
     *
     * @param TokenStorageInterface $storage A TokenStorageInterface instance.
     */
    public function __construct(TokenStorageInterface $storage)
    {
        $this->storage = $storage;
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
        $user = \app\op\invokeIf($this->storage->getToken(), 'getUser');

        $builder
            ->add('name')
            ->add('description')
            ->add('active', CheckboxType::class)
            ->add('recipients', EntityType::class, [
                'class' => PersonRecipient::class,
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function (PersonRecipientRepository $repository) use ($user) {
                    if ($user instanceof User) {
                        return $repository->getAvailableForUser($user);
                    }

                    return $repository->createQueryBuilder('Person');
                },
            ])
            ->add('notifications', EntityType::class, [
                'class' => Notification::class,
                'multiple' => true,
                'by_reference' => false,
                'query_builder' => function (NotificationRepository $repository) use ($user) {
                    if ($user instanceof User) {
                        return $repository->getQueryBuilderForForm($user);
                    }

                    return $repository->createQueryBuilder('Notification');
                },
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
            'data_class' => GroupRecipient::class,
        ]);
    }
}
