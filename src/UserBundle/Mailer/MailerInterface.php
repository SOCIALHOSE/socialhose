<?php

namespace UserBundle\Mailer;

use AppBundle\Entity\EmailedDocument;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;

/**
 * Interface MailerInterface
 * Send emails to user.
 *
 * @package UserBundle\Mailer
 */
interface MailerInterface
{

    /**
     * Send generated password to user.
     *
     * @param User   $user     A User entity instance.
     * @param string $password Generated password.
     *
     * @return boolean
     */
    public function sendPassword(User $user, $password);

    /**
     * Send email about success verification.
     *
     * @param User   $user       A User entity instance.
     * @param string $confirmUrl Generated confirm url.
     *
     * @return boolean
     */
    public function sendVerificationSuccess(User $user, $confirmUrl);

    /**
     * Send email about failed verification.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function sendVerificationRejected(User $user);

    /**
     * Send password resetting confirmation email.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function sendPasswordResettingConfirmation(User $user);

    /**
     * Send notification email to specified addresses.
     *
     * @param array  $addresses Array of recipient emails.
     * @param string $subject   Notification subject.
     * @param string $body      Notification body.
     *
     * @return boolean
     */
    public function sendNotificationEmail(array $addresses, $subject, $body);

    /**
     * Send emailed document to recipients.
     *
     * @param EmailedDocument $emailedDocument A EmailedDocument instance.
     *
     * @return boolean
     */
    public function sendEmailedDocument(EmailedDocument $emailedDocument);

    /**
     * Send unsubscribe notification.
     *
     * @param Notification $notification A Notification entity.
     * @param User         $user         A User entity who unsubscribe from
     *                                   specified notification.
     *
     * @return boolean
     */
    public function sendUnsubscribe(Notification $notification, User $user);

    /**
     * Send mail messages.
     *
     * Should be called only in command.
     *
     * @return void
     */
    public function flushQueue();
}
