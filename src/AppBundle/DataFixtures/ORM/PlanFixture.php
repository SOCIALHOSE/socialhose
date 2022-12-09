<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Plan;

/**
 * Class PlanFixture
 * @package AppBundle\DataFixtures\ORM
 */
class PlanFixture extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $plan = Plan::create()
            ->setTitle('Social Starter')
            ->setInnerName('social_starter')
            ->setSearchesPerDay(500)
            ->setSavedFeeds(15)
            ->setMasterAccounts(1)
            ->setSubscriberAccounts(5)
            ->setAlerts(5)
            ->setNewsletters(1)
            ->setAnalytics(false)
            ->setNews(true)
            ->setBlog(false)
            ->setReddit(false)
            ->setInstagram(true)
            ->setIsDefault(true)
            ->setTwitter(false)
            ->setPrice(160.0);
        $manager->persist($plan);
        $this->setReference('starter_plan', $plan);


        $plan = Plan::create()
            ->setTitle('Pr Starter')
            ->setInnerName('pr_starter')
            ->setSearchesPerDay(1000)
            ->setSavedFeeds(20)
            ->setMasterAccounts(1)
            ->setSubscriberAccounts(20)
            ->setAlerts(20)
            ->setNewsletters(10)
            ->setAnalytics(true)
            ->setNews(true)
            ->setBlog(false)
            ->setIsDefault(true)
            ->setReddit(false)
            ->setInstagram(true)
            ->setTwitter(false)
            ->setPrice(190.0);
        $manager->persist($plan);
        $this->setReference('pr_starter', $plan);


        $plan = Plan::create()
            ->setTitle('The Works')
            ->setInnerName('the_works')
            ->setSearchesPerDay(2000)
            ->setSavedFeeds(25)
            ->setMasterAccounts(1)
            ->setSubscriberAccounts(25)
            ->setAlerts(30)
            ->setNewsletters(20)
            ->setAnalytics(true)
            ->setNews(true)
            ->setBlog(true)
            ->setReddit(true)
            ->setInstagram(true)
            ->setIsDefault(true)
            ->setTwitter(true)
            ->setPrice(325.0);
        $manager->persist($plan);
        $this->setReference('the_works', $plan);



        $plan = Plan::create()
            ->setTitle('Free')
            ->setInnerName('free')
            ->setSearchesPerDay(100)
            ->setSavedFeeds(0)
            ->setMasterAccounts(1)
            ->setSubscriberAccounts(0)
            ->setAlerts(5)
            ->setNewsletters(5)
            ->setAnalytics(true)
            ->setNews(true)
            ->setBlog(true)
            ->setReddit(true)
            ->setInstagram(true)
            ->setTwitter(true)
            ->setIsDefault(true)
            ->setPrice(0.0);
        $manager->persist($plan);
        $this->setReference('free', $plan);

        $manager->flush();
    }
}
