<?php

namespace AuthenticationBundle\Security\Http\Firewall;

use AppBundle\HttpFoundation\AppResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * Class AuthenticationListener
 * @package AuthenticationBundle\Security\Http\Firewall
 */
class AuthenticationListener implements ListenerInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $manager;

    /**
     * @var AuthenticationSuccessHandlerInterface
     */
    private $successHandler;

    /**
     * @var AuthenticationFailureHandlerInterface
     */
    private $failureHandler;

    /**
     * @var string
     */
    private $providerKey;

    /**
     * @param TokenStorageInterface                 $storage        A
     *                                                              TokenStorageInterface
     *                                                              instance.
     * @param AuthenticationManagerInterface        $manager        A
     *                                                              AuthenticationManagerInterface
     *                                                              instance.
     * @param AuthenticationSuccessHandlerInterface $successHandler A
     *                                                              AuthenticationSuccessHandlerInterface
     *                                                              instance.
     * @param AuthenticationFailureHandlerInterface $failureHandler A
     *                                                              AuthenticationFailureHandlerInterface
     *                                                              instance.
     * @param string                                $providerKey    Security
     *                                                              provider
     *                                                              name.
     */
    public function __construct(
        TokenStorageInterface $storage,
        AuthenticationManagerInterface $manager,
        AuthenticationSuccessHandlerInterface $successHandler,
        AuthenticationFailureHandlerInterface $failureHandler,
        $providerKey
    ) {
        $this->storage = $storage;
        $this->manager = $manager;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->providerKey = $providerKey;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event A GetResponseEvent instance.
     *
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // Check request method.
        // We allow only post request to authentication endpoint.
        if (! $request->isMethod('POST')) {
            $event->setResponse(AppResponse::create(
                'Invalid method.',
                AppResponse::HTTP_METHOD_NOT_ALLOWED
            ));
            return;
        }

        // Try to decode request body as json.
        $payload = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $event->setResponse(AppResponse::badRequest(
                'Json decode: '. json_last_error_msg() .'.'
            ));
            return;
        }

        // Check payload.
        if (! isset($payload['email'], $payload['password'])) {
            $event->setResponse(AppResponse::badRequest(
                'Credentials not provided.'
            ));
            return;
        }

        // Try to authenticate token.
        try {
            $token = new UsernamePasswordToken(
                trim($payload['email']),
                trim($payload['password']),
                $this->providerKey
            );
            $token = $this->manager->authenticate($token);
            $this->storage->setToken($token);

            $response = $this->successHandler
                ->onAuthenticationSuccess($request, $token);
        } catch (AuthenticationException $e) {
            $response = $this->failureHandler
                ->onAuthenticationFailure($request, $e);
        }

        $event->setResponse($response);
    }
}
