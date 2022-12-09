<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

/**
 * Class RefreshTokenFixture
 * @package AppBundle\DataFixtures\ORM
 */
class RefreshTokenFixture extends AbstractFixture
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
        if (! $this->checkEnvironment('test')) {
            return;
        }

        $token = new RefreshToken();
        $token
            ->setRefreshToken('user1_token')
            ->setUsername('test@email.com')
            ->setValid(date_create()->modify('+ 10 days'));

        $manager->persist($token);
        $manager->flush();
    }
}
