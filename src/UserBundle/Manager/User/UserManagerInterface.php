<?php

namespace UserBundle\Manager\User;

use FOS\UserBundle\Model\UserManagerInterface as BaseManagerInterface;
use UserBundle\Entity\User;

/**
 * Class UserManager
 *
 * @package UserBundle\Manager\User
 */
interface UserManagerInterface extends BaseManagerInterface
{

    /**
     * @param User $user A Confirmed user instance.
     *
     * @return User
     */
    public function confirmUser(User $user);
}
