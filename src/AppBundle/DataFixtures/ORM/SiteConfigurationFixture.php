<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\AppBundleServices;
use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class SiteConfigurationFixture
 * @package AppBundle\DataFixtures\ORM
 */
class SiteConfigurationFixture extends AbstractFixture
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
        $this->container->get(AppBundleServices::CONFIGURATION)
            ->syncWithDefinitions();
    }
}
