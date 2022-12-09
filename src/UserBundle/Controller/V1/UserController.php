<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use AppBundle\Controller\Traits\FormFactoryAwareTrait;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Controller\V1\AbstractV1Controller;
use FOS\UserBundle\Model\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Form\ChangePasswordType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PaymentBundle\Enum\PaymentGatewayEnum;
use UserBundle\Entity\Subscription\OrganizationSubscription;  
use Stripe\Exception\ApiErrorException;  
use UserBundle\Entity\Plan;

/**
 * Class UserController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/users",
 *     service="user.controller.user"
 * )
 */
class UserController extends AbstractV1Controller
{

    use
        TokenStorageAwareTrait,
        FormFactoryAwareTrait;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * UserController constructor.
     *
     * @param TokenStorageInterface $tokenStorage A TokenStorageInterface
     *                                            instance.
     * @param FormFactoryInterface  $formFactory  A FormFactoryInterface instance.
     * @param UserManagerInterface  $userManager  A UserManagerInterface instance.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        UserManagerInterface $userManager,
        ContainerInterface $container
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->container = $container;
    }

    /**
     * Change password for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/change-password", methods={ "POST" })
     * @ApiDoc(
     *  resource="Security",
     *  section="User",
     *  input={
     *     "class"="UserBundle\Form\ChangePasswordType",
     *     "name"=false
     *  }
     * )
     *
     * @param Request $request A Http Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $form = $this
            ->createForm(ChangePasswordType::class, $user)
            ->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userManager->updateUser($user);

            return $this->generateResponse();
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Get list of subscriber for current master.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/current/restrictions", methods={ "GET" })
     * @ApiDoc(
     *  resource="Current",
     *  section="User",
     *  output={
     *     "class"="",
     *     "data"={
     *      "limits"={
     *       "dataType"="object",
     *       "required"=true,
     *       "readonly"=true,
     *       "children"={
     *         "searchesPerDay"={
     *          "dataType"="object",
     *          "description"="Searches per day limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed searches count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used searches count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         },
     *         "savedFeeds"={
     *          "dataType"="object",
     *          "description"="Feeds limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed feeds count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used feeds count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         },
     *         "masterAccounts"={
     *          "dataType"="object",
     *          "description"="Master accounts limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed master accounts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used master accounts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         },
     *         "subscriberAccounts"={
     *          "dataType"="object",
     *          "description"="Subscriber accounts limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed subscriber accounts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used subscriber accounts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         },
     *         "alerts"={
     *          "dataType"="object",
     *          "description"="Alerts limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed alerts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used alerts count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         },
     *         "newsletters"={
     *          "dataType"="object",
     *          "description"="Newsletters limits",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *              "limit"={
     *                  "dataType"="integer",
     *                  "description"="Allowed newsletters count",
     *                  "required"=true,
     *                  "readonly"=true
     *              },
     *              "current"={
     *                  "dataType"="integer",
     *                  "description"="Used newsletters count",
     *                  "required"=true,
     *                  "readonly"=true
     *              }
     *          }
     *         }
     *
     *       }
     *     },
     *     "permissions"={
     *       "dataType"="object",
     *       "required"=true,
     *       "readonly"=true,
     *       "children"={
     *         "analytics"={
     *          "dataType"="boolean",
     *          "description"="Can user use analytics or not",
     *          "required"=true,
     *          "readonly"=true
     *         }
     *       }
     *      }
     *     }
     *  },
     *  statusCodes={
     *     200="List of restrictions successfully returned."
     *  }
     * )
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function restrictionsAction()
    {
        $user = $this->getCurrentUser();
        if ($user === null) {
            return $this->generateResponse([], 403);
        }

        return $this->generateResponse($user->getRestrictions());
    }

    /**
     *
     * @Route("/update/plan", methods={ "POST" })
     *
     * @param Request $request A Http Request instance.
     */
    public function updatePlanAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $data = $request->request->all();
        $gateway = PaymentGatewayEnum::paypal();
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        if (isset(
            $data['news'],
            $data['blog'],
            $data['reddit'],
            $data['instagram'],
            $data['twitter'],
            $data['analytics'],
            $data['searchesPerDay'],
            $data['savedFeeds'],
            $data['masterAccounts'],
            $data['subscriberAccounts'],
            $data['webFeeds'],
            $data['alerts']
            ) && 
            ($data['searchesPerDay'] >= 0) &&
            ($data['savedFeeds'] >= 0) &&
            ($data['masterAccounts'] >= 0) &&
            ($data['subscriberAccounts'] >= 0) &&
            ($data['webFeeds'] >= 0) &&
            ($data['alerts'] >= 0)
            ) {
                //Call cost calculation plan 
                $costCalculation = $this->container->get('cost.calculation');
                $response =  $costCalculation->costCalculationAction($request, true); 
                $oldPrice = $user->getBillingSubscription()->getPlan()->getPrice();
                
                //Stripe process
                $stripe = $this->container->get('stripe.service');
                $stripe->setApiKey();
                if (empty($user->getStripeUserId())) {
                    $customer = $stripe->createCustomer(
                        [
                        'email' => $user->getEmail(),
                        'name' => $user->getFirstName().' '.$user->getLastName(),
                        'metadata' => ['paymentMethod' => $data['paymentID']]
                        ]
                    );
                    $customerArray = [];
                    if ($customer instanceof ApiErrorException) {
                        $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer failed'];
                        return $this->generateResponse($customerArray, 400);
                    }

                    if (isset($customer['id'])) {
                        $user->setStripeUserId($customer['id']);
                        $em->persist($user);
                        $em->flush();
                        //Add card atatch to customer
                        if (isset($data['paymentID']) && !empty($data['paymentID'])) {
                            $cardAttach = $stripe->paymentMethodAttachToCustomer($data['paymentID'],
                                ['customer' => $customer['id']]
                            );
            
                            if ($cardAttach instanceof ApiErrorException) {
                                $cardAttachArray = ['paymentError' => 1,'data'=>$cardAttach,'message'=>'Card attached to customer failed'];
                                return $this->generateResponse($cardAttachArray, 500);
                            }
                        }
                        //Add product
                        $product = $stripe->addProduct(
                            [
                            'name' => $user->getCompanyName(),
                            'metadata' => (array)$customer['id']
                            ]
                        );
                        if ($product instanceof ApiErrorException) {
                            $productArray = ['paymentError' => 1,'data'=>$product,'message'=>'Product failed'];
                            return $this->generateResponse($productArray, 400);
                        }
        
                        if (isset($product['id'])) {
                            //Call cost calculation plan 
                            $costCalculation = $this->container->get('cost.calculation');
                            $response =  $costCalculation->costCalculationAction($request, true); 
                        
                            $price = $stripe->addPrice(
                                [
                                'unit_amount' => isset($response['price']) ? $response['price'] * 100 : 0,
                                'currency' => 'usd',
                                'recurring' => ['interval' => 'month'],
                                'product' => $product['id']
                                ]
                            );
                            if ($price instanceof ApiErrorException) {
                                $priceArray = ['paymentError' => 1,'data'=>$price,'message'=>'Price add failed'];
                                return $this->generateResponse($priceArray, 400);
                            }
        
                            //Plan subscription code
                            if (isset($price['id'])) {
                                //Add plan
                                $subscription = $stripe->createSubscription(
                                    [
                                        'customer' => $customer['id'],
                                        'items' => [['price' => $price['id']]],
                                        'default_payment_method' => $data['paymentID']
                                    ]
                                );
                                if ($subscription instanceof ApiErrorException) {
                                    $subscriptionArray = ['paymentError' => 1,'data'=>$subscription,'message'=>'Subscribtion failed'];
                                    return $this->generateResponse($subscriptionArray, 400);
                                }
                                //update customer metadata
                                $customer = $stripe->updateCustomer($customer['id'],
                                    [
                                    'metadata' => [
                                                    'paymentMethod' => $data['paymentID'],
                                                    'productId' => $product['id'],
                                                    'priceId' => $price['id'],
                                                    'subscriptionId' => $subscription['id'],
                                                    'subStartDate' => $subscription['current_period_start'],
                                                    'subEndDate' => $subscription['current_period_end'],
                                                ]
                                    ]
                                );
                                $customerArray = [];
                                if ($customer instanceof ApiErrorException) {
                                    $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer add meta data failed'];
                                    return $this->generateResponse($customerArray, 400);
                                }

                                $plan = $user->getBillingSubscription()->getPlan();
                                $plan->setTitle($user->getCompanyName())
                                        ->setInnerName('Starter')
                                        ->setPrice(isset($response['price']) ? $response['price'] : 0)
                                        ->setNews($data['news'])
                                        ->setBlog($data['blog'])
                                        ->setReddit($data['reddit'])
                                        ->setInstagram($data['instagram'])
                                        ->setTwitter($data['twitter'])
                                        ->setAnalytics($data['analytics'])
                                        ->setSearchesPerDay($data['searchesPerDay'])
                                        ->setSavedFeeds($data['savedFeeds'])
                                        ->setMasterAccounts($data['masterAccounts'])
                                        ->setSubscriberAccounts($data['subscriberAccounts'])
                                        ->setWebFeeds($data['webFeeds'])
                                        ->setUser($user)
                                        ->setAlerts($data['alerts']);                    
                                $em->persist($plan);  
                                $em->flush();
                                
                
                                $subscriptionObj = $user->getBillingSubscription();    
                                $subscriptionObj->setGateway($gateway);
                                $subscriptionObj->setStartDate(new \DateTime('@' . $subscription['current_period_start']));
                                $subscriptionObj->setEndDate(new \DateTime('@' . $subscription['current_period_end']));
                                $em->persist($subscriptionObj);  
                                $em->flush();
                            }
                        }    
        
                    }
                } else {
                    if ($response['price'] < $oldPrice) {
                        $planNew = new  Plan();
                        $planNew->setTitle($user->getCompanyName());
                        $planNew->setInnerName('Starter');
                        $planNew->setPrice(isset($response['price']) ? $response['price'] : 0);
                        $planNew->setNews($data['news']);
                        $planNew->setBlog($data['blog']);
                        $planNew->setReddit($data['reddit']);
                        $planNew->setInstagram($data['instagram']);
                        $planNew->setTwitter($data['twitter']);
                        $planNew->setAnalytics($data['analytics']);
                        $planNew->setSearchesPerDay($data['searchesPerDay']);
                        $planNew->setSavedFeeds($data['savedFeeds']);
                        $planNew->setMasterAccounts($data['masterAccounts']);
                        $planNew->setSubscriberAccounts($data['subscriberAccounts']);
                        $planNew->setWebFeeds($data['webFeeds']);
                        $planNew->setAlerts($data['alerts']);
                        $planNew->setUser($user);
                        $planNew->setIsPlanDowngrade(true);
                        $em->persist($planNew);  
                        $em->flush();

                        $subscription = $user->getBillingSubscription();
                        $subscription->setIsPlanDowngrade(true);
                        $em->persist($subscription);
                        $em->flush();

                        $customer = $stripe->getCustomer(
                            $user->getStripeUserId()
                        );
                        $customerArray = [];
                        if ($customer instanceof ApiErrorException) {
                            $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer not found'];
                            return $this->generateResponse($customerArray, 400);
                        }
                        if (isset($customer['id']) && isset($data['paymentID']) && !empty($data['paymentID'])) {
                            //Add card atatch to customer
                            $cardAttach = $stripe->paymentMethodAttachToCustomer($data['paymentID'],
                                ['customer' => $customer['id']]
                            );
            
                            if ($cardAttach instanceof ApiErrorException) {
                                $cardAttachArray = ['paymentError' => 1,'data'=>$cardAttach,'message'=>'Card update attached to customer failed'];
                                return $this->generateResponse($cardAttachArray, 500);
                            }
                            //Card detach to customer
                            $cardDetachPaymentMethod = $stripe->paymentMethodDetachToCustomer($customer['metadata']['paymentMethod']
                            );
                            if ($cardDetachPaymentMethod instanceof ApiErrorException) {
                                $cardDetachArray = ['paymentError' => 1,'data'=>$cardDetachPaymentMethod,'message'=>'Card detach to customer failed'];
                                return $this->generateResponse($cardDetachArray, 500);
                            }
                             //update customer metadata
                             $customer = $stripe->updateCustomer($customer['id'],
                             [
                             'metadata' => [
                                             'paymentMethod' => isset($data['paymentID']) ? $data['paymentID'] : $customer['metadata']['paymentMethod'],
                                             ]
                             ]
                            );
                            $customerArray = [];
                            if ($customer instanceof ApiErrorException) {
                                $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer update meta data failed'];
                                return $this->generateResponse($customerArray, 400);
                            }
                        }    
                                
                    } else {
                        $customer = $stripe->getCustomer(
                            $user->getStripeUserId()
                        );
                        $customerArray = [];
                        if ($customer instanceof ApiErrorException) {
                            $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer not found'];
                            return $this->generateResponse($customerArray, 400);
                        }
                        if (isset($customer['id'])) {
                            //Add card atatch to customer
                            if (isset($data['paymentID']) && !empty($data['paymentID'])) {
                                $cardAttach = $stripe->paymentMethodAttachToCustomer($data['paymentID'],
                                    ['customer' => $customer['id']]
                                );
                
                                if ($cardAttach instanceof ApiErrorException) {
                                    $cardAttachArray = ['paymentError' => 1,'data'=>$cardAttach,'message'=>'Card update attached to customer failed'];
                                    return $this->generateResponse($cardAttachArray, 500);
                                }
                                //Card detach to customer
                                $cardDetachPaymentMethod = $stripe->paymentMethodDetachToCustomer($customer['metadata']['paymentMethod']
                                );
                                if ($cardDetachPaymentMethod instanceof ApiErrorException) {
                                    $cardDetachArray = ['paymentError' => 1,'data'=>$cardDetachPaymentMethod,'message'=>'Card detach to customer failed'];
                                    return $this->generateResponse($cardDetachArray, 500);
                                }
                            }
                            if (isset($customer['metadata']['productId'])) {
                                   
                                $price = $stripe->addPrice(
                                    [
                                    'unit_amount' => isset($response['price']) ? $response['price'] * 100 : 0,
                                    'currency' => 'usd',
                                    'recurring' => ['interval' => 'month'],
                                    'product' => $customer['metadata']['productId']
                                    ]
                                );
                                if ($price instanceof ApiErrorException) {
                                    $priceArray = ['paymentError' => 1,'data'=>$price,'message'=>'Price not found'];
                                    return $this->generateResponse($priceArray, 400);
                                }
            
                                //Plan subscription code
                                if (isset($price['id'])) {
                                    //Add subscription
                                    $subscription = $stripe->getSubscription(
                                        $customer['metadata']['subscriptionId']
                                    );
                                 
                                    if ($subscription instanceof ApiErrorException) {
                                        $subscriptionArray = ['paymentError' => 1,'data'=>$subscription,'message'=>'Subscribtion get failed'];
                                        return $this->generateResponse($subscriptionArray, 400);
                                    }
    
                                    if (isset($subscription['id'])) {
                                         //Add subscription item
                                        $subscriptionItem = $stripe->updateSubscriptionItem($subscription['items']['data'][0]['id'],
                                        [
                                            'price' => $price['id'],
                                        ]
                                        );
                                        if ($subscriptionItem instanceof ApiErrorException) {
                                            $subscriptionItemArray = ['paymentError' => 1,'data'=>$subscriptionItem,'message'=>'Subscribtion Item failed'];
                                            return $this->generateResponse($subscriptionItemArray, 400);
                                        }
                                    }
    
                                     //update customer metadata
                                    $customer = $stripe->updateCustomer($customer['id'],
                                        [
                                        'metadata' => [
                                                        'paymentMethod' => isset($data['paymentID']) ? $data['paymentID'] : $customer['metadata']['paymentMethod'],
                                                        'priceId' => $price['id'],
                                                        'subscriptionId' => $subscription['id'],
                                                        'subStartDate' => $subscription['current_period_start'],
                                                        'subEndDate' => $subscription['current_period_end'],
                                                        ]
                                        ]
                                    );
                                    $customerArray = [];
                                    if ($customer instanceof ApiErrorException) {
                                        $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer update meta data failed'];
                                        return $this->generateResponse($customerArray, 400);
                                    }
                                    $plan = $user->getBillingSubscription()->getPlan();
                                    $plan->setTitle($user->getCompanyName())
                                            ->setInnerName('Starter')
                                            ->setPrice(isset($response['price']) ? $response['price'] : 0)
                                            ->setNews($data['news'])
                                            ->setBlog($data['blog'])
                                            ->setReddit($data['reddit'])
                                            ->setInstagram($data['instagram'])
                                            ->setTwitter($data['twitter'])
                                            ->setAnalytics($data['analytics'])
                                            ->setSearchesPerDay($data['searchesPerDay'])
                                            ->setSavedFeeds($data['savedFeeds'])
                                            ->setMasterAccounts($data['masterAccounts'])
                                            ->setSubscriberAccounts($data['subscriberAccounts'])
                                            ->setWebFeeds($data['webFeeds'])
                                            ->setUser($user)
                                            ->setAlerts($data['alerts']);                    
                                    $em->persist($plan);  
                                    $em->flush();

                                    $user->getBillingSubscription()->setStartDate(new \DateTime('@' . $subscription['current_period_start']));
                                    $user->getBillingSubscription()->setEndDate(new \DateTime('@' . $subscription['current_period_end']));
                                    $em->persist($user);
                                    $em->flush();                                    
                                }
                            }    
                        }
                    }
                   
                }
        }
       
        return $this->generateResponse([
            'success' => true,
        ]);
    }

    /**
     *
     * @Route("/cancel/plan", methods={ "POST" })
     *
     * @param Request $request A Http Request instance.
     */
    public function cancelSubscriptionAction(Request $request)
    {
        $user = $this->getCurrentUser();
        //Stripe process
        $stripe = $this->container->get('stripe.service');
        $stripe->setApiKey();
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $customer = $stripe->getCustomer(
            $user->getStripeUserId()
        );
        $customerArray = [];
        if ($customer instanceof ApiErrorException) {
            $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer not found'];
            return $this->generateResponse($customerArray, 400);
        }
        if (isset($customer['id'])) {
           $updateSubscription = $stripe->updateSubscription($customer['metadata']['subscriptionId'],
                [
                    'cancel_at_period_end' => true,
                ]
            );
            if ($updateSubscription instanceof ApiErrorException) {
                $updateSubscriptionArray = ['paymentError' => 1,'data'=>$updateSubscription,'message'=>'Cancel subscription'];
                return $this->generateResponse($updateSubscriptionArray, 500);
            }
            $user->getBillingSubscription()->setIsSubscriptionCancelled(true);
            $em->persist($user);  
            $em->flush();
        }
       
        return $this->generateResponse([
            'success' => true,
        ]);
    }    

    /**
     *
     * @Route("/card/change", methods={ "POST" })
     *
     * @param Request $request A Http Request instance.
     */
    public function changeCardAction(Request $request)
    {
        $user = $this->getCurrentUser();
        if (!empty($user->getStripeUserId())) {
            $customerStripeId = $user->getStripeUserId();
            $stripe = $this->container->get('stripe.service');
            $stripe->setApiKey();
            $customer = $stripe->getCustomer(
                $user->getStripeUserId()
            );
            //Card detach to customer
            $cardDetachPaymentMethod = $stripe->paymentMethodDetachToCustomer($customer['metadata']['paymentMethod']
            );
            if ($cardDetachPaymentMethod instanceof ApiErrorException) {
                $cardDetachArray = ['paymentError' => 1,'data'=>$cardDetachPaymentMethod,'message'=>'Card detach to customer failed'];
                return $this->generateResponse($cardDetachArray, 500);
            }
            //Card attach to customer
            $cardAttachPaymentMethod = $stripe->paymentMethodAttachToCustomer($request->request->get('paymentID'),
                        ['customer' => $customerStripeId]
            );
            if ($cardAttachPaymentMethod instanceof ApiErrorException) {
                $cardAttachArray = ['paymentError' => 1,'data'=>$cardAttachPaymentMethod,'message'=>'Updated card attached to customer failed'];
                return $this->generateResponse($cardAttachArray, 500);
            }
            //update customer metadata
            $customer = $stripe->updateCustomer($customerStripeId,
                [
                'metadata' => ['paymentMethod' => $request->request->get('paymentID')]
                ]
            );
            if ($customer instanceof ApiErrorException) {
                $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer update meta data failed'];
                return $this->generateResponse($customerArray, 400);
            }
            $data = ['success' => 1,'message'=>'Card updated successfully to this customer..'];
            return $this->generateResponse($data,200);
        }
        $data = ['error' => 1,'message'=>'Customer not registered in Stripe'];
        return $this->generateResponse($data, 400);
    }

    /**
     *
     * @Route("/invoices", methods={ "GET" })
     *
     * @param Request $request A Http Request instance.
     */
    public function getInvoiceAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $invoices = [];
        if (!empty($user->getStripeUserId())) {
            $stripe = $this->container->get('stripe.service');
            $stripe->setApiKey();
            $invoices = $stripe->getAllInvoice(['customer' => $user->getStripeUserId()]);
            if ($invoices instanceof ApiErrorException) {
                $invoicesArray = ['paymentError' => 1,'data'=>$invoices,'message'=>'List all invoice of customer faild'];
                return $this->generateResponse($invoicesArray, 400);
            }
        }    
        $data = ['success' => 1,'data' => $invoices];
        return $this->generateResponse($data, 200);
    }    
}
