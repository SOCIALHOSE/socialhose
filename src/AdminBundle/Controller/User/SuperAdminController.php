<?php

namespace AdminBundle\Controller\User;

use AdminBundle\Form\User\AdminType;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class SuperAdminController
 * @package AdminBundle\Controller\User
 *
 * @Security("has_role('ROLE_SUPER_ADMIN')")
 * @Route("/users/super-admin")
 */
class SuperAdminController extends Controller
{

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/edit")
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request A Request instance.
     *
     * @return RedirectResponse|array
     */
    public function editAction(Request $request)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $this->getUser();

        $form = $this->createForm(AdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->updateUser($user);

            return $this->redirect($request->getUri());
        }

        return [
            'user' => $user,
            'form' => $form->createView(),
        ];
    }
}
