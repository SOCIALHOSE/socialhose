<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use CacheBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserManagerInterface;
use PaymentBundle\Enum\PaymentGatewayEnum;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;

/**
 * Class UserFixture
 * @package AppBundle\DataFixtures\ORM
 */
class UserFixture extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(ObjectManager $manager)
    {
        /** @var UserManagerInterface $userManager */
        $userManager = $this->container->get('fos_user.user_manager');

        if ($this->checkEnvironment('prod')) {
            /** @var User $superAdmin */
            $superAdmin = $userManager->createUser();
            $superAdmin
                ->setFirstName('Super')
                ->setLastName('Admin')
                ->setEmail('super_admin@socialhose.io')
                ->setPhoneNumber('44444444444')
                ->setVerified()
                ->setEnabled(true)
                ->addRole(UserRoleEnum::SUPER_ADMIN)
                ->setPlainPassword('FEvJNcKGk2rVDMVL');

            $userManager->updateUser($superAdmin);
            $this->setReference('super_admin@socialhose.io', $superAdmin);

            return;
        }

        //
        // Create organization subscription and users.
        //
        /** @var Organization $organization */
        $organization = $this->getReference('organization');
        /** @var Plan $businessPlan */
        $businessPlan = $this->getReference('starter_plan');

        /** @var Plan $basicPlan */
        $basicPlan = $this->getReference('pr_starter');

        //
        // First department.
        //

        $firstSubscription = OrganizationSubscription::create()
            ->setGateway(PaymentGatewayEnum::paypal())
            ->setPayed(true)
            ->setPlan($businessPlan)
            ->setOrganization($organization)
            ->setOrganizationAddress('First department address')
            ->setOrganizationEmail('first_department.organization@email.com')
            ->setOrganizationPhone('111111111');

        /** @var User $master */
        $master = $userManager->createUser();
        $master
            ->setFirstName('John')
            ->setLastName('Smith')
            ->setEmail('test@email.com')
            ->setPhoneNumber('11111111111')
            ->setVerified()
            ->setEnabled(true)
            ->setBillingSubscription($firstSubscription)
            ->addRole(UserRoleEnum::MASTER_USER)
            ->setPlainPassword('test');
        $main = Category::createMainCategory($master);
        Category::createSharedCategory($master);
        Category::createTrashCategory($master);
        $subMain = Category::createChild($main, $master, 'Sub main');
        Category::createChild($subMain, $master, 'Sub main sub 1');
        Category::createChild($subMain, $master, 'Sub main sub 2');
        $subMainSub3 = Category::createChild($subMain, $master, 'Sub main sub 3');
        Category::createChild($subMainSub3, $master, 'Test');

        $firstSubscription->setOwner($master);

        $userManager->updateUser($master);
        $this->setReference('test@email.com', $master);
        $this->setReference('first_subscription', $firstSubscription);

        /** @var User $user */
        $user = $userManager->createUser();
        $user
            ->setFirstName('John')
            ->setLastName('Smith')
            ->setEmail('test_subscriber@email.com')
            ->setPhoneNumber('11111111112')
            ->setVerified()
            ->setEnabled(true)
            ->setBillingSubscription($firstSubscription)
            ->addRole(UserRoleEnum::SUBSCRIBER)
            ->setMasterUser($master)
            ->setPlainPassword('test');


        $manager->persist($firstSubscription);
        $userManager->updateUser($user);
        $this->setReference('test_subscriber@email.com', $user);

        //
        // Second department.
        //

        $secondSubscription = OrganizationSubscription::create()
            ->setGateway(PaymentGatewayEnum::paypal())
            ->setPayed(true)
            ->setPlan($basicPlan)
            ->setOrganization($organization)
            ->setOrganizationAddress('Second department address')
            ->setOrganizationEmail('second_department.organization@email.com')
            ->setOrganizationPhone('222222222');

        /** @var User $user */
        $user = $userManager->createUser();
        $user
            ->setFirstName('Master')
            ->setLastName('Smith')
            ->setEmail('master@email.com')
            ->setPhoneNumber('22222222222')
            ->setVerified()
            ->setEnabled(true)
            ->setBillingSubscription($secondSubscription)
            ->addRole(UserRoleEnum::MASTER_USER)
            ->setPlainPassword('test');
        $main = Category::createMainCategory($user);
        Category::createSharedCategory($user);
        Category::createTrashCategory($user);
        Category::createChild($main, $user, 'Sub main');

        $secondSubscription->setOwner($user);
        $manager->persist($secondSubscription);

        $userManager->updateUser($user);
        $this->setReference('master@email.com', $user);
        $this->setReference('second_subscription', $secondSubscription);

        //
        // Individual subscription.
        //
        $personSubscription = PersonalSubscription::create()
            ->setGateway(PaymentGatewayEnum::paypal())
            ->setPayed(true)
            ->setPlan($basicPlan);

        $user = $userManager->createUser();
        $user
            ->setFirstName('Jane')
            ->setLastName('Smith')
            ->setEmail('jane@person.com')
            ->setPhoneNumber('33333333333')
            ->setVerified()
            ->setEnabled(true)
            ->setBillingSubscription($personSubscription)
            ->addRole(UserRoleEnum::MASTER_USER)
            ->setPlainPassword('test');
        Category::createMainCategory($user);
        Category::createTrashCategory($user);

        $userManager->updateUser($user);
        $this->setReference('jane@person.com', $user);
        $this->setReference('personal_subscription', $personSubscription);

        $personSubscription->setOwner($user);
        $manager->persist($personSubscription);

        //
        // Admins.
        //

        /** @var User $superAdmin */
        $superAdmin = $userManager->createUser();
        $superAdmin
            ->setFirstName('Super')
            ->setLastName('Admin')
            ->setEmail('super_admin@socialhose.com')
            ->setPhoneNumber('44444444444')
            ->setVerified()
            ->setEnabled(true)
            ->addRole(UserRoleEnum::SUPER_ADMIN)
            ->setPlainPassword('test');

        $userManager->updateUser($superAdmin);
        $this->setReference('super_admin@socialhose.com', $superAdmin);

        /** @var User $admin */
        $admin = $userManager->createUser();
        $admin
            ->setFirstName('Just')
            ->setLastName('Admin')
            ->setEmail('admin@socialhose.com')
            ->setPhoneNumber('55555555555')
            ->setVerified()
            ->setEnabled(true)
            ->addRole(UserRoleEnum::ADMIN)
            ->setPlainPassword('test');

        $userManager->updateUser($admin);
        $this->setReference('admin@socialhose.com', $admin);
    }

    /**
     * Get the order of this fixture.
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
