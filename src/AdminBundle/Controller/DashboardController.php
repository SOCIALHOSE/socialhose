<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class DashboardController
 * @package AdminBundle\Controller
 */
class DashboardController extends Controller
{
    /**
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     * @Route("/", name="admin_dashboard")
     * @Template
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }
}
