<?php

namespace QueueBundle\Consumer;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Manager\Notification\NotificationManagerInterface;
use UserBundle\Repository\NotificationRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NotificationsSenderConsumer
 *
 * @package QueueBundle\Consumer
 */
class NotificationsSenderConsumer extends AbstractConsumer
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var NotificationManagerInterface
     */
    private $manager;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * NotificationSenderConsumer constructor.
     *
     * @param LoggerInterface              $logger     A LoggerInterface instance.
     * @param EntityManagerInterface       $em         A EntityManagerInterface
     *                                                 instance.
     * @param NotificationManagerInterface $manager    A NotificationManagerInterface
     *                                                 instance.
     * @param MailerInterface              $mailer     A MailerInterface instance.
     * @param EngineInterface              $templating A templating EngineInterface
     *                                                 instance.
     */
    public function __construct(
        LoggerInterface $logger,
        EntityManagerInterface $em,
        NotificationManagerInterface $manager,
        MailerInterface $mailer,
        EngineInterface $templating,
        ContainerInterface $container
    ) {
        parent::__construct($logger, $em->getConnection());

        $this->em = $em;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->container = $container;
    }

    /**
     * Execute consumer specific code.
     *
     * @param string $messageBody Sanitized message body.
     *
     * @return mixed
     */
    protected function doExecute($messageBody)
    {
        $row = unserialize($messageBody);

        if (! is_array($row) || ! isset($row['notification_id'], $row['schedules'])) {
            $this->error('Got invalid message, drop it', [ 'message' => $messageBody ]);

            return true; // We return true in order to not requeue this invalid
                         // message again.
        }
        $id = $row['notification_id'];
        $schedules = $row['schedules'];
        $schedules = explode(',', $schedules);

        if (($schedules === false) || (count($schedules) === 0)) {
            $this->error('Got invalid message, drop it', [ 'message' => $messageBody ]);

            return true; // We return true in order to not requeue this invalid
            // message again.
        }

        $this->info('Send notification', [ 'message' => $messageBody, 'id' => $id ]);

        /** @var NotificationRepository $repository */
        $repository = $this->em->getRepository(Notification::class);
        $notification = $repository->getForSending($id);

        if ($notification instanceof Notification) {
            $sendableNotification = $this->manager->prepareToSend($notification);
            $sendableNotification->send(
                $this->mailer,
                $this->templating,
                $this->em,
                $schedules
            );
        }

        return true;
    }
}
