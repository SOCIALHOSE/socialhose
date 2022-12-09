<?php

namespace AdminBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use PaymentBundle\Agreement\AgreementManagerInterface;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use PaymentBundle\PaymentBundleServices;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Entity\User;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Manager\User\UserManagerInterface;
use UserBundle\Repository\UserRepository;
use UserBundle\UserBundleServices;

/**
 * Class UserVerificationController
 * @package AdminBundle\Controller
 *
 * @Route("/users/verifications")
 */
class UserVerificationController extends Controller
{

    const LIMIT = 20;

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/", methods={ "GET" })
     * @Template
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);

        $notVerifiedUsers = $repository->getNotVerifiedQueryBuilder();

        /** @var PaginatorInterface $paginator */
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $notVerifiedUsers,
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return [ 'users' => $pagination ];
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @Route("/{id}", methods={ "GET", "POST" }, requirements={ "id": "\d+" })
     * @Template
     *
     * @param Request $request A HTTP Request instance.
     * @param integer $id      A not verified user instance.
     *
     * @return array|RedirectResponse
     */
    public function showAction(Request $request, $id)
    {
        /** @var UserRepository $repository */
        $repository = $this->getDoctrine()->getRepository(User::class);

        $user = $repository->find($id);
        if (! $user instanceof User) {
            throw $this->createNotFoundException();
        }

        if ($user->isVerified() || ! $user->getBillingSubscription()->isPayed()) {
            return $this->redirectToRoute('admin_userverification_index');
        }

        if ($request->isMethod('post')) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            /** @var MailerInterface $mail */
            $mailer = $this->get(UserBundleServices::MAILER);

            if ($request->request->has('reject')) {
                //
                // Admin reject registration attempt.
                //
                /** @var PaymentGatewayFactoryInterface $factory */
                $factory = $this->get(PaymentBundleServices::PAYMENT_GATEWAY_FACTORY);
                /** @var AgreementManagerInterface $manager */
                $manager = $this->get(PaymentBundleServices::AGREEMENT_MANAGER);

                $user->getBillingSubscription()->cancel($factory, 'Account registration rejected');
                $manager->removeAgreement($user->getBillingSubscription());
                $userManager->deleteUser($user);

                $mailer->sendVerificationRejected($user);

                $this->addFlash('admin_success', sprintf(
                    '%s registration rejected and email sent to %s',
                    $user->getFullName(),
                    $user->getEmail()
                ));
            } elseif ($request->request->has('verify')) {
                //
                // User verified so we should generate password and email it to
                // user.
                //
                $password = $userManager->confirmUser($user);
                $mailer->sendVerificationSuccess($user, $password);

                $this->addFlash('admin_success', sprintf(
                    '%s registration verified and email sent to %s',
                    $user->getFullName(),
                    $user->getEmail()
                ));
            }

            return $this->redirectToRoute('admin_userverification_index');
        }

        $billingSubscription = $user->getBillingSubscription();
        $organization = null;
        if ($billingSubscription instanceof OrganizationSubscription) {
            $organization = $billingSubscription->getOrganization();
        }

        return [
            'user' => $user,
            'plan' => $billingSubscription->getPlan(),
            'subscription' => $billingSubscription,
            'organization' => $organization,
        ];
    }
}
