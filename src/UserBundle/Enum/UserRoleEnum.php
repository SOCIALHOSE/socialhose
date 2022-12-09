<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class UserRoleEnum
 * @package UserBundle\Enum
 *
 * @method static UserRoleEnum subscriber()
 * @method static UserRoleEnum masterUser()
 * @method static UserRoleEnum admin()
 * @method static UserRoleEnum superAdmin()
 */
class UserRoleEnum extends AbstractEnum
{

    const SUBSCRIBER = 'ROLE_SUBSCRIBER';
    const MASTER_USER = 'ROLE_MASTER_USER';
    const ADMIN = 'ROLE_ADMIN';
    const SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
