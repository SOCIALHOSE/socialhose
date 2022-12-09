<?php

namespace UserBundle\Controller\Security;

use ApiBundle\Controller\AbstractApiController;
use FOS\UserBundle\Model\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use UserBundle\Entity\User;
use UserBundle\Form\ResettingConfirmType;
use UserBundle\Form\ResettingRequestType;
use UserBundle\Mailer\MailerInterface;
use UserBundle\UserBundleServices;

/**
 * Class ResettingController
 * @package UserBundle\Controller\Security
 *
 * @Route("/resetting", service="user.controller.resetting")
 */
class ResettingController extends AbstractApiController
{

    /**
     * Request password resetting.
     * User will receive email with information about resetting account.
     *
     * Example
     *
     * Request:
     * ```json
     * {
     *  "email": "socialhose@mail.com"
     * }
     * ```
     *
     * User will receive email with link "../auth/reset-password/?resetting_token=12dasv...".
     *
     * @Route("/request", methods={ "POST" })
     * @ApiDoc(
     *  resource="Resetting",
     *  section="Security",
     *  input={
     *     "class"="UserBundle\Form\ResettingRequestType",
     *     "name"=false
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function requestAction(Request $request)
    {
        $form = $this->createForm(ResettingRequestType::class);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            $data = $form->getData();

            /** @var UserManagerInterface $manager */
            $manager = $this->get('fos_user.user_manager');

            $user = $manager->findUserByEmail($data['email']);
            if (! $user instanceof User) {
                $message = "A recovery link has been sent to {$data['email']}, if found in our system.";
                return $this->generateResponse($message, 400);
            }

            if (! $user->isEnabled()) {
                // This user id locked, so we don't send reset email to him.
                return $this->generateResponse('User is locked.', 400);
            }

            $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');
            if ($user->isPasswordRequestNonExpired($ttl)) {
                // This user already request password changing and reset token is not
                // expired yet.
                return $this->generateResponse('Already requested.', 400);
            }

            // Generate new confirmation token.
            /** @var TokenGeneratorInterface $tokenGenerator */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
            $user->setPasswordRequestedAt(new \DateTime());
            $manager->updateUser($user);

            // Send confirmation email to user.
            /** @var MailerInterface $mailer */
            $mailer = $this->get(UserBundleServices::MAILER);
            $mailer->sendPasswordResettingConfirmation($user);

            return $this->generateResponse();
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Confirm password resetting.
     *
     * Example
     *
     * Request:
     * ```json
     * {
     *  "confirmationToken": "12dasv ...",
     *  "password": "newPassword"
     * }
     * ```
     *
     * @Route("/confirm", methods={ "POST" })
     * @ApiDoc(
     *  resource="Resetting",
     *  section="Security",
     *  input={
     *     "class"="UserBundle\Form\ResettingConfirmType",
     *     "name"=false
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function confirmAction(Request $request)
    {
        $form = $this->createForm(ResettingConfirmType::class);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            /** @var UserManagerInterface $userManager */
            $userManager = $this->get('fos_user.user_manager');

            $data = $form->getData();

            $user = $userManager
                ->findUserByConfirmationToken($data['confirmationToken']);

            if ($user === null) {
                // Can't find user by provided confirmation token
                return $this->generateResponse('Invalid token.', 400);
            }

            if (! $user->isEnabled()) {
                // This user id locked, so we don't send reset email to him.
                return $this->generateResponse('User is locked.', 400);
            }

            $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');
            if (! $user->isPasswordRequestNonExpired($ttl)) {
                // This token is expired.
                return $this->generateResponse('Confirmation token expired.', 400);
            }

            // All ok.
            $user
                ->setPlainPassword($data['password'])
                ->setConfirmationToken(null)
                ->setPasswordRequestedAt(null);
            $userManager->updateUser($user);

            return $this->generateResponse();
        }

        return $this->generateResponse($form, 400);
    }
}
