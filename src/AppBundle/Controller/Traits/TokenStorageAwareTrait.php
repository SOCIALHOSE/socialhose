<?php


namespace AppBundle\Controller\Traits;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\User;

/**
 * Trait TokenStorageAwareTrait
 *
 * @package AppBundle\Controller\Traits
 * @deprecated
 */
trait TokenStorageAwareTrait
{

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * Return current use if it exists.
     *
     * @return User|null
     */
    public function getCurrentUser()
    {
        $user = null;
        $token = $this->tokenStorage->getToken();

        if ($token !== null) {
            $user = $token->getUser();
        }

        return $user;
    }
}
