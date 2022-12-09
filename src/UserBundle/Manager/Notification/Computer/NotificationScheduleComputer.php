<?php

namespace UserBundle\Manager\Notification\Computer;

use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;

/**
 * Class NotificationScheduleComputer
 *
 * Compute notification schedule date's.
 *
 * @package UserBundle\Manager\Notification\Computer
 */
class NotificationScheduleComputer implements NotificationScheduleComputerInterface
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
     * @return array[]
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    public function compute(array $schedules, $to, \DateTimeZone $timeZone)
    {
        $start = new \DateTime('now', $timeZone);
        $to = is_string($to) ? new \DateTime($to) : $to;

        if (! $to instanceof \DateTime) {
            throw new \InvalidArgumentException('Expects date or valid \DateTime constructor parameter.');
        }

        $dates = [];
        foreach ($schedules as $schedule) {
            $tmp = $schedule->computeDates($start, $to);
            foreach ($tmp as $date) {
                $key = $date->format('Y-m-d H:i');

                if (! isset($dates[$key])) {
                    $dates[$key] = [
                        'date' => $date,
                        'ids' => [],
                    ];
                }

                $dates[$key]['ids'][] = $schedule->getId();
            }
        }

        //
        // Now we should convert date's back to current timezone in order to
        // simplify further processing.
        //
        $defaultTZ = new \DateTimeZone(date_default_timezone_get());
        return array_map(function (array $row) use ($defaultTZ) {
            $row['date']->setTimezone($defaultTZ);

            return $row;
        }, $dates);
    }
}
