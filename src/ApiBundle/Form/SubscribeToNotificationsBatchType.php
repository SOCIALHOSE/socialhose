<?php

namespace ApiBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;
use UserBundle\Repository\NotificationRepository;

/**
 * Class SubscribeToNotificationsBatchType
 *
 * @package ApiBundle\Form
 */
class SubscribeToNotificationsBatchType extends EntitiesBatchType
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * SubscribeToNotificationsBatchType constructor.
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('ids')->add('ids', EntityType::class, [
            'class' => Notification::class,
            'multiple' => true,
            'query_builder' => function (NotificationRepository $repository) {
                $user = \app\op\invokeIf($this->storage->getToken(), 'getUser');
                if ($user instanceof User) {
                    return $repository->getQueryBuilderForForm($user);
                }

                return $repository->createQueryBuilder('Notification');
            },
        ]);
        $builder->add('subscribe', CheckboxType::class);
    }
}
