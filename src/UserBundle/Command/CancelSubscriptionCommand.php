<?php

namespace UserBundle\Command;

use AppBundle\Command\AbstractSingleCopyCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\User;
use UserBundle\Repository\UserRepository;
use UserBundle\Entity\Plan;

/**
 * Class CancelSubscriptionCommand
 *
 * @package UserBundle\Command
 */
class CancelSubscriptionCommand extends AbstractSingleCopyCommand
{

    const NAME = 'socialhose:cancel-subscription';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CancelSubscriptionCommand constructor.
     *
     * @param EntityManagerInterface $em     A EntityManagerInterface instance.
     * @param LoggerInterface        $logger A LoggerInterface instance.
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct(self::NAME, $logger);

        $this->em = $em;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Cancel subscription');
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
        $users = $repository->getAllUserBillingSubscription($currentDate);
        foreach ($users as $user) {
            $repository = $this->em->getRepository(Plan::class);
            $planObj = $repository->findOneBy([ 'title' => 'Free' ]);
            
            $plan = $user->getBillingSubscription()->getPlan();
            $plan->setTitle($user->getCompanyName());
            $plan->setInnerName('Starter');
            $plan->setPrice(0);
            $plan->setNews($planObj->isNews());
            $plan->setBlog($planObj->isBlog());
            $plan->setReddit($planObj->isReddit());
            $plan->setInstagram($planObj->isInstagram());
            $plan->setTwitter($planObj->isTwitter());
            $plan->setAnalytics($planObj->isAnalytics());
            $plan->setSearchesPerDay($planObj->getSearchesPerDay());
            $plan->setSavedFeeds($planObj->getSavedFeeds());
            $plan->setMasterAccounts($planObj->getMasterAccounts());
            $plan->setSubscriberAccounts($planObj->getSubscriberAccounts());
            $plan->setAlerts($planObj->getAlerts());
            $plan->setNewsLetters($planObj->getNewsLetters());
            $plan->setWebFeeds($planObj->getWebFeeds());
            $plan->setAlerts($planObj->getAlerts());
            $plan->setIsPlanDowngrade(false);

            $this->em->persist($plan);
            $this->em->flush();

            $subscription = $user->getBillingSubscription();
            $subscription->setIsSubscriptionCancelled(false);
            $this->em->persist($subscription);
            $this->em->flush();

        }
        $io->success('Cancel Subscription Successfully.');
        return 0;
    }
}
