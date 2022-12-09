<?php

namespace AuthenticationBundle\Security\Core\User;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Security\UserProvider;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Utils\RoleChecker\RoleCheckerInterface;

/**
 * Class EmailUserProvider
 * @package AuthenticationBundle\Security\Core\User
 */
class EmailUserProvider extends UserProvider
{

    /**
     * @var RoleCheckerInterface
     */
    private $roleChecker;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager A UserManagerInterface instance.
     * @param RoleCheckerInterface $roleChecker A RoleCheckerInterface instance.
     */
    public function __construct(
        UserManagerInterface $userManager,
        RoleCheckerInterface $roleChecker
    ) {
        parent::__construct($userManager);
        $this->roleChecker = $roleChecker;
    }

    /**
     * Finds a user by email.
     *
     * @param string $email User email.
     *
     * @return UserInterface|null
     */
    protected function findUser($email)
    {
        $user = $this->userManager->findUserByEmail($email);

        // Restrict super admin and ordinal admin's login to front side.
        if (($user instanceof User)
            && $this->roleChecker->has($user, UserRoleEnum::SUBSCRIBER)) {
            return $user;
        }

        return null;
    }
}
