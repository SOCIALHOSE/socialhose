<?php

namespace UserBundle\Mailer;

use AppBundle\Configuration\ConfigurationInterface;
use AppBundle\Configuration\ParametersName;
use AppBundle\Entity\EmailedDocument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class Mailer
 * Default implementation of MailerInterface.
 *
 * @package UserBundle\Mailer
 */
class Mailer implements MailerInterface
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Swift_Transport
     */
    private $transport;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param \Swift_Mailer          $mailer        A Swift_Mailer instance.
     * @param \Swift_Transport       $transport     A Swift_Transport instance.
     * @param \Twig_Environment      $twig          A \Twig_Environment instance.
     * @param ConfigurationInterface $configuration A ConfigurationInterface
     *                                              instance.
     * @param UrlGeneratorInterface  $urlGenerator  A UrlGeneratorInterface
     *                                              instance.
     */
    public function __construct(
        \Swift_Mailer $mailer,
        \Swift_Transport $transport,
        \Twig_Environment $twig,
        ConfigurationInterface $configuration,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->mailer = $mailer;
        $this->transport = $transport;
        $this->twig = $twig;
        $this->configuration = $configuration;
        $this->urlGenerator = $urlGenerator;
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
        return $this->sendEmail(
            $user->getEmail(),
            'Password is changed',
            ParametersName::MAIL_PASSWORD,
            [
                'user' => $user,
                'password' => $password,
            ]
        );
    }

    /**
     * Send generated password and confirm url to user.
     *
     * @param User   $user     A User entity instance.
     * @param string $password A user plain password.
     *
     * @return boolean
     */
    public function sendVerificationSuccess(User $user, $password)
    {
        return $this->sendEmail(
            $user->getEmail(),
            'Verification status',
            ParametersName::MAIL_VERIFICATION_SUCCESS,
            [
                'user' => $user,
                'password' => $password,
            ]
        );
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
        return $this->sendEmail(
            $user->getEmail(),
            'Verification status',
            ParametersName::MAIL_VERIFICATION_REJECT,
            [
                'user' => $user,
            ]
        );
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
        return $this->sendEmail(
            $user->getEmail(),
            'Password resetting',
            ParametersName::MAIL_RESETTING_CONFIRMATION,
            [
                'user' => $user,
                'confirmationUrl' => $this->urlGenerator->generate('app_index_index', [
                    'part' => 'auth/reset-password',
                    'resetting_token' => $user->getConfirmationToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        );
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
        $from = $this->configuration->getParameter(ParametersName::MAILER_ADDRESS);
        $fromName = $this->configuration->getParameter(ParametersName::MAILER_SENDER_NAME);

        $message = \Swift_Message::newInstance()
            ->setTo($addresses)
            ->setFrom($from, $fromName)
            ->setSubject($subject)
            ->setBody($body, 'text/html');

        return $this->send($message) > 0;
    }



    /**
     * @param string       $renderedTemplate
     * @param array|string $fromEmail
     * @param array|string $toEmail
     */
    public function sendEmailMessage(UserInterface $user, $baseurl)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $parameters = array(
            'user' => $user,
            'confirmationUrl' => $baseurl.'/auth/confirm-account/'.$user->getConfirmationToken(),
        );
        $toEmail = (string) $user->getEmail();
        $template = $this->twig->load('@FOSUser/Registration/email.txt.twig');

        $message = (new \Swift_Message())
            ->setSubject('Verify your email address')
            ->setFrom("support@socialhose.io","SOCIALHOSE.IO")
            ->setTo($toEmail)
            ->setBody($template->render(
                $parameters
            ));

        $this->send($message);
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
        $subject = $emailedDocument->getSubject() === ''
            ? 'Emailed document content'
            : $emailedDocument->getSubject();

        $from = $this->configuration->getParameter(ParametersName::MAILER_ADDRESS);
        $fromName = $this->configuration->getParameter(ParametersName::MAILER_SENDER_NAME);

        $message = \Swift_Message::newInstance()
            ->setTo($emailedDocument->getEmailTo())
            ->setFrom($from, $fromName)
            ->setReplyTo($emailedDocument->getEmailReplyTo())
            ->setSubject($subject)
            ->setBody($emailedDocument->getContent(), 'text/html');

        return $this->send($message) > 0;
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
        return $this->sendEmail(
            $user->getEmail(),
            sprintf(
                'User %s unsubscribed from %s',
                $user->getFullName(),
                $notification->getName()
            ),
            ParametersName::MAIL_UNSUBSCRIBE,
            [
                'user' => $user,
                'notification' => $notification,
            ]
        );
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
        $transport = $this->mailer->getTransport();
        if ($transport instanceof  \Swift_Transport_SpoolTransport) {
            $spool = $transport->getSpool();
            if ($spool instanceof \Swift_MemorySpool) {
                $spool->flushQueue($this->transport);
            }
        }
    }

    /**
     * @param string $recipient         Recipient email address.
     * @param string $subject           Email subject.
     * @param string $bodyParameterName Name of configuration parameter which is
     *                                  holds body text.
     * @param array  $parameters        Template parameters.
     *
     * @return boolean
     */
    private function sendEmail($recipient, $subject, $bodyParameterName, array $parameters = [])
    {
        $from = $this->configuration->getParameter(ParametersName::MAILER_ADDRESS);
        $fromName = $this->configuration->getParameter(ParametersName::MAILER_SENDER_NAME);

        $message = \Swift_Message::newInstance($subject, $this->twig->render(
            'UserBundle::email_layout.html.twig',
            [
                'body' => $this->twig->createTemplate(
                    $this->configuration->getParameter($bodyParameterName)
                )->render($parameters),
            ]
        ), 'text/html')
            ->setTo($recipient)
            ->setFrom($from, $fromName);

        return $this->send($message) > 0;
    }

    /**
     * @param \Swift_Message $message
     * @return int
     */
    private function send($message)
    {
        $this->resetTransport();

        return $this->mailer->send($message);
    }

    /**
     * Reset SMTP connection for each send so that connections
     * are not dropped during long-running processes.
     *
     * See https://github.com/swiftmailer/swiftmailer/issues/490#issuecomment-72492442
     *
     * @return void
     */
    private function resetTransport()
    {
        try {
            if ($this->transport instanceof \Swift_Transport_AbstractSmtpTransport) {
                $this->transport->reset();
            }
        } catch (\Exception $e) {
            try {
                $this->transport->stop();
            } catch (\Exception $e) {
                // pass
            }
            // $this->transport->start();
        }
    }
}
