<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\OrganizationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Organization;
use UserBundle\Entity\Subscription\OrganizationSubscription;
use UserBundle\Repository\OrganizationRepository;
use UserBundle\Repository\SubscriptionRepository;

/**
 * Class OrganizationController
 * @package AdminBundle\Controller
 *
 * @Route("/organization")
 */
class OrganizationController extends Controller
{

    const LIMIT = 20;

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/")
     * @Template
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var OrganizationRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Organization::class);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $repository->getListQueryBuilder(),
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return [
            'organizations' => $pagination,
        ];
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/{id}", methods={ "GET", "POST" }, requirements={ "id": "\d+" })
     * @Template
     *
     * @param Request $request A HTTP Request instance.
     * @param integer $id      A Organization entity id.
     *
     * @return array|Response
     */
    public function editAction(Request $request, $id)
    {
        /** @var OrganizationRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Organization::class);

        $organization = $repository->find($id);
        if (! $organization instanceof Organization) {
            throw $this->createNotFoundException();
        }

        /** @var SubscriptionRepository $repository */
        $repository = $this->getDoctrine()->getRepository(OrganizationSubscription::class);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $repository->getForOrganization($organization->getId()),
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        $form = $this->createForm(OrganizationType::class, $organization);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($organization);
            $em->flush();

            return $this->redirect($request->getUri());
        }

        return [
            'form' => $form->createView(),
            'organization' => $organization,
            'subscriptions' => $pagination,
        ];
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/{id}/delete", methods={ "GET" }, requirements={ "id": "\d+" })
     *
     * @param integer $id A Organization entity id.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        /** @var OrganizationRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Organization::class);

        $organization = $repository->find($id);
        if (! $organization instanceof Organization) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($organization);
        $em->flush();

        return $this->redirectToRoute('admin_organization_index');
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route(
     *     "/{organizationId}/subscription/{subscriptionId}",
     *     methods={ "GET" },
     *     requirements={
     *      "organizationId": "\d+",
     *      "subscriptionId": "\d+"
     *     }
     * )
     *
     * @param integer $organizationId A Organization entity id.
     * @param integer $subscriptionId A billing Subscription entity id.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function subscriptionDeleteAction($organizationId, $subscriptionId)
    {
        /** @var OrganizationRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Organization::class);

        $organization = $repository->find($organizationId);
        if (! $organization instanceof Organization) {
            throw $this->createNotFoundException();
        }

        /** @var SubscriptionRepository $repository */
        $repository = $this->getDoctrine()->getRepository(OrganizationSubscription::class);

        $subscription = $repository->find($subscriptionId);
        if ((! $subscription instanceof OrganizationSubscription) || ($subscription->getOrganization()->getId() === $organizationId)) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($subscription);
        $em->flush();

        return $this->redirectToRoute('admin_organization_edit', [
            'id' => $organizationId,
        ]);
    }
}
