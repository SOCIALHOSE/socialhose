<?php

namespace AuthenticationBundle\Security\Http\Authentication;

use Gesdinet\JWTRefreshTokenBundle\Security\Authenticator\RefreshTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;

/**
 * Class RefreshTokenAuthenticator
 * @package AuthenticationBundle\Security\Http\Authentication
 */
class RefreshTokenAuthenticator extends BaseAuthenticator
{

    /**
     * @param Request $request     A Request instance.
     * @param string  $providerKey Firewall name.
     *
     * @return PreAuthenticatedToken
     */
    public function createToken(Request $request, $providerKey)
    {
        $refreshTokenString = $request->request->get('refreshToken');

        return new PreAuthenticatedToken(
            '',
            $refreshTokenString,
            $providerKey
        );
    }
}
