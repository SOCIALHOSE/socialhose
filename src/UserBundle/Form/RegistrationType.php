<?php

namespace UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use PaymentBundle\Enum\PaymentGatewayEnum;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterfacePasswordType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Entity\User;
use CacheBundle\Entity\Category;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Repository\OrganizationRepository;
use Symfony\Component\Form\DataMapperInterface;
/**
 * Class RegistrationType
 * @package UserBundle\Form
 */
class RegistrationType extends AbstractType implements DataMapperInterface
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
     * @param array                $options The options.
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
                    new NotBlank([
                        'message' => 'Email address should not be blank',
                    ]),
                ],
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('companyName')
            ->add('jobFunction')
            ->add('industry')
            ->add('websiteUrl')
            ->add('password',PasswordType::class)
            ->add('numberOfEmployee');

        if (!empty($options['paymentID'])) {
            //->add('phoneNumber')
           $builder->add('searchesPerDay', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Searches Per Day should not be blank',
                    ]),
                ],
            ])
            ->add('savedFeeds', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saved Feeds should not be blank',
                    ]),
                ],
            ])
            ->add('masterAccounts', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Master Accounts should not be blank',
                    ]),
                ],
            ])
            ->add('subscriberAccounts', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Subscriber Accounts should not be blank',
                    ]),
                ],
            ])
            ->add('webFeeds', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Web Feeds should not be blank',
                    ]),
                ],
            ])
            ->add('alerts', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Alerts should not be blank',
                    ]),
                ],
            ])
            ->add('news', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'News should not be blank',
                    ]),
                ],
            ])
            ->add('blog', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Blog should not be blank',
                    ]),
                ],
            ])
            ->add('reddit', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Reddit should not be blank',
                    ]),
                ],
            ])
            ->add('instagram', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Instagram should not be blank',
                    ]),
                ],
            ])
            ->add('twitter', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Twitter should not be blank',
                    ]),
                ],
            ])
            ->add('analytics', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Analytics should not be blank',
                    ]),
                ],
            ])
            ->add('paymentID');
            //->add('billingPlanId', EntityType::class, [ 'class' => Plan::class ])
            // ->add('privatePerson', CheckboxType::class, [ 'mapped' => false ])
            // ->add('organizationName', null, [ 'description' => 'Used only for organization subscription.' ])
            // ->add('organizationAddress', null, [ 'description' => 'Used only for organization subscription.' ])
            // ->add('organizationEmail', null, [ 'description' => 'Used only for organization subscription.' ])
            // ->add('organizationPhone', null, [ 'description' => 'Used only for organization subscription.' ])
            // ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            //     $data = $event->getData();
            //     $form = $event->getForm();
            //     if (isset($data['privatePerson']) && $data['privatePerson']) {
            //         $form
            //             ->remove('organizationName')
            //             ->remove('organizationAddress')
            //             ->remove('organizationEmail')
            //             ->remove('organizationPhone');
            //     }
            // })
        }  

        $builder->setDataMapper($this);
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
            'paymentID' => null
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param User|null                                  $data  Structured data.
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
     * @param User|null                                  $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        // TODO for now it's ok, but used gateway should be selected by users.
        $gateway = PaymentGatewayEnum::paypal();
        //Add plan
        if (isset(
            $forms['news'],
            $forms['blog'],
            $forms['reddit'],
            $forms['instagram'],
            $forms['twitter'],
            $forms['analytics'],
            $forms['searchesPerDay'],
            $forms['savedFeeds'],
            $forms['masterAccounts'],
            $forms['subscriberAccounts'],
            $forms['webFeeds'],
            $forms['alerts'],
            $forms['paymentID']
            ) && 
            ($forms['searchesPerDay']->getData() >= 0) &&
            ($forms['savedFeeds']->getData() >= 0) &&
            ($forms['masterAccounts']->getData() >= 0) &&
            ($forms['subscriberAccounts']->getData() >= 0) &&
            ($forms['webFeeds']->getData() >= 0) &&
            ($forms['alerts']->getData() >= 0)
            ) {
                $plan = Plan::create()
                    ->setTitle($forms['companyName']->getData())
                    ->setInnerName('Starter')
                    ->setPrice(0)
                    ->setNews($forms['news']->getData())
                    ->setBlog($forms['blog']->getData())
                    ->setReddit($forms['reddit']->getData())
                    ->setInstagram($forms['instagram']->getData())
                    ->setTwitter($forms['twitter']->getData())
                    ->setAnalytics($forms['analytics']->getData())
                    ->setSearchesPerDay($forms['searchesPerDay']->getData())
                    ->setSavedFeeds($forms['savedFeeds']->getData())
                    ->setMasterAccounts($forms['masterAccounts']->getData())
                    ->setSubscriberAccounts($forms['subscriberAccounts']->getData())
                    ->setWebFeeds($forms['webFeeds']->getData())
                    ->setAlerts($forms['alerts']->getData());                    
                $this->em->persist($plan);  

                $subscription = OrganizationSubscription::create()
                ->setPlan($plan)
                ->setGateway($gateway)
                ->addUser($data)
                ->setOwner($data);
                
                //Category add default
                $category = new Category($data, 'My Hose');
                $category->setType(Category::TYPE_MY_CONTENT);
                $category = new Category($data, 'Shared Hose');
                $category->setType(Category::TYPE_SHARED_CONTENT);
                $category = new Category($data, 'Deleted Hose');
                $category->setType(Category::TYPE_DELETED_CONTENT);     
        } else if (!isset($forms['paymentID'])) {
            $gateway = PaymentGatewayEnum::free();
            //Get a free plan
            $repository = $this->em->getRepository(Plan::class);
            $planObj = $repository->findOneBy([ 'title' => 'Free' ]);
            $plan = Plan::create()
                    ->setTitle($forms['companyName']->getData())
                    ->setInnerName('Starter')
                    ->setPrice(0)
                    ->setNews($planObj->isNews())
                    ->setBlog($planObj->isBlog())
                    ->setReddit($planObj->isReddit())
                    ->setInstagram($planObj->isInstagram())
                    ->setTwitter($planObj->isTwitter())
                    ->setAnalytics($planObj->isAnalytics())
                    ->setSearchesPerDay($planObj->getSearchesPerDay())
                    ->setSavedFeeds($planObj->getSavedFeeds())
                    ->setMasterAccounts($planObj->getMasterAccounts())
                    ->setSubscriberAccounts($planObj->getSubscriberAccounts())
                    ->setAlerts($planObj->getAlerts())
                    ->setNewsLetters($planObj->getNewsLetters())
                    ->setWebFeeds($planObj->getWebFeeds())
                    ->setAlerts($planObj->getAlerts());
            $this->em->persist($plan);

            $subscription = OrganizationSubscription::create()
            ->setPlan($plan)
            ->setGateway($gateway)
            ->addUser($data)
            ->setOwner($data);
            
            //Category add default
            $category = new Category($data, 'My Hose');
            $category->setType(Category::TYPE_MY_CONTENT);
            $category = new Category($data, 'Shared Hose');
            $category->setType(Category::TYPE_SHARED_CONTENT);
            $category = new Category($data, 'Deleted Hose');
            $category->setType(Category::TYPE_DELETED_CONTENT); 

        }
        // if (isset(
        //     $forms['organizationName'],
        //     $forms['organizationAddress'],
        //     $forms['organizationEmail'],
        //     $forms['organizationPhone']
        //     )) {
        //         //
        //         // Try to find already exists organization.
        //         //
        //         $orgName = $forms['organizationName']->getData();
                
        //         /** @var OrganizationRepository $repository */
        //         $repository = $this->em->getRepository(Organization::class);
        //         $organization = $repository->findOneBy([ 'name' => $orgName ]);
                
        //         if (! $organization instanceof Organization) {
        //             $organization = Organization::create()->setName($orgName);
        //             $data->setRoles([ UserRoleEnum::MASTER_USER ]);
        //             $this->em->persist($organization);
        //         } else {
        //             $data->setRoles([ UserRoleEnum::SUBSCRIBER ]);
        //         }
                
        //         $subscription = OrganizationSubscription::create()
        //             ->setOrganization($organization)
        //             ->setOrganizationAddress($forms['organizationAddress']->getData())
        //             ->setOrganizationEmail($forms['organizationEmail']->getData())
        //             ->setOrganizationPhone($forms['organizationPhone']->getData());
        //     } 
            // else {
            //     $subscription = PersonalSubscription::create();
            //     $data->setRoles([ UserRoleEnum::MASTER_USER ]);
            // }
            
           

            $data->setRoles([ UserRoleEnum::MASTER_USER ]);
            $data->setEmail($forms['email']->getData());
            $data->setFirstName($forms['firstName']->getData());
            $data->setIndustry($forms['industry']->getData());
            $data->setLastName($forms['lastName']->getData());
            $data->setCompanyName($forms['companyName']->getData());
            $data->setJobFunction($forms['jobFunction']->getData());
            $data->setWebsiteUrl($forms['websiteUrl']->getData());
            $data->setNumberOfEmployee($forms['numberOfEmployee']->getData());
            
    }
}
