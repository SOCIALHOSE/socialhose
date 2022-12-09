<?php

namespace UserBundle\Command;

use AppBundle\Command\AbstractSingleCopyCommand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Repository\SubscriptionRepository;
use Symfony\Component\Console\Style\SymfonyStyle;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;
use UserBundle\Entity\Plan;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DowngradeSubscriptionPlanCommand
 *
 * @package UserBundle\Command
 */
class DowngradeSubscriptionPlanCommand extends AbstractSingleCopyCommand
{

    const NAME = 'socialhose:downgrade-subscription-plan';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * DowngradeSubscriptionPlanCommand constructor.
     *
     * @param EntityManagerInterface $em     A EntityManagerInterface instance.
     * @param LoggerInterface        $logger A LoggerInterface instance.
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger,ContainerInterface $container)
    {
        parent::__construct(self::NAME, $logger);

        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Downgrade subscription plan');
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $this->em->getRepository(User::class);
        $currentDate = date('Y-m-d');
        $users = $repository->getAllUserBillingSubscriptionPlanDowngrade($currentDate);
        foreach ($users as $user) {
            $repository = $this->em->getRepository(Plan::class);
            $planObj = $repository->findOneBy(['isPlanDowngrade' => true, 'user' => $user->getId()], ['id'=>'desc']);
            
            $subscription = $user->getBillingSubscription();
            $subscription->setIsPlanDowngrade(false);
            $subscription->setPlan($planObj);
            $this->em->persist($subscription);
            $this->em->flush();
            $this->downgradePlanInStripe($user,$planObj);
        }
        $io->success('Downgrade Subscription Plan Successfully.');
        return 0;
    }

    protected function downgradePlanInStripe($user, $planObj)
    {
        $stripe = $this->container->get('stripe.service');
        $stripe->setApiKey();
        $customer = $stripe->getCustomer(
            $user->getStripeUserId()
        );
        $customerArray = [];
        if ($customer instanceof ApiErrorException) {
            $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer not found'];
            $this->logger->info(sprintf(
                'Error cron job downgrade \'%s\'',
                json_encode($customerArray)
            ));
        }
        if (isset($customer['id'])) {
            $price = $stripe->addPrice(
                [
                'unit_amount' => !empty($planObj->getPrice()) ? $planObj->getPrice() * 100 : 0,
                'currency' => 'usd',
                'recurring' => ['interval' => 'month'],
                'product' => $customer['metadata']['productId']
                ]
            );
            if ($price instanceof ApiErrorException) {
                $priceArray = ['paymentError' => 1,'data'=>$price,'message'=>'Price not found'];
                $this->logger->info(sprintf(
                    'Error cron job downgrade \'%s\'',
                    json_encode($priceArray)
                ));
                return $this->generateResponse($priceArray, 400);
            }
            if (isset($price['id'])) {
                //Add subscription
                $subscription = $stripe->getSubscription(
                    $customer['metadata']['subscriptionId']
                );
             
                if ($subscription instanceof ApiErrorException) {
                    $subscriptionArray = ['paymentError' => 1,'data'=>$subscription,'message'=>'Subscribtion get failed'];
                    $this->logger->info(sprintf(
                        'Error cron job downgrade \'%s\'',
                        json_encode($subscriptionArray)
                    ));
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
                        $this->logger->info(sprintf(
                            'Error cron job downgrade \'%s\'',
                            json_encode($subscriptionItemArray)
                        ));
                        return $this->generateResponse($subscriptionItemArray, 400);
                    }
                     //update customer metadata
                     $customer = $stripe->updateCustomer($customer['id'],
                     [
                     'metadata' => [
                                     'priceId' => $price['id'],
                                     'subscriptionId' => $subscription['id'],
                                     'subStartDate' => $subscription['current_period_start'],
                                     'subEndDate' => $subscription['current_period_end'],
                                     ]
                     ]
                    );
                    if ($customer instanceof ApiErrorException) {
                        $customerArray = ['paymentError' => 1,'data'=>$customer,'message'=>'Customer update meta data failed'];
                        $this->logger->info(sprintf(
                            'Error cron job downgrade \'%s\'',
                            json_encode($customerArray)
                        ));
                    }
                }
            }    
        }    
    }    
}
