<?php

namespace UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use PaymentBundle\Enum\PaymentGatewayEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Repository\OrganizationRepository;

/**
 * Class HubSpotRegistrationType
 * @package UserBundle\Form
 */
class HubSpotRegistrationType extends AbstractType implements DataMapperInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RegistrationType constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
     * @param array $options The options.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9!#$%&\'*+\/=?^_` {|}~;."-]+@[a-zA-Z0-9!#$%&\'*+\/=?^_` {|}~;."-]+\.[a-zA-Z0-9]+$/',
                        'message' => 'This value is not a valid email address',
                    ]),
                    new Length([
                        'max' => 160,
                        'maxMessage' => 'Email address is too long. It should have {{ limit }} character or less',
                    ]),
                ],
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('companyName')
            ->add('jobFunction')
            ->add('numberOfEmployee')
            ->add('industry')
            ->add('websiteUrl')
//            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
//                $data = $event->getData();
//                $form = $event->getForm();
//            })
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
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param User|null $data Structured data.
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing because it's not necessary method.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param User|null $data Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $data->setEmail($forms['email']->getData());
        $data->setFirstName($forms['firstName']->getData());
        $data->setLastName($forms['lastName']->getData());
        $data->setJobFunction($forms['jobFunction']->getData());
        $data->setCompanyName($forms['companyName']->getData());
        $data->setNumberOfEmployee($forms['numberOfEmployee']->getData());
        $data->setIndustry($forms['industry']->getData());
        $data->setWebsiteUrl($forms['websiteUrl']->getData());
        $data->setPhoneNumber(' ');
        $data->setHubSpot(true);

    }
}

