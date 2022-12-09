<?php

namespace UserBundle\Manager\Notification;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationSendHistory;
use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;
use UserBundle\Entity\Notification\ThemeOption\ThemeOptionHeader;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Manager\Notification\Model\FeedData;
use UserBundle\Repository\PersonRecipientRepository;

/**
 * Class SendableNotification
 * @package UserBundle\Manager\Notification
 */
class SendableNotification
{

    const TEMPLATE = 'UserBundle:Notification:index.html.twig';

    /**
     * @var SendableNotificationConfig
     */
    private $config;

    /**
     * @var Notification
     */
    private $notification;

    /**
     * Sent data.
     *
     * @var FeedData[]
     */
    private $data;

    /**
     * @var boolean
     */
    private $success;

    /**
     * SendableNotification constructor.
     *
     * @param SendableNotificationConfig $config       A SendableNotificationConfig
     *                                                 instance.
     * @param Notification               $notification A Notification instance.
     * @param FeedData[]|array           $data         Sent data.
     * @param boolean                    $success      Flag, true if we successfully
     *                                                 get data.
     */
    public function __construct(
        SendableNotificationConfig $config,
        Notification $notification,
        array $data,
        $success = true
    ) {
        $this->config = $config;
        $this->notification = $notification;
        $this->data = $data;
        $this->success = $success;
    }

    /**
     * Send notification.
     *
     * @param MailerInterface        $mailer     A MailerInterface instance.
     * @param EngineInterface        $templating A templating EngineInterface
     *                                           instance.
     * @param EntityManagerInterface $em         A EntityManagerInterface
     *                                           instance.
     * @param integer[]|array        $schedules  Array of schedules entity ids.
     *
     * @return boolean
     */
    public function send(
        MailerInterface $mailer,
        EngineInterface $templating,
        EntityManagerInterface $em,
        array $schedules
    ) {
        // if (! $this->success) {
        //     return false;
        // }

        $body = $this->render($templating);
        if ($body === null) {
            return false;
        }

        //
        // Get recipient's emails.
        //
        $recipients = $this->notification->getRecipients()->map(function (AbstractRecipient $recipient) use ($em) {
            $emails = null;
            if ($recipient instanceof GroupRecipient) {
                /** @var PersonRecipientRepository $repository */
                $repository = $em->getRepository(PersonRecipient::class);

                $emails = $repository->getEmailsByGroup($recipient->getId());
            } elseif ($recipient instanceof PersonRecipient) {
                $emails = $recipient->getEmail();
            }

            return $emails;
        })->toArray();

//        $recipients = array_filter(\Functional\flatten($recipients));
        $recipients = array_filter(\nspl\a\flatten($recipients));

        //
        // Send notification and flush queue.
        //
        $sent = $mailer->sendNotificationEmail(
            $recipients,
            $this->notification->getSubject(),
            $body
        );
        $mailer->flushQueue();

        if ($sent) {
            //
            // We should change date of last notification sending and store it to
            // history.
            //
            /** @var Notification $notificationReference */
            $notificationReference = $em->getReference(Notification::class, $this->notification->getId());
            $schedules = $em->getRepository(AbstractNotificationSchedule::class)
                ->findBy([ 'id' => $schedules ]);
            $schedules = array_map(function (AbstractNotificationSchedule $schedule) {
                $historySchedule = clone $schedule;
                $historySchedule->setNotification(null);

                return $historySchedule;
            }, $schedules);

            $notificationReference->setLastSentAt(new \DateTime());
            $history = new NotificationSendHistory(
                $notificationReference,
                $schedules
            );

            $em->persist($notificationReference);
            $em->persist($history);
            $em->flush();

            //
            // Remove old history.
            //
            $em->createQueryBuilder()
                ->delete()
                ->from(NotificationSendHistory::class, 'History')
                ->where('History.date <= :date')
                ->setParameter('date', date_create()->modify($this->config->historyStorePeriod))
                ->getQuery()
                ->execute();
        }

        return $sent;
    }

    /**
     * Render notification template.
     *
     * @param EngineInterface $templating A templating EngineInterface instance.
     *
     * @return string|null
     */
    public function render(EngineInterface $templating)
    {
        // if (! $this->success) {
        //     return null;
        // }

        $body = null;
        if (count($this->data) > 0) {
            //
            // Render proper notification template.
            //
            $isEnhanced = $this->notification->getThemeType()->is(ThemeTypeEnum::ENHANCED);

            $themeOptions = $this->notification->getActualThemeOptions();

            //
            // Set default logo image for enhanced layout.
            //
            $header = $themeOptions->getHeader();

            if ($isEnhanced && ($header->getImageUrl() === '')) {
                $header->setImageUrl(ThemeOptionHeader::DEFAULT_IMAGE);
            } elseif (! $isEnhanced && ($header->getImageUrl() === ThemeOptionHeader::DEFAULT_IMAGE)) {
                $header->setImageUrl('');
            }

            $body = $templating->render(self::TEMPLATE, [
                'feeds' => $this->data,
                'theme' => [
                    'options' => $themeOptions->toArray(),
                    'type' => $this->notification->getThemeType()->getValue(),
                ],
            ]);
        } elseif ($this->notification->isSendWhenEmpty()) {
            //
            // Render empty notification template if notification allow empty sending.
            //
            $body = $this->config->emptyMessage;
        }

        return $body;
    }
}
