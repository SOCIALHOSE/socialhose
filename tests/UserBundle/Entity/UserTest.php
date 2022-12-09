<?php

namespace UserBundle\Entity;

use Tests\AppTestCase;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\AppPermissionEnum;

/**
 * Class UserTest
 *
 * @package UserBundle\Entity
 */
class UserTest extends AppTestCase
{

    /**
     * @var Plan|\PHPUnit_Framework_MockObject_MockObject
     */
    private $plan;

    /**
     * @var AbstractSubscription|\PHPUnit_Framework_MockObject_MockObject
     */
    private $subscription;

    /**
     * @var User|\PHPUnit_Framework_MockObject_MockObject
     */
    private $user;

    /**
     * @return void
     */
    public function testIsAllowedTo()
    {
        $this->plan->setAnalytics(true);
        $this->assertTrue($this->user->isAllowedTo(AppPermissionEnum::analytics()));

        $this->plan->setAnalytics(false);
        $this->assertFalse($this->user->isAllowedTo(AppPermissionEnum::analytics()));
    }

    /**
     * @return void
     */
    public function testUseLimit()
    {
        /** @var AppLimitEnum $value */
        foreach (AppLimitEnum::getValues() as $value) {
            $this->plan->setLimitValue($value, 4);
            $this->subscription->setLimitValue($value, 1);

            $this->user->useLimit($value);
            $this->assertEquals(2, $this->user->getUsedLimit($value));
            $this->user->useLimit($value, 2);
            $this->assertEquals(4, $this->user->getUsedLimit($value));
        }
    }

    /**
     * @return void
     */
    public function testReleaseLimit()
    {
        /** @var AppLimitEnum $value */
        foreach (AppLimitEnum::getValues() as $value) {
            $this->subscription->setLimitValue($value, 4);

            $this->user->releaseLimit($value);
            $this->assertEquals(3, $this->user->getUsedLimit($value));
            $this->user->releaseLimit($value, 2);
            $this->assertEquals(1, $this->user->getUsedLimit($value));
        }
    }

    /**
     * @expectedException \AppBundle\Exception\LimitExceedException
     *
     * @return void
     */
    public function testUseLimitExceed()
    {
        $this->plan->setSearchesPerDay(4);
        $this->subscription->setSearchesPerDay(3);

        $this->user->useLimit(AppLimitEnum::searches(), 2);
    }

    /**
     * @return void
     */
    public function testGetRestrictions()
    {
        $limits = [];
        /** @var AppLimitEnum $value */
        foreach (AppLimitEnum::getValues() as $value) {
            $limit = mt_rand(5, 10);
            $current = mt_rand(0, $limit);

            $limits[$value->getValue()] = [
                'limit' => $limit,
                'current' => $current,
            ];

            $this->plan->setLimitValue($value, $limit);
            $this->subscription->setLimitValue($value, $current);
        }

        $permissions = [];
        /** @var AppPermissionEnum $value */
        foreach (AppPermissionEnum::getValues() as $value) {
            $allow = (boolean) mt_rand(0, 1);

            $permissions[$value->getValue()] = $allow;
            $this->plan->setPermission($value, $allow);
        }

        $restrictions = $this->user->getRestrictions();

        $this->assertCount(2, $restrictions);
        $this->assertArrayHasKey('limits', $restrictions);
        $this->assertArrayHasKey('permissions', $restrictions);
        $this->assertEquals($limits, $restrictions['limits']);
        $this->assertEquals($permissions, $restrictions['permissions']);
    }

    /**
     * @return void
     */
    public function setFirstNameWithoutRecipient()
    {
        $this->user->setFirstName('first name');

        $this->assertEquals('first name', $this->user->getFirstName());
    }

    /**
     * @return void
     */
    public function setFirstNameWithRecipient()
    {
        $recipient = new PersonRecipient();

        $this->user
            ->setRecipient($recipient)
            ->setFirstName('first name');

        $this->assertEquals('first name', $this->user->getFirstName());
        $this->assertEquals('first name', $recipient->getFirstName());
    }

    /**
     * @return void
     */
    public function setLastNameWithoutRecipient()
    {
        $this->user->setLastName('last name');

        $this->assertEquals('last name', $this->user->getLastName());
    }

    /**
     * @return void
     */
    public function setLastNameWithRecipient()
    {
        $recipient = new PersonRecipient();

        $this->user
            ->setRecipient($recipient)
            ->setLastName('last name');

        $this->assertEquals('last name', $this->user->getLastName());
        $this->assertEquals('last name', $recipient->getLastName());
    }

    /**
     * @return void
     */
    public function setEmailWithoutRecipient()
    {
        $this->user->setEmail('test@test.test');

        $this->assertEquals('test@test.test', $this->user->getEmail());
    }

    /**
     * @return void
     */
    public function setEmailWithRecipient()
    {
        $recipient = new PersonRecipient();

        $this->user
            ->setRecipient($recipient)
            ->setEmail('test@test.test');

        $this->assertEquals('test@test.test', $this->user->getEmail());
        $this->assertEquals('test@test.test', $recipient->getEmail());
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->plan = new Plan();
        $this->subscription = new PersonalSubscription();
        $this->subscription->setPlan($this->plan);

        $this->user = new User();
        $this->user->setBillingSubscription($this->subscription);
    }
}
