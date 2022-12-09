<?php

namespace UserBundle\Controller\Security;

use ApiBundle\Controller\AbstractApiController;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Form\HubSpotRegistrationType;
use UserBundle\Manager\User\UserManagerInterface;

/**
 * Class HubSpotRegistrationController
 * @package UserBundle\Controller\Security
 *
 * @Route("/hubspot-registration", service="user.controller.hubspot_registration")
 */
class HubSpotRegistrationController extends AbstractApiController
{

    /**
     * Register new user.
     * Return empty response.
     *
     * @Route("", methods={ "POST" })
     *
     *
     * @param Request $request A Request instance.
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function registerAction(Request $request)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var \UserBundle\Entity\User $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $form = $this->createForm(HubSpotRegistrationType::class, $user);

        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword('');
            /** @var TokenGeneratorInterface $tokenGenerator */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());

            $userManager->updateUser($user);

            return $this->generateResponse([
                'code' => $user->getConfirmationToken(),
            ]);
        }

        return $this->generateResponse($form, 400);

    }

}
