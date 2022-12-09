<?php

namespace AppBundle\Exception;

use UserBundle\Entity\User;
use UserBundle\Enum\AppPermissionEnum;

/**
 * Class NotAllowedException
 *
 * Occurred when we user try to make operations which is not allowed for him.
 *
 * @package AppBundle\Exception
 */
class NotAllowedException extends \RuntimeException
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var AppPermissionEnum
     */
    private $permission;

    /**
     * NotAllowedException constructor.
     *
     * @param User              $user          Who try to make not allowed action.
     * @param AppPermissionEnum $appPermission Which permission is required.
     */
    public function __construct(User $user, AppPermissionEnum $appPermission)
    {
        parent::__construct(sprintf(
            'User \'%s\' is don\'t have \'%s\' permission',
            $user->getId(),
            $appPermission->getValue()
        ));

        $this->user = $user;
        $this->permission = $appPermission;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return AppPermissionEnum
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
