<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Entity\Notification\NotificationThemeOptions;

/**
 * Class NotificationThemeFixtures
 * @package AppBundle\DataFixtures\ORM
 */
class NotificationThemeFixtures extends AbstractFixture
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager|EntityManagerInterface $manager A ObjectManager instance.
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $defaultOptions = NotificationThemeOptions::createDefault();

        //
        // Create default theme which should not be editable by master users.
        //
        $default = NotificationTheme::create()
            ->setName('Socialhose theme')
            ->setEnhanced($defaultOptions)
            ->setPlain($defaultOptions)
            ->setDefault(true);

        $this->addReference('default_notification_theme', $default);

        $manager->persist($default);
        $manager->flush();
    }
}
