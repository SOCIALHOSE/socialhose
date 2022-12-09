<?php

namespace AdminBundle\Controller\User;

use AdminBundle\Form\Search;
use AdminBundle\Form\Type\SearchType;
use FOS\UserBundle\Model\UserManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\UserBundleServices;

/**
 * Class AbstractUserController
 * @package AdminBundle\Controller\User
 */
abstract class AbstractUserController extends Controller
{

    protected static $role;
    protected static $formClass;
    protected static $limit = 20;

    /**
     * @Route("/")
     * @Method({"GET", "POST"})
     *
     * @param Request $request A Request instance.
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        $search = new Search();
        $searchForm = $this->createForm(SearchType::class, $search);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && !$searchForm->isValid()) {
            $search = new Search();
        }

        $query = $userRepository->getUserByRoleQB(new UserRoleEnum(static::$role), $search->getHandledQuery());

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            self::$limit
        );

        return $this->render($this->getTemplate('index'), [
            'users' => $pagination,
            'search' => $searchForm->createView(),
            'newUrl' => $this->generateCRUDUrl('new'),
            'deleteRoute' => $this->getRoute('delete'),
            'changeEnabledRoute' => $this->getRoute('changeEnabled'),
            'resendPasswordRoute' => $this->getRoute('resendPassword'),
            'showRoute' => $this->getRoute('show'),
            'editRoute' => $this->getRoute('edit'),
        ]);
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request A Request instance.
     *
     * @return Response
     */
    public function newAction(Request $request)
    {
        /** @var $userManager UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        /** @var \UserBundle\Entity\User $user */
        $user = $userManager->createUser();
        $user->setEnabled(true);

        $form = $this->createForm(static::$formClass, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$form->has('plainPassword')) {
                $user->generatePassword();
                $password = $user->getPlainPassword();
            } else {
                $password = $form->get('plainPassword')->getData();
            }

            $user
                ->setVerified(true)
                ->addRole(static::$role);

            if (($response = $this->preCreate($user)) !== null) {
                return $response;
            }

            $userManager->updateUser($user);

            $mailer = $this->get(UserBundleServices::MAILER);
            $mailer->sendPassword($user, $password);

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirect($this->generateCRUDUrl('show', $user->getId()));
        }

        if ($request->isXmlHttpRequest()) {
            $resolveName = static function (FormInterface $form) use (&$resolveName) {
                $parent = $form->getParent();

                if ($parent === null) {
                    return $form->getName();
                }

                return $resolveName($parent) .'_'. $form->getName();
            };

            return new JsonResponse([
                'success' => false,
                'errors' => array_map(function (FormError $error) use ($resolveName) {
                    return [
                        'field' => $resolveName($error->getOrigin()),
                        'message' => $error->getMessage(),
                    ];
                }, iterator_to_array($form->getErrors(true, true))),
            ], 400);
        }

        return $this->render($this->getTemplate('new'), [
            'user' => $user,
            'form' => $form->createView(),
            'indexUrl' => $this->generateCRUDUrl('index'),
        ]);
    }

    /**
     * @Route("/{id}/show"), requirements={ "id": "\d+" }
     * @Method({ "GET" })
     *
     * @param User $user A User entity instance.
     *
     * @return Response
     */
    public function showAction(User $user)
    {
        return $this->render($this->getTemplate('show'), [
            'user' => $user,
            'listUrl' => $this->generateCRUDUrl('index'),
            'editUrl' => $this->generateCRUDUrl('edit', $user->getId()),
            'deleteUrl' => $this->generateCRUDUrl('delete', $user->getId()),
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={ "id": "\d+" })
     * @Method({"GET", "POST"})
     *
     * @param Request $request A Request instance.
     * @param User    $user    A User entity instance.
     *
     * @return Response
     */
    public function editAction(Request $request, User $user)
    {
        $form = $this->createForm(static::$formClass, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('user.user_manager');

            if ((trim($user->getPlainPassword()) !== '') && $form->get('notifyAboutPassword')->getData()) {
                /** @var MailerInterface $mailer */
                $mailer = $this->get(UserBundleServices::MAILER);
                $mailer->sendPassword($user, $user->getPlainPassword());
            }

            $manager->updateUser($user);

            return $this->redirect($this->generateCRUDUrl('index'));
        }

        return $this->render($this->getTemplate('edit'), [
            'user' => $user,
            'form' => $form->createView(),
            'indexUrl' => $this->generateCRUDUrl('index'),
            'deleteUrl' => $this->generateCRUDUrl('delete', $user->getId()),
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={ "id": "\d+" })
     *
     * @param User $user A User entity instance.
     *
     * @return Response
     */
    public function deleteAction(User $user)
    {
        $manager = $this->get('user.user_manager');

        if (($response = $this->preDelete($user)) !== null) {
            return $response;
        }

        $manager->deleteUser($user);

        return $this->redirectToRoute($this->getRoute('index'));
    }

    /**
     * @param object $entity Deleted entity instance.
     *
     * @return null|Response
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function preDelete($entity)
    {
        return null;
    }

    /**
     * @param string $name CRUD operation name.
     *
     * @return string
     */
    public function getRoute($name)
    {
        $controllerName = strtolower(str_replace('Controller', '', \app\c\getShortName(static::class)));
        $name = strtolower($name);

        return "admin_user_{$controllerName}_{$name}";
    }

    /**
     * @param string $name CRUD operation name.
     *
     * @return string
     */
    public function getTemplate($name)
    {
        $controllerName = str_replace('Controller', '', \app\c\getShortName(static::class));

        return "AdminBundle:User\\{$controllerName}:$name.html.twig";
    }

    /**
     * @Route(
     *  "/{id}/change-enabled",
     *  requirements={ "id": "\d+" },
     *  condition="request.isXmlHttpRequest()"
     * )
     *
     * @param User $user A User entity instance.
     *
     * @return JsonResponse
     */
    public function changeEnabledAction(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setEnabled(! $user->isEnabled());

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'enabled' => $user->isEnabled(),
        ]);
    }

    /**
     * @Route(
     * "/{id}/resend-password",
     * requirements={ "id": "\d+" },
     * condition="request.isXmlHttpRequest()"
     * )
     *
     * @param User $user A User entity instance.
     *
     * @return JsonResponse
     */
    public function resendPasswordAction(User $user)
    {
        $manager = $this->get('user.user_manager');

        $user->generatePassword();
        $password = $user->getPlainPassword();
        $manager->updateUser($user);

        /** @var MailerInterface $mailer */
        $mailer = $this->get(UserBundleServices::MAILER);
        $mailer->sendPassword($user, $password);

        return new JsonResponse();
    }

    /**
     * @param string  $name Route name.
     * @param integer $user A User entity id.
     *
     * @return string
     */
    protected function generateCRUDUrl($name, $user = null)
    {
        $parameters = [];
        if ($user !== null) {
            $parameters = [ 'id' => $user ];
        }

        return $this->generateUrl($this->getRoute($name), $parameters);
    }

    /**
     * @param object $entity Created entity instance.
     *
     * @return null|Response
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function preCreate($entity)
    {
        return null;
    }
}
