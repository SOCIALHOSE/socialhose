<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Class SecurityController
 * @package AdminBundle\Controller
 */
class SecurityController extends Controller
{

    /**
     * @Route("/login", name="admin_login")
     * @Template
     *
     * @param Request $request A Request instance.
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ];
    }

    /**
     * @Route("/login_check", name="admin_login_check")
     *
     * @return void
     */
    public function loginCheckAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

    /**
     * @Route("/logout", name="admin_logging_out")
     *
     * @return void
     */
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
