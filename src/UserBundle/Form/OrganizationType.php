<?php


namespace UserBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\OrganizationSubscription;

/**
 * Class OrganizationType
 * @package UserBundle\Form
 */
class OrganizationType extends AbstractType
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
            ->add('organizationAddress')
            ->add('organizationEmail')
            ->add('organizationPhone')
            ->add('organizationPhone')
            ->add('organization', EntityType::class, array('class' => Organization::class))
            ->add('billingPlanId', EntityType::class, array('class' => Plan::class, 'property_path' => 'plan'));
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
            'data_class' => OrganizationSubscription::class,
        ]);
    }
}
