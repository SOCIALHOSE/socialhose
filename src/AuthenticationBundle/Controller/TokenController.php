<?php

namespace AuthenticationBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use AppBundle\HttpFoundation\AppResponse;
use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TokenController
 * @package AuthenticationBundle\Controller
 *
 * @Route("/token", service="authentication_bundle.controller.token")
 */
class TokenController extends AbstractApiController
{

    /**
     * Create JWT token for specified user.
     *
     * @Route("/create", methods={ "POST" })
     * @AppApiDoc(
     *  authentication=false,
     *  resource="Authentication",
     *  section="Security",
     *  parameters={
     *      {
     *          "name"="email",
     *          "dataType"="string",
     *          "required"=true,
     *          "description"="User email."
     *      },
     *      {
     *          "name"="password",
     *          "dataType"="string",
     *          "required"=true,
     *          "description"="User password."
     *      }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *          "user"={
     *              "class"="UserBundle\Entity\User",
     *              "groups"={ "user", "id", "recipient", "restrictions" }
     *          },
     *          "token"={
     *              "dataType"="string",
     *              "required"=true
     *          },
     *          "refreshToken"={
     *              "dataType"="string",
     *              "required"=true
     *          },
     *     }
     *  },
     *  statusCodes={
     *     200="Token successfully created.",
     *     400="Provided invalid data.",
     *     401="Provided invalid credentials or not provided at all."
     *  }
     * )
     *
     * @return void
     */
    public function createAction()
    {
        // Dummy method. Used only for documentation.
    }

    /**
     * Refresh JWT token by refresh token.
     *
     * @Route("/refresh", methods={ "POST" })
     * @AppApiDoc(
     *  authentication=false,
     *  resource="Authentication",
     *  section="Security",
     *  parameters={
     *      {
     *          "name"="refreshToken",
     *          "dataType"="string",
     *          "required"=true,
     *          "description"="Refresh token."
     *      }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *          "user"={
     *              "class"="UserBundle\Entity\User",
     *              "groups"={ "user", "id", "recipient", "restrictions" }
     *          },
     *          "token"={
     *              "dataType"="string",
     *              "required"=true
     *          },
     *          "refreshToken"={
     *              "dataType"="string",
     *              "required"=true
     *          }
     *     }
     *  },
     *  statusCodes={
     *     200="Token successfully refreshed.",
     *     400="Refresh token not provided.",
     *     401="Provided invalid refresh token or not provided at all."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return AppResponse
     */
    public function refreshAction(Request $request)
    {
        /** @var RefreshToken $refresher */
        $refresher = $this->get('gesdinet.jwtrefreshtoken');

        if (! $request->request->get('refreshToken')) {
            return AppResponse::badRequest('refreshToken: This value should not be null.');
        }

        return $refresher->refresh($request);
    }
}
