<?php

namespace AdminBundle\Form\User;

use AdminBundle\Form\Type\HiddenEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Repository\UserRepository;

/**
 * Class SubscriberType
 * @package AdminBundle\Form\User
 */
class SubscriberType extends AbstractUserType
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
            ->add('position')
            ->add('phoneNumber', TextType::class, [
                'attr' => [ 'data-type' => 'phone-number' ],
            ]);

        $type = EntityType::class;
        $parameters = [ 'class' => User::class ];
        if ((boolean) $options['show_master_selector']) {
            $parameters['choice_label'] = 'username';
            $parameters['query_builder'] = function (UserRepository $repository) {
                $qb = $repository->getUserByRoleQB(UserRoleEnum::masterUser(), []);
                $alias = $qb->getRootAliases()[0];

                return $qb
                    ->orderBy($alias. '.username', 'DESC');
            };
        } else {
            $type = HiddenEntityType::class;
        }
        $builder->add('masterUser', $type, $parameters);
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
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'validation_groups' => [
                    'admin_users_creation',
                    'admin_subscribers_creation',
                ],
                'show_master_selector' => true,
            ]);
    }
}
