<?php

namespace UserBundle\Entity\Notification\Schedule;

use ApiBundle\Entity\NormalizableEntityInterface;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationSendHistory;

/**
 * Class AbstractNotificationSchedule
 *
 * @ORM\Table(name="notification_schedule")
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "daily"="DailyNotificationSchedule",
 *  "weekly"="WeeklyNotificationSchedule",
 *  "monthly"="MonthlyNotificationSchedule"
 * })
 */
abstract class AbstractNotificationSchedule implements
    EntityInterface,
    NormalizableEntityInterface
{

    use BaseEntityTrait;

    /**
     * @var Notification
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Notification\Notification",
     *     inversedBy="schedules"
     * )
     */
    private $notification;

    /**
     * @var NotificationSendHistory
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Notification\NotificationSendHistory",
     *     inversedBy="schedules"
     * )
     */
    private $history;

    /**
     * Set notification
     *
     * @param Notification $notification A Notification instance.
     *
     * @return static
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
     * Set history
     *
     * @param NotificationSendHistory $history A NotificationSendHistory instance.
     *
     * @return AbstractNotificationSchedule
     */
    public function setHistory(NotificationSendHistory $history = null)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history
     *
     * @return NotificationSendHistory
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Compute all date's for this schedule in specified period.
     *
     * @param \DateTime $start Start of computing period.
     * @param \DateTime $end   End of computing period.
     *
     * @return \DateTime[]
     */
    public function computeDates(\DateTime $start, \DateTime $end)
    {
        $modifiedStart = clone $start;
        $modifiedEnd = clone $end;

        $modifiedStart
            ->setTime($modifiedStart->format('H'), $modifiedStart->format('i'), 0);

        // Add 1 seconds in order to catch end date if it should exists in result's.
        $modifiedEnd
            ->setTime($modifiedEnd->format('H'), $modifiedEnd->format('i'), 1);

        return $this->doComputeDates($modifiedStart, $modifiedEnd);
    }

    /**
     * Return key identifier for current schedule.
     *
     * @return string
     */
    abstract public function getKey();

    /**
     * Compute all date's for this schedule in specified period.
     *
     * @param \DateTime $start Start of computing period.
     * @param \DateTime $end   End of computing period.
     *
     * @return \DateTime[]
     */
    abstract protected function doComputeDates(\DateTime $start, \DateTime $end);
}
