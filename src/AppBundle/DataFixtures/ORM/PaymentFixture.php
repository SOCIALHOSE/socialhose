<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use PaymentBundle\Entity\Model\Money;
use PaymentBundle\Entity\Payment;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Enum\PaymentStatusEnum;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class PaymentFixture
 * @package AppBundle\DataFixtures\ORM
 */
class PaymentFixture extends AbstractFixture
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
        if (! $this->checkEnvironment('dev')) {
            return;
        }

        /** @var AbstractSubscription[] $subscriptions */
        $subscriptions = [
            $this->getReference('first_subscription'),
            $this->getReference('second_subscription'),
            $this->getReference('personal_subscription'),
        ];

        $faker = $this->getFaker();
        for ($i = 0; $i < 50; $i++) {
            $payment = Payment::create()
                ->setGateway(PaymentGatewayEnum::paypal())
                ->setAmount(new Money($faker->randomFloat(2, 10, 20), 'USD'))
                ->setStatus(new PaymentStatusEnum($faker->randomElement(PaymentStatusEnum::getAvailables())))
                ->setSubscription($faker->randomElement($subscriptions))
                ->setTransactionId($faker->md5);
            $manager->persist($payment);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture.
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}
