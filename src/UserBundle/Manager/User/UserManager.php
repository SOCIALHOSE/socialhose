<?php

namespace UserBundle\Manager\User;

use CacheBundle\Entity\Category;
use FOS\UserBundle\Doctrine\UserManager as BaseManager;
use FOS\UserBundle\Model\UserInterface;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;

/**
 * Class UserManager
 *
 * @package UserBundle\Manager\User
 */
class UserManager extends BaseManager implements UserManagerInterface
{

    /**
     * Deletes a user.
     *
     * @param UserInterface $user A UserInterface entity instance.
     *
     * @return void
     */
    public function deleteUser(UserInterface $user)
    {
        if (! $user instanceof User) {
            throw new \InvalidArgumentException('Expects instance of ' . User::class);
        }

        $this->objectManager->remove($user->getRecipient());
        $user->setRecipient(null);

        $billingSubscriptions = $user->getBillingSubscription();
        $billingSubscriptions->removeUser($user);
        $user->setBillingSubscription(null);

        if ($billingSubscriptions->isOwnedBy($user)) {
            $this->objectManager->remove($billingSubscriptions);
        }

        parent::deleteUser($user);
    }

    /**
     * Updates a user.
     *
     * @param UserInterface $user     A UserInterface entity instance.
     * @param boolean       $andFlush Flush data to storage.
     *
     * @return void
     */
    public function updateUser(UserInterface $user, $andFlush = true)
    {
        if (! $user instanceof User) {
            throw new \InvalidArgumentException('Expects instance of ' . User::class);
        }

        if (($user->getId() === null)
            && ($user->hasRole(UserRoleEnum::SUBSCRIBER)
            || $user->hasRole(UserRoleEnum::MASTER_USER))
        ) {
            //
            // For all new users we create recipient with their emails.
            //
            $recipient = PersonRecipient::createFromUser($user)
                ->setAssociatedUser($user)
                ->setOwner($user);
            $this->objectManager->persist($recipient);
        }

        parent::updateUser($user, $andFlush);
    }

    /**
     * @param User $user A Confirmed user instance.
     *
     * @return string New password.
     */
    public function confirmUser(User $user)
    {

        Category::createMainCategory($user);
        Category::createSharedCategory($user);
        Category::createTrashCategory($user);

        $user
            ->setVerified()
            ->setEnabled(true)
            ->generatePassword();

        $password = $user->getPlainPassword();
        $this->updateUser($user);

        return $password;
    }
}
