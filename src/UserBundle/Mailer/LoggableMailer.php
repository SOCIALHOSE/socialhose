<?php

namespace UserBundle\Mailer;

use AppBundle\Entity\EmailedDocument;
use Psr\Log\LoggerInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;

/**
 * Class LoggableMailer
 *
 * @package UserBundle\Mailer
 */
class LoggableMailer implements MailerInterface
{

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LoggableMailer constructor.
     *
     * @param MailerInterface $mailer A MailerInterface instance.
     * @param LoggerInterface $logger A LoggerInterface instance.
     */
    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * Send generated password to user.
     *
     * @param User   $user     A User entity instance.
     * @param string $password Generated password.
     *
     * @return boolean
     */
    public function sendPassword(User $user, $password)
    {
        $this->logger->info('Send password to '. $user->getId());

        return $this->mailer->sendPassword($user, $password);
    }

    /**
     * Send password resetting confirmation email.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function sendPasswordResettingConfirmation(User $user)
    {
        $this->logger->info('Send password resetting confirmation to '. $user->getId());

        return $this->mailer->sendPasswordResettingConfirmation($user);
    }

    /**
     * Send notification email to specified addresses.
     *
     * @param array  $addresses Array of recipient emails.
     * @param string $subject   Notification subject.
     * @param string $body      Notification body.
     *
     * @return boolean
     */
    public function sendNotificationEmail(array $addresses, $subject, $body)
    {
        $this->logger->info(
            'Send notification to '. implode(', ', $addresses) .' recipients'
        );

        return $this->mailer->sendNotificationEmail($addresses, $subject, $body);
    }

    /**
     * Send emailed document to recipients.
     *
     * @param EmailedDocument $emailedDocument A EmailedDocument instance.
     *
     * @return boolean
     */
    public function sendEmailedDocument(EmailedDocument $emailedDocument)
    {
        $this->logger->info('Send emailed document to '. implode(', ', $emailedDocument->getEmailTo()));

        return $this->mailer->sendEmailedDocument($emailedDocument);
    }

    /**
     * Send generated password and confirm url to user.
     *
     * @param User   $user       A User entity instance.
     * @param string $confirmUrl Generated confirm url.
     *
     * @return boolean
     */
    public function sendVerificationSuccess(User $user, $confirmUrl)
    {
        $this->logger->info('Send verification success email to '. $user->getId());

        return $this->mailer->sendVerificationSuccess($user, $confirmUrl);
    }

    /**
     * Send email about failed verification.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function sendVerificationRejected(User $user)
    {
        $this->logger->info('Send verification success email to '. $user->getId());

        return $this->mailer->sendVerificationRejected($user);
    }

    /**
     * Send unsubscribe notification.
     *
     * @param Notification $notification A Notification entity.
     * @param User         $user         A User entity who unsubscribe from
     *                                   specified notification.
     *
     * @return boolean
     */
    public function sendUnsubscribe(Notification $notification, User $user)
    {
        $this->logger->info('Send unsubscribe notification to '. $notification->getOwner()->getFullName());

        return $this->mailer->sendUnsubscribe($notification, $user);
    }

    /**
     * Send mail messages.
     *
     * Should be called only in command.
     *
     * @return void
     */
    public function flushQueue()
    {
        $this->logger->info('Spool messages');

        $this->mailer->flushQueue();
    }
}
