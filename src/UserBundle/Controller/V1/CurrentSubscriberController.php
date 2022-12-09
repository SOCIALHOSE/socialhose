<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Controller\Annotation\Roles;
use FOS\UserBundle\Model\UserManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Enum\UserRoleEnum;
use UserBundle\Form\SubscriberType;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Repository\UserRepository;
use UserBundle\UserBundleServices;

/**
 * Class CurrentSubscriberController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/users/current/subscribers",
 *     service="user.controller.current_subscriber"
 * )
 */
class CurrentSubscriberController extends AbstractApiController
{

    /**
     * Get list of subscriber for current master.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("", methods={ "GET" })
     * @ApiDoc(
     *  resource="Current user subscribers",
     *  section="User",
     *  filters={
     *     {
     *          "name"="page",
     *          "dataType"="integer",
     *          "description"="Requested page number, start from 1",
     *          "requirements"="\d+",
     *          "default"="1"
     *     },
     *     {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "description"="Max entities per page, default 100",
     *          "requirements"="\d+",
     *          "default"="100"
     *     }
     *  },
     *  output={
     *     "class"="Pagination<UserBundle\Entity\User>",
     *     "groups"={ "subscriber", "id" }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        /** @var UserRepository $repository */
        $repository = $this->getManager()->getRepository('UserBundle:User');
        $user = $this->getCurrentUser();

        $pagination = $this->paginate(
            $request,
            $repository->getSubscribersQueryBuilder($user->getId())
        );

        return $this->generateResponse($pagination, 200, [ 'subscriber', 'id' ]);
    }

    /**
     * Get information about subscriber.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/{id}", requirements={ "id"="\d+" }, methods={ "GET" })
     * @ApiDoc(
     *  resource="Current user subscribers",
     *  section="User",
     *  output={
     *     "class"="Pagination<UserBundle\Entity\User>",
     *     "groups"={ "subscriber", "id" }
     *  },
     *  statusCodes={
     *     200="Subscriber successfully returned."
     *  }
     * )
     *
     * @param integer $id A subscriber User entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function getAction($id)
    {
        /** @var UserManagerInterface $manager */
        $manager = $this->get('fos_user.user_manager');

        $current = $this->getCurrentUser();
        $user = $manager->findUserBy([
            'id' => $id,
            'masterUser' => $current->getId(),
        ]);

        if ($user === null) {
            return $this->generateResponse("Can't find subscriber with id {$id}.", 404);
        }

        return $this->generateResponse($user, 200, [ 'subscriber', 'id' ]);
    }

    /**
     * Create new subscriber for current user.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("", methods={ "POST" })
     * @ApiDoc(
     *  resource="Current user subscribers",
     *  section="User",
     *  input={
     *     "class"="UserBundle\Form\SubscriberType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="Pagination<UserBundle\Entity\User>",
     *     "groups"={ "subscriber", "id" }
     *  },
     *  statusCodes={
     *     200="Subscriber successfully created."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return User|\ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        $current = $this->getCurrentUser();
        $user = new User();
        $user
            ->generatePassword()
            ->setMasterUser($current)
            ->setRoles([ UserRoleEnum::SUBSCRIBER ]);

        $form = $this->createForm(SubscriberType::class, $user);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            /** @var UserManagerInterface $manager */
            $manager = $this->get('fos_user.user_manager');

            $password = $user->getPlainPassword();
            $manager->updateUser($user);

            /** @var MailerInterface $mailer */
            $mailer = $this->get(UserBundleServices::MAILER);
            $mailer->sendPassword($user, $password);

            return $this->generateResponse($user, 200, [ 'subscriber', 'id' ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Update current user subscriber.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/{id}", requirements={ "id"="\d+" }, methods={ "PUT" })
     * @ApiDoc(
     *  resource="Current user subscribers",
     *  section="User",
     *  input={
     *     "class"="UserBundle\Form\SubscriberType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="Pagination<UserBundle\Entity\User>",
     *     "groups"={ "subscriber", "id" }
     *  },
     *  statusCodes={
     *     200="Subscriber successfully updated.",
     *     404="Can't find category by specified id."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A subscriber User entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $id)
    {
        /** @var UserManagerInterface $manager */
        $manager = $this->get('fos_user.user_manager');

        $current = $this->getCurrentUser();
        $user = $manager->findUserBy([
            'id' => $id,
            'masterUser' => $current->getId(),
        ]);

        if ($user === null) {
            return $this->generateResponse("Can't find subscriber with id {$id}.", 404);
        }

        $form = $this->createForm(SubscriberType::class, $user, [
            'method' => 'PUT',
        ]);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            $manager->updateUser($user);

            return $this->generateResponse($user, 200, [ 'subscriber', 'id' ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Update current user subscriber.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/{id}", requirements={ "id"="\d+" }, methods={ "DELETE" })
     * @ApiDoc(
     *  resource="Current user subscribers",
     *  section="User",
     *  statusCodes={
     *     200="Subscriber successfully deleted.",
     *     404="Can't find category by specified id."
     *  }
     * )
     *
     * @param integer $id A subscriber User entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteAction($id)
    {
        /** @var UserManagerInterface $manager */
        $manager = $this->get('fos_user.user_manager');

        $current = $this->getCurrentUser();
        $user = $manager->findUserBy([
            'id' => $id,
            'masterUser' => $current->getId(),
        ]);

        if ($user === null) {
            return $this->generateResponse("Can't find subscriber with id {$id}.", 404);
        }

        $manager->deleteUser($user);

        return $this->generateResponse();
    }
}
