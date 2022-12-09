<?php

namespace UserBundle\Manager\Notification;

use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Recipient\AbstractRecipient;

/**
 * Interface NotificationManagerInterface
 * @package UserBundle\Manager\Notification
 */
interface NotificationManagerInterface
{

    /**
     * Add new notification or update exists.
     *
     * @param Notification $notification A Notification instance.
     *
     * @return void
     */
    public function persists(Notification $notification);

    /**
     * Activate specified notifications.
     *
     * @param Notification|Notification[] $notifications A Notification entity
     *                                                   instance or array of
     *                                                   instances.
     * @param boolean                     $active        Activate or deactivate
     *                                                   specified notifications.
     *
     * @return void
     */
    public function activatedToggle($notifications, $active = true);

    /**
     * Publish specified notifications.
     *
     * @param Notification|Notification[] $notifications A Notification entity
     *                                                   instance or array of
     *                                                   instances.
     * @param boolean                     $publish       Publish or make private
     *                                                   specified notifications.
     *
     * @return void
     */
    public function publishedToggle($notifications, $publish = true);

    /**
     * Publish specified notifications.
     *
     * @param AbstractRecipient           $recipient     Who try to subscribe or
     *                                                   unsubscribe from specified
     *                                                   notifications.
     * @param Notification|Notification[] $notifications A Notification entity
     *                                                   instance or array of
     *                                                   instances.
     * @param boolean                     $subscribe     Subscribe or unsubscribe
     *                                                   from specified notifications.
     *
     * @return void
     */
    public function subscriptionToggle(AbstractRecipient $recipient, $notifications, $subscribe = true);

    /**
     * Remove specified notifications.
     *
     * @param Notification|Notification[] $notifications A removed Notification
     *                                                   entity instance or array
     *                                                   of instances.
     *
     * @return void
     */
    public function remove($notifications);

    /**
     * Prepare specified notification for sending.
     *
     * @param Notification $notification A Notification instance.
     *
     * @return SendableNotification
     */
    public function prepareToSend(Notification $notification);
}
