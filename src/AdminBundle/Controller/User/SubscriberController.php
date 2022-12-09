<?php

namespace AdminBundle\Controller\User;

use AdminBundle\Form\User\SubscriberType;
use AppBundle\Exception\LimitExceedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\UserRoleEnum;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SubscriberController
 * @package AdminBundle\Controller\User
 *
 * @Security("has_role('ROLE_ADMIN')")
 * @Route("/users/subscribers")
 */
class SubscriberController extends AbstractUserController
{

    protected static $role = UserRoleEnum::SUBSCRIBER;
    protected static $formClass = SubscriberType::class;

    /**
     * @param object|User $entity Created entity instance.
     *
     * @return null|Response
     */
    public function preCreate($entity)
    {
        if ($entity->hasRole(UserRoleEnum::SUBSCRIBER)) {
            $master = $entity->getMasterUser();

            try {
                $entity->useLimit(AppLimitEnum::subscriberAccounts());
            } catch (LimitExceedException $exception) {
                $this->addFlash('admin_error', 'Allowed limit for creating subscribers for this master is exceeded');

                return $this->redirectToRoute($this->getRoute('index'));
            }

            $entity->setBillingSubscription($master->getBillingSubscription());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
        }

        return null;
    }

    /**
     * @param object|User $entity Deleted entity instance.
     *
     * @return null|Response
     */
    public function preDelete($entity)
    {
        if ($entity->hasRole(UserRoleEnum::SUBSCRIBER)) {
            $master = $entity->getMasterUser();
            if ($master !== null) { // The impossible happens.
                $entity->releaseLimit(AppLimitEnum::subscriberAccounts());

                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();
            }
        }

        return null;
    }
}
