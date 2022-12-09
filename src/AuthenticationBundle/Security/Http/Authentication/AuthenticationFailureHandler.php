<?php

namespace AuthenticationBundle\Security\Http\Authentication;

use AppBundle\HttpFoundation\AppMergeableResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler as BaseFailureHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class AuthenticationFailureHandler
 * Extends 'Lexik' AuthenticationFailureHandler because 'Gesdinet' RefreshToken
 * depends on 'Lexik' AuthenticationFailureHandler but not on
 * AuthenticationFailureHandlerInterface.
 *
 * @package AuthenticationBundle\Security\Http\Authentication
 */
class AuthenticationFailureHandler extends BaseFailureHandler
{

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request                 $request   A Request instance.
     * @param AuthenticationException $exception A AuthenticationException
     *                                           instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response The response to return,
     * never null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $response = AppMergeableResponse::unauthorized($exception->getMessage());

        $event = new AuthenticationFailureEvent($exception, $response);
        $this->dispatcher->dispatch(Events::AUTHENTICATION_FAILURE, $event);

        return $event->getResponse();
    }
}
