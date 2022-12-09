<?php

namespace AppBundle\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class BaseFixtureTrait
 * @package AppBundle\DataFixtures\ORM
 */
trait BaseFixtureTrait
{

    use ContainerAwareTrait;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * Check current application environment.
     *
     * @param string|string[] $expected Expected environment name or array of
     *                                  names.
     *
     * @return boolean
     */
    protected function checkEnvironment($expected)
    {
        $expected = (array) $expected;
        $environment = $this->container->getParameter('kernel.environment');

        return in_array($environment, $expected, true);
    }

    /**
     * @return Generator
     */
    protected function getFaker()
    {
        if ($this->faker === null) {
            $this->faker = Factory::create();
        }

        return $this->faker;
    }
}
