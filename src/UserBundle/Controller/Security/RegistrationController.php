<?php

namespace UserBundle\Controller\Security;

use ApiBundle\Controller\AbstractApiController;
use AppBundle\AppBundleServices;
use AppBundle\Configuration\ConfigurationInterface;
use AppBundle\Configuration\ParametersName;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use PaymentBundle\Model\BillingSubscription;
use PaymentBundle\Model\PaymentData;
use PaymentBundle\PaymentBundleServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Plan;
use UserBundle\Entity\User;
use UserBundle\Form\PaymentDataType;
use UserBundle\Form\RegistrationType;
use UserBundle\Manager\User\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Event\GetResponseUserEvent;
use UserBundle\Mailer\MailerInterface;
use Stripe\Exception\ApiErrorException;
use UserBundle\Security\CostCalculationController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegistrationController
 * @package UserBundle\Controller\Security
 *
 * @Route("/registration", service="user.controller.registration")
 */
class RegistrationController extends AbstractApiController
{
    
    /**
     * Max organization rep response.
     */
    const DEFAULT_LIMIT = 10;

    /**
     * Register new user.
     * Return empty response.
     *
     * @Route("", methods={ "POST" })
     *
     * @ApiDoc(
     *     resource="Registration",
     *     section="Security",
     *     input={
     *      "class"="UserBundle\Form\RegistrationType",
     *      "name"=false
     *     },
     *     output={
     *      "class"="",
     *      "data"={
     *          "message"={
     *              "dataType"="string",
     *              "description"="Registration success message"
     *          }
     *      }
     *     },
     *     statusCodes={
     *      200="Register successfully."
     *     }
     * )
     *
     * @param Request $request A Request instance.
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function registerAction(Request $request)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');
        /** @var \UserBundle\Entity\User $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $form = $this->createForm(RegistrationType::class, $user,  array(
            'paymentID' => $request->request->get('paymentID'),
        ));
        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');
            $encoded = $passwordEncoder->encodePassword($user, $form['password']->getData());
            $user->setPassword($encoded);
            
            // if (!empty($user->getBillingSubscription()->getPlan()->isFree())) {
            //     $user->getBillingSubscription()->setPayed(true);
            //     $userManager->updateUser($user);
            
            //     /** @var ConfigurationInterface $configuration */
            //     $configuration = $this->get(AppBundleServices::CONFIGURATION);
            
            //     return $this->generateResponse([
            //         'message' => $configuration->getParameter(ParametersName::REGISTRATION_PAYMENT_AWAITING),
            //         ]);
            // }
            
            /** @var TokenGeneratorInterface $tokenGenerator */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
            
