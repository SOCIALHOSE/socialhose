<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\AbstractCRUDController;
use ApiBundle\Form\ActivatedEntitiesBatchType;
use ApiBundle\Form\EntitiesBatchType;
use ApiBundle\Form\SubscribeToNotificationsBatchType;
use ApiBundle\Security\Inspector\InspectorInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Manager\Notification\NotificationManagerInterface;
use UserBundle\Security\Inspector\NotificationInspector;
use UserBundle\UserBundleServices;

/**
 * Class AbstractRecipientController
 *
 * @package UserBundle\Controller\V1
 */
abstract class AbstractRecipientController extends AbstractCRUDController
{

    /**
     * Batch remove of recipients.
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function batchDelete(Request $request)
    {
        $processor = function (Collection $recipients) {
            $em = $this->getManager();

            foreach ($recipients as $recipient) {
                $em->remove($recipient);
            }

            $em->flush();
        };

        return $this->batchProcessing(
            $request,
            InspectorInterface::DELETE,
            EntitiesBatchType::class,
            $processor
        );
    }

    /**
     * Batch activate/deactivate recipients.
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function batchActiveToggle(Request $request)
    {
        $processor = function (Collection $recipients, $active) {
            $em = $this->getManager();

            foreach ($recipients as $recipient) {
                $recipient->setActive($active);
                $em->persist($recipient);
            }
            $em->flush();
        };

        return $this->batchProcessing(
            $request,
            InspectorInterface::UPDATE,
            ActivatedEntitiesBatchType::class,
            $processor
        );
    }

    /**
     * Batch subscribe/unsubscribe specified recipient from notifications.
     *
     * @param Request $request A HTTP Request instance.
     * @param integer $id      A recipient entity instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function batchSubscriptionToggle(Request $request, $id)
    {
        $recipient = $this->getManager()->getRepository($this->entity)->find($id);

        if ($recipient === null) {
            $name = \app\c\getShortName($this->entity);
            // Remove 'Abstract' prefix if it exists.
            if (strpos($name, 'Abstract') !== false) {
                $name = substr($name, 8);
            }

            return $this->generateResponse("Can't find {$name} with id {$id}.", 404);
        }

        $reasons = $this->checkAccess(NotificationInspector::UPDATE, $recipient);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        $processor = function (Collection $notifications, $subscribed) use ($recipient) {
            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->subscriptionToggle(
                $recipient,
                $notifications->toArray(),
                (bool) $subscribed
            );
        };

        return $this->batchProcessing(
            $request,
            function (Collection $notifications, $subscribed) {
                return $subscribed ? NotificationInspector::SUBSCRIBE : NotificationInspector::UNSUBSCRIBE;
            },
            SubscribeToNotificationsBatchType::class,
            $processor
        );
    }
}
