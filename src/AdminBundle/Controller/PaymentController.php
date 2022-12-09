<?php

namespace AdminBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use PaymentBundle\Entity\Payment;
use PaymentBundle\Repository\PaymentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaymentController
 * @package AdminBundle\Controller
 *
 * @Route("/payment")
 */
class PaymentController extends Controller
{

    const LIMIT = 10;

    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Route("/", methods={ "GET" })
     * @Template
     *
     * @param Request $request A HTTP Request.
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        /** @var PaginatorInterface $paginator */
        $paginator = $this->get('knp_paginator');
        /** @var PaymentRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Payment::class);

        $pagination = $paginator->paginate(
            $repository->getListQueryBuilder(),
            $request->query->getInt('page', 1),
            self::LIMIT
        );

        return [
            'payments' => $pagination,
        ];
    }
}
