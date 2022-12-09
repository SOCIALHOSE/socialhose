<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use CacheBundle\Entity\SourceList;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class SourceListFixture
 * @package AppBundle\DataFixtures\ORM
 */
class SourceListFixture extends AbstractFixture
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

        $users = [
            $this->getReference('test@email.com'),
            $this->getReference('master@email.com'),
        ];

        for ($i = 1; $i <= 25; $i++) {
            $user = $users[$i % 2];

            $sourceList = new SourceList();
            $sourceList->setName('Source list '. $i);
            $sourceList->setUser($user);

            if ($this->getFaker()->boolean()) {
                $sourceList
                    ->setUpdatedBy($user)
                    ->setUpdatedAt(new \DateTime());
            }

            $manager->persist($sourceList);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}
