<?php

namespace UserBundle\Manager\Notification\Computer;

use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;

/**
 * Interface NotificationScheduleComputerInterface
 *
 * Compute notification schedule date's.
 *
 * @package UserBundle\Manager\Notification\Computer
 */
interface NotificationScheduleComputerInterface
{

    /**
     * Compute all notification send date's from current date to specified.
     *
     * All date's is unique even if notification have some interacting scheduling.
     *
     * @param AbstractNotificationSchedule[]|array $schedules An array of
     *                                                        AbstractNotificationSchedule
     *                                                        instance's.
     * @param \DateTime|string                     $to        Computing bound. If parameter
     *                                                        is string expects format
     *                                                        accepted by 'modify' method
     *                                                        of DateTime class.
     * @param \DateTimeZone                        $timeZone  Timezone used for each
     *                                                        date.
     *
     * @return \DateTime[]
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function compute(array $schedules, $to, \DateTimeZone $timeZone);
}
