<?php

namespace AuthenticationBundle\Security\Core\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class NotVerifiedException
 *
 * @package AuthenticationBundle\Security\Core\Exception
 */
class NotVerifiedException extends AccountStatusException
{

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'Account is not verified.';
    }
}
