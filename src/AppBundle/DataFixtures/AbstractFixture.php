<?php

namespace AppBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture as BaseClass;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Class AbstractFixture
 * @package AppBundle\DataFixtures\ORM
 */
abstract class AbstractFixture extends BaseClass implements
    ContainerAwareInterface,
    OrderedFixtureInterface
{

    use BaseFixtureTrait;

    /**
     * Get the order of this fixture.
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
