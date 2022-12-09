<?php

namespace UserBundle\Entity\Notification;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;

/**
 * Class NotificationSendHistory
 *
 * @ORM\Table(name="notifications_history")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\NotificationSendHistoryRepository")
 */
class NotificationSendHistory implements EntityInterface, NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var Notification
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Notification\Notification",
     *     inversedBy="history"
     * )
     */
    private $notification;

    /**
     * Schedules which trigger notification sending.
     *
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule",
     *     mappedBy="history",
     *     cascade={ "persist", "remove" }
     * )
     */
    private $schedules;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * NotificationSendHistory constructor.
     *
     * @param Notification                         $notification A Notification entity
     *                                                           instance.
     * @param AbstractNotificationSchedule[]|array $schedule     Array of schedules which
     *                                                           trigger notification
     *                                                           sending.
     */
    public function __construct(Notification $notification, array $schedule)
    {
        $this->notification = $notification;

        foreach ($schedule as $item) {
            $this->addSchedule($item);
        }
        $this->date = $notification->getLastSentAt();
    }

    /**
     * Set notification
     *
     * @param Notification $notification A Notification entity instance.
     *
     * @return NotificationSendHistory
     */
    public function setNotification(Notification $notification = null)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Get notification
     *
     * @return Notification
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Add schedule
     *
     * @param AbstractNotificationSchedule $schedule A AbstractNotificationSchedule
     *                                               instance.
     *
     * @return NotificationSendHistory
     */
    public function addSchedule(AbstractNotificationSchedule $schedule)
    {
        $this->schedules[] = $schedule;
        $schedule->setHistory($this);

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param AbstractNotificationSchedule $schedule A AbstractNotificationSchedule
     *                                               instance.
     *
     * @return  NotificationSendHistory
     */
    public function removeSchedule(AbstractNotificationSchedule $schedule)
    {
        $this->schedules->removeElement($schedule);
        $schedule->setHistory(null);

        return $this;
    }

    /**
     * Get schedules
     *
     * @return Collection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Set date
     *
     * @param \DateTime $date Sent date.
     *
     * @return NotificationSendHistory
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('notification', [ 'history' ])
                ->setField(function () {
                    return $this->notification->getId();
                }),
            PropertyMetadata::createString('name', [ 'history' ])
                ->setField(function () {
                    return $this->notification->getName();
                }),
            PropertyMetadata::createString('type', [ 'history' ])
                ->setField(function () {
                    return $this->notification->getNotificationType()->getValue();
                }),
            PropertyMetadata::createCollection('schedule', AbstractNotificationSchedule::class, [ 'history' ])
                ->setField('schedules'),
            PropertyMetadata::createDate('date', [ 'history' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'history' ];
    }
}
