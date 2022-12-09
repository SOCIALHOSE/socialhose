<?php

namespace AdminBundle\Controller\User;

use AdminBundle\Form\User\MasterUserType;
use AdminBundle\Form\User\SubscriberType;
use PaymentBundle\Enum\PaymentGatewayEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\PersonalSubscription;
use UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Enum\UserRoleEnum;
use \Symfony\Component\HttpFoundation\Response;

/**
 * User controller.
 *
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/users/masters")
 */
class MasterController extends AbstractUserController
{

    protected static $role = UserRoleEnum::MASTER_USER;
    protected static $formClass = MasterUserType::class;

    /**
     * @Route("/{id}/show"), requirements={ "id": "\d+" }
     * @Method({ "GET" })
     *
     * Finds and displays a user entity.
     *
     * @param User $user A User entity instance.
     *
     * @return Response
     */
    public function showAction(User $user)
    {
        $subscriber = new User();
        $subscriber->setMasterUser($user);

        $form = $this->createForm(
            SubscriberType::class,
            $subscriber,
            [
                'action' => $this->generateUrl('admin_user_subscriber_new'),
                'show_master_selector' => false,
            ]
        );

        return $this->render($this->getTemplate('show'), [
            'user' => $user,
            'subscriberForm' => $form->createView(),
            'listUrl' => $this->generateCRUDUrl('index'),
            'editUrl' => $this->generateCRUDUrl('edit', $user->getId()),
            'deleteUrl' => $this->generateCRUDUrl('delete', $user->getId()),
        ]);
    }

    /**
     * @param object|User $entity Created entity instance.
     *
     * @return null|Response
     */
    public function preCreate($entity)
    {
        if ($entity->hasRole(UserRoleEnum::MASTER_USER)) {
            //
            // Subscribe created masters to free plan.
            //
            $em = $this->getDoctrine()->getManager();

            $freePlan = current(array_filter(
                $em->getRepository(Plan::class)->findAll(),
                function (Plan $plan) {
                    return $plan->isFree();
                }
            ));

            if ($freePlan === null) {
                $this->addFlash('admin_error', 'Can\'t create master \'cause we don\'t have free plan');

                return $this->redirectToRoute($this->getRoute('index'));
            }

            $subscription = new PersonalSubscription();
            $subscription
                ->setPlan($freePlan)
                ->setGateway(PaymentGatewayEnum::paypal())
                ->setPayed(true)
                ->setOwner($entity);

            $entity->setBillingSubscription($subscription);
        }

        return null;
    }
}
