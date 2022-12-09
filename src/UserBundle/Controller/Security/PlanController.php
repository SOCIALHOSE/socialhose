<?php

namespace UserBundle\Controller\Security;

use ApiBundle\Controller\AbstractApiController;
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
 * @package UserBundle\Controller\Security
 *
 * @Route("/plans", service="user.controller.plan")
 */
class PlanController extends AbstractApiController
{
    /**
     * @Route("", methods={ "GET" })
     *
     * @return array
     */
    public function indexAction()
    {

        $repository = $this->getManager()->getRepository(Plan::class);

        $qb = $repository->createQueryBuilder('p');
        $query = $qb
            ->where('p.is_default = true')
            ->andwhere('p.title != :title')
            ->setParameters(array(
                'title' => 'Free'
            ));
        $query = $query->getQuery();
        $plans = $query->getResult();

        if (count($plans) === 0) {
            return $this->generateResponse("Can't find plans.", 404);
        }

        return $this->generateResponse($plans, 200, [
            'id',
            'plan'
        ]);
    }

}
