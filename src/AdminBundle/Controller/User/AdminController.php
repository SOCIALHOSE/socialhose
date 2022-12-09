<?php

namespace AdminBundle\Controller\User;

use AdminBundle\Form\User\AdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Enum\UserRoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AdminController
 * @package AdminBundle\Controller\User
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/users/admins")
 */
class AdminController extends AbstractUserController
{

    protected static $role = UserRoleEnum::ADMIN;
    protected static $formClass = AdminType::class;
}
