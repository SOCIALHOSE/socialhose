<?php

namespace AuthenticationBundle\EventListener;

use AppBundle\HttpFoundation\AppResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AuthenticationSubscriber
 * @package AuthenticationBundle\EventListener
 */
class AuthenticationSubscriber implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and
     *  respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority),
     *  array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthSuccess',
            Events::JWT_EXPIRED => 'onExpired',
            Events::JWT_INVALID => 'onInvalid',
        ];
    }

    /**
     * @param AuthenticationSuccessEvent $event A AuthenticationSuccessEvent
     *                                          instance.
     *
     * @return void
     */
    public function onAuthSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();

        //
        // Change refresh token name to camelCase.
        //
        if (isset($data['refresh_token'])) {
            $data['refreshToken'] = $data['refresh_token'];
            unset($data['refresh_token']);
        }

        $event->setData($data);
    }

    /**
     * Change default response about expired token to proper response.
     *
     * @param JWTExpiredEvent $event A JWTExpiredEvent instance.
     *
     * @return void
     */
    public function onExpired(JWTExpiredEvent $event)
    {
        $event->setResponse(AppResponse::unauthorized('Expired JWT Token.'));
    }

    /**
     * Change default response about invalid token to proper response.
     *
     * @param JWTInvalidEvent $event A JWTInvalidEvent instance.
     *
     * @return void
     */
    public function onInvalid(JWTInvalidEvent $event)
    {
        $event->setResponse(AppResponse::unauthorized('Invalid JWT Token.'));
    }
}
