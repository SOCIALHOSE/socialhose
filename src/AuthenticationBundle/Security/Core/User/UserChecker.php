<?php

namespace AuthenticationBundle\Security\Core\User;

use AuthenticationBundle\Security\Core\Exception\NotVerifiedException;
use AuthenticationBundle\Security\Core\Exception\PaymentRequiredException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\User\UserChecker as BaseUserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;

/**
 * Class UserChecker
 *
 * @package AuthenticationBundle\Security\Core\User
 */
class UserChecker extends BaseUserChecker
{

    /**
     * Checks the user account before authentication.
     *
     * @param UserInterface $user A UserInterface instance.
     *
     * @return void
     *
     * @throws AccountStatusException If user not pass check.
     */
    public function checkPreAuth(UserInterface $user)
    {
        parent::checkPreAuth($user);

        if (! $user instanceof User) {
            return;
        }

        if (! $user->isVerified()) {
            $exception = new NotVerifiedException('User account is not verified.');
            $exception->setUser($user);
            throw $exception;
        }

        if ($user->hasRole(UserRoleEnum::SUBSCRIBER) && ! $user->getBillingSubscription()->isPayed()) {
            $exception = new PaymentRequiredException('Billing subscription not paid.');
            $exception->setUser($user);
            throw $exception;
        }
    }
}