            //stripe register user
            if (isset($form['paymentID'])) {
                $stripe = $this->get('stripe.service');
                $stripe->setApiKey();
               
                $customer = $stripe->createCustomer(
                    [
                    'email' => $form['email']->getData(),
                    'name' => $form['firstName']->getData().' '.$form['lastName']->getData(),
                    ]
                );

                $customerArray = [];
                if ($customer instanceof ApiErrorException) {
                    $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer failed'];
                    return $this->generateResponse($customerArray, 400);
                } 
                if (isset($customer['id'])) {
                    $user->setStripeUserId($customer['id']);

                    //Add card atatch to customer
                    $cardAttach = $stripe->paymentMethodAttachToCustomer($form['paymentID']->getData(),
                        ['customer' => $customer['id']]
                    );

                    if ($cardAttach instanceof ApiErrorException) {
                        $cardAttachArray = ['paymentError' => 1,'data'=>$cardAttach,'message'=>'Card attached to customer failed'];
                        return $this->generateResponse($cardAttachArray, 500);
                    }

                    //Add product
                    $product = $stripe->addProduct(
                        [
                        'name' => 'SOCIALHOSE.IO Media Monitoring Subscription',
                        'metadata' => (array)$customer['id']
                        ]
                    );
                    if ($product instanceof ApiErrorException) {
                        $productArray = ['paymentError' => 1,'data'=>$product,'message'=>'Product failed'];
                        return $this->generateResponse($productArray, 400);
                    }

                    if (isset($product['id'])) {

                        //Call cost calculation plan 
                        $costCalculation = $this->get('cost.calculation');
                        $response =  $costCalculation->costCalculationAction($request, true); 
                        //Add plan
                        // $plan = $stripe->addPlan(
                        //     [
                        //     'amount' => isset($response['price']) ? $response['price'] * 100 : 0,
                        //     'currency' => 'usd',
                        //     'interval' => 'month',
                        //     'product' => $product['id']
                        //     ]
                        // );
                        // if ($plan instanceof ApiErrorException) {
                        //     $planArray = ['paymentError' => 1,'data'=>[],'message'=>'Plan failed'];
                        //     return $this->generateResponse($planArray, 500);
                        // }

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
                                    'default_payment_method' => $form['paymentID']->getData()
                                ]
                            );
                            if ($subscription instanceof ApiErrorException) {
                                $subscriptionArray = ['paymentError' => 1,'data'=>$subscription,'message'=>'Subscribtion failed'];
                                return $this->generateResponse($subscriptionArray, 400);
                            }
                        }
                    }    

                }
            }
            $mailer = $this->get('user.mailer.default');
            $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
            $mailer->sendEmailMessage($user, $baseurl);
            $userManager->updateUser($user);
            
            return $this->generateResponse([
                'success' => true,
                'isFreeUser'=> isset($form['paymentID']) ? false : true
            ]);
        }
            
        return $this->generateResponse($form, 400);
    }
             
    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $user->setVerified(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        return $this->generateResponse([
            'success' => true,
        ]);
    }

    /**
     * Get billing plans
     *
     * @Route("/plans", methods={ "GET" })
     *
     * @ApiDoc(
     *     resource="Registration",
     *     section="Security",
     *     output={
     *      "class"="Array<UserBundle\Entity\Plan>",
     *      "groups"={ "id", "plan" }
     *     },
     *     statusCodes={
     *      200="All available plans."
     *     }
     * )
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function billingPlansAction()
    {
        $repository = $this->getManager()->getRepository(Plan::class);
        $plans = $repository->findAll();
        if (count($plans) === 0) {
            return $this->generateResponse("Can't find plans.", 404);
        }

        return $this->generateResponse($plans, 200, [
            'id',
            'plan',
        ]);
    }

    /**
     * Organization autocomplete
     *
     * @Route("/organizationAutocomplete", methods={ "GET" })
     * @ApiDoc(
     *     resource="Registration",
     *     section="Security",
     *     filters={
     *      {
     *          "name"="organizationName",
     *          "dataType"="string",
     *          "description"="Part of organization name",
     *          "requirements"="[\w\s]+"
     *      }
     *     },
     *     output={
     *      "class"="",
     *      "data"={
     *          ""={
     *              "dataType"="Collection of string",
     *              "description"="Matched organization names.",
     *              "required"=true,
     *              "readonly"=true
     *          }
     *      }
     *     },
     *     statusCodes={
     *      200="All available organization names."
     *     }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return string
     */
    public function organizationAutocompleteAction(Request $request)
    {
        $repository = $this->getManager()->getRepository(Organization::class);
        $organizationName = trim($request->query->get('organizationName'));
        $organizationName = implode(' ', \nspl\a\map(function ($name) {
            return '%'. trim($name) .'%';
        }, explode(' ', $organizationName)));

        $organizations = $repository->createQueryBuilder('Organization')
            ->select('Organization.name')
            ->where('Organization.name LIKE :name')
            ->setParameter('name', $organizationName)
            ->getQuery()
            ->setMaxResults(self::DEFAULT_LIMIT)
            ->getResult();

        return $this->generateResponse(\nspl\a\map(\nspl\op\itemGetter('name'), $organizations));
    }

    /**
     * Get list of available gateways.
     *
     * @Route("/paymentGateways", methods={ "GET" })
     * @ApiDoc(
     *     resource="Registration",
     *     section="Security",
     *     output={
     *      "class"="",
     *      "data"={
     *          ""={
     *              "dataType"="collection of string",
     *              "description"="Available payment gateways"
     *          }
     *      }
     *     },
     * )
     *
     * @return array
     */
    public function gatewaysAction()
    {
        return PaymentGatewayEnum::getChoices();
    }

    /**
     * Finish registration.
     *
     * @Route("/finish", methods={ "POST" })
     * @ApiDoc(
     *     resource="Registration",
     *     section="Security",
     *     input={
     *      "class"="UserBundle\Form\PaymentDataType",
     *      "name"=false
     *     },
     *     output={
     *      "class"="",
     *      "data"={
     *          "message"={
     *              "dataType"="string",
     *              "description"="Payment success message"
     *          }
     *      }
     *     },
     * )
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return string
     */
    public function finishAction(Request $request)
    {
        $form = $this->createForm(PaymentDataType::class);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var PaymentData $data */
            $data = $form->getData();

            $gatewayName = $data->getGateway();
            $subscription = $data->getUser()->getBillingSubscription();
            $creditCard = $data->getCreditCard();

            if ($subscription === null) {
                return $this->generateResponse([ 'Unknown confirmation token' ], 400);
            }

            /** @var PaymentGatewayFactoryInterface $gatewayFactory */
            $gatewayFactory = $this->get(PaymentBundleServices::PAYMENT_GATEWAY_FACTORY);
            $gateway = $gatewayFactory->getGateway($gatewayName);

            $billingSubscription = new BillingSubscription($subscription, $subscription->getPlan(), $creditCard);
            $gateway->executeSubscription($billingSubscription);

            $user = $data->getUser();
            $user->setConfirmationToken(null);

            /** @var EntityManagerInterface $em */
            $em = $this->get('doctrine.orm.default_entity_manager');

            $em->persist($user);
            $em->flush();

            /** @var ConfigurationInterface $configuration */
            $configuration = $this->get(AppBundleServices::CONFIGURATION);

            return $this->generateResponse([
                'message' => $configuration->getParameter(ParametersName::REGISTRATION_PAYMENT_AWAITING),
            ]);
        }

        return $this->generateResponse($form, 400);
    }
}
