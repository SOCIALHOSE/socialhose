<?php

namespace UserBundle;

/**
 * Class UserBundleServices
 * @package UserBundle
 */
class UserBundleServices
{

    /**
     * Send emails to user.
     *
     * Implements {@see \UserBundle\Mailer\MailerInterface}
     * interface.
     */
    const MAILER = 'user.mailer';

    /**
     * Notification manager.
     *
     * Implements {@see \UserBundle\Manager\Notification\NotificationManagerInterface}
     * interface.
     */
    const NOTIFICATION_MANAGER = 'user.notification_manager';

    /**
     * Restriction repository.
     *
     * Implements {@see \UserBundle\Restriction\Repository\RestrictionsRepositoryInterface}
     * interface.
     */
    const RESTRICTION_REPOSITORY = 'user.restriction_repository';
}
