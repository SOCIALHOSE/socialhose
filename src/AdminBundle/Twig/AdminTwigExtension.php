<?php

namespace AdminBundle\Twig;

use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;

/**
 * Class AdminTwigExtension
 *
 * @package AdminBundle\Twig
 */
class AdminTwigExtension extends \Twig_Extension
{

    /**
     * Map between role name and human readable role title.
     *
     * @var array
     */
    protected static $rolesMap = [
        UserRoleEnum::ADMIN       => 'Admin',
        UserRoleEnum::MASTER_USER => 'Master User',
        UserRoleEnum::SUPER_ADMIN => 'Super Admin',
        UserRoleEnum::SUBSCRIBER  => 'Subscriber',
    ];

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('humanReadableRoles', function (User $user) {
                $roles = $user->getRoles();
                $result = [];
                foreach ($roles as $role) {
                    if (array_key_exists($role, self::$rolesMap)) {
                        $result[$role] = self::$rolesMap[$role];
                    }
                }

                return $result;
            }),
        ];
    }
}
