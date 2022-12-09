<?php

namespace AuthenticationBundle\Security\Core\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Class PaymentRequiredException
 *
 * @package AuthenticationBundle\Security\Core\Exception
 */
class PaymentRequiredException extends AccountStatusException
{

    /**
     * Message key to be used by the translation component.
     *
     * @return string
     */
    public function getMessageKey()
    {
        return 'Payment awaiting.';
    }
}
