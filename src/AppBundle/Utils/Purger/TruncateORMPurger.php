<?php

namespace AppBundle\Utils\Purger;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;

/**
 * Class TruncateORMPurger
 *
 * Simple purger which purge only with truncate.
 * Decorate ORMPurger instance.
 *
 * @package AppBundle\Utils\Purger
 */
class TruncateORMPurger implements PurgerInterface
{

    /**
     * @var ORMPurger
     */
    private $purger;

    /**
     * TruncateORMPurger constructor.
     *
     * @param ORMPurger $purger A ORMPurger instance.
     */
    public function __construct(ORMPurger $purger)
    {
        $this->purger = $purger;
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
    }


    /**
     * Purge the data from the database for the given EntityManager.
     *
     * @return void
     */
    public function purge()
    {
        $connection = $this->purger->getObjectManager()->getConnection();
        $connection->exec('SET FOREIGN_KEY_CHECKS = 0');
        $this->purger->purge();
        $connection->exec('SET FOREIGN_KEY_CHECKS = 1');
    }
}
