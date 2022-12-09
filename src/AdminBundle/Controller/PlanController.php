<?php

namespace AdminBundle\Controller;

use PaymentBundle\Enum\PaymentGatewayEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Form\PlanType;
use UserBundle\Repository\PlanRepository;
use UserBundle\Repository\SubscriptionRepository;

/**
 * Class PlanController
 * @package AdminBundle\Controller
 *
 * @Route("/plans")
 */
class PlanController extends Controller
{

    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Route("/", methods={ "GET" })
     * @Template
     *
     * @return array
     */
    public function indexAction()
    {
        /** @var PlanRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Plan::class);

        $plans = $repository->findAll();

        return [ 'plans' => $plans ];
    }

    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Route("/{id}", methods={ "GET", "POST" }, requirements={ "id": "\d+" })
     * @Template
     *
     * @param Request $request A HTTP Request instance.
     * @param integer $id      A Plan entity id.
     *
     * @return array|RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var PlanRepository $repository */
        $repository = $em->getRepository(Plan::class);
        $plan = $repository->find($id);

        if (! $plan instanceof Plan) {
            throw $this->createNotFoundException();
        }

        $previousPrice = $plan->getPrice();

        $form = $this->createForm(PlanType::class, $plan);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($plan);

            if (abs($plan->getPrice() - $previousPrice) >= 0.000001) {
                //
                // Plan price was changed.
                //
                // For now we unsubscribe and disable all subscribed to this plan
                // users, remove this plan for payment gateway and create new one
                //
                // todo rewrite this code when we find out that we should do with already subscribed users.
                //
                /** @var SubscriptionRepository $subscriptionRepository */
                $subscriptionRepository = $em->getRepository(AbstractSubscription::class);
                $subscriptions = $subscriptionRepository->getForPlan($plan->getId());

                $paymentGateway = $this->get('payment.gateway_factory')
                    ->getGateway(PaymentGatewayEnum::paypal());

                /** @var AbstractSubscription $subscription */
                foreach ($subscriptions as $subscription) {
                    $paymentGateway->cancelSubscription($subscription, 'Billing plan is removed');
                    $subscription
                        ->setPayed(false)
                        ->setPlan(null);

                    $em->persist($subscription);
                }

                if ($plan->isFree()) {
                    $paymentGateway->removePlan($plan);
                } else {
                    $paymentGateway->updatePlan($plan);
                }
            }

            $em->flush();

            return $this->redirect($request->getUri());
        }

        return [
            'form' => $form->createView(),
            'plan' => $plan,
        ];
    }
}
