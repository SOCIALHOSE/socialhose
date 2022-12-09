<?php

namespace AuthenticationBundle\Security\Http\Authentication;

use AppBundle\HttpFoundation\AppMergeableResponse;
use FOS\UserBundle\Doctrine\UserManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler as BaseSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AuthenticationSuccessHandler
 *
 * Extends 'Lexik' AuthenticationSuccessHandler because 'Gesdinet' RefreshToken
 * depends on 'Lexik' AuthenticationSuccessHandler but not on
 * AuthenticationSuccessHandlerInterface.
 *
 * @package AuthenticationBundle\Security\Http\Authentication
 */
class AuthenticationSuccessHandler extends BaseSuccessHandler
{

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * AuthenticationSuccessHandler constructor.
     *
     * @param JWTManager               $jwtManager  A JWTManager instance.
     * @param EventDispatcherInterface $dispatcher  A EventDispatcherInterface
     *                                              instance.
     * @param NormalizerInterface      $normalizer  A NormalizerInterface
     *                                              instance.
     * @param UserManager              $userManager A UserManager instance.
     */
    public function __construct(
        JWTManager $jwtManager,
        EventDispatcherInterface $dispatcher,
        NormalizerInterface $normalizer,
        UserManager $userManager
    ) {
        parent::__construct($jwtManager, $dispatcher);
        $this->normalizer = $normalizer;
        $this->userManager = $userManager;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request        $request A Request instance.
     * @param TokenInterface $token   A TokenInterface instance.
     *
     * @return \Symfony\Component\HttpFoundation\Response The response to return,
     * never null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $user = $token->getUser();

        // Update use last login.
        //
        // We not flush changes right now because we also generate refresh token
        // and put it too into database.
        //
        $user->setLastLogin(new \DateTime());
        $this->userManager->updateUser($user, false);

        $jwtToken = $this->jwtManager->create($user);

        $response = AppMergeableResponse::create()
            ->setData([
                'user' => $this->normalizer->normalize($user, null, [
                    'id',
                    'user',
                    'recipient',
                    'restrictions',
                ]),
                'token' => $jwtToken,
            ])
            ->setOriginalPriority(true);

        $event = new AuthenticationSuccessEvent(
            [ 'token' => $jwtToken ],
            $user,
            $response
        );
        $this->dispatcher->dispatch(Events::AUTHENTICATION_SUCCESS, $event);

        return $response->setData($event->getData());
    }
}
