<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Organization;

/**
 * Class OrganizationFixture
 * @package AppBundle\DataFixtures\ORM
 */
class OrganizationFixture extends AbstractFixture
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
        if ($this->checkEnvironment('prod')) {
            return;
        }

        $organization = Organization::create()
            ->setName('Test Organization');
        $this->setReference('organization', $organization);
        $manager->persist($organization);

        $manager->flush();
    }
}
