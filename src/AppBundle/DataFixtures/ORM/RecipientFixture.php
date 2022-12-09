<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;

/**
 * Class RecipientFixture
 * @package AppBundle\DataFixtures\ORM
 */
class RecipientFixture extends AbstractFixture
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
        if (! $this->checkEnvironment('dev')) {
            return;
        }

        /** @var User[] $users */
        $users = [
            $this->getReference('test@email.com'),
            $this->getReference('master@email.com'),
        ];

        $faker = $this->getFaker();

        foreach ($users as $user) {
            if ($user->hasRole(UserRoleEnum::MASTER_USER)) {
                $personCount = random_int(4, 10);
                $groupCount = random_int(3, 5);
                $persons = [];

                for ($i = 0; $i < $personCount; ++$i) {
                    $person = PersonRecipient::create()
                        ->setFirstName($faker->firstName)
                        ->setLastName($faker->lastName)
                        ->setEmail($faker->email)
                        ->setOwner($user);

                    $persons[] = $person;

                    $manager->persist($person);
                }

                for ($i = 0; $i < $groupCount; ++$i) {
                    $groupPersons = $faker->randomElements($persons, random_int(2, 4));

                    $group = GroupRecipient::create()
                        ->setName($faker->word)
                        ->setDescription($faker->realText())
                        ->setOwner($user);

                    foreach ($groupPersons as $person) {
                        $group->addRecipient($person);
                    }

                    $manager->persist($group);
                }

                $manager->persist($user);
                $manager->flush();
            }
        }
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
