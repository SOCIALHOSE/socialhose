<?php

namespace UserBundle\Entity\Notification\Schedule;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DailyNotificationSchedule
 *
 * @ORM\Entity
 */
class DailyNotificationSchedule extends AbstractNotificationSchedule
{

    const TIME_15_M = '15m';
    const TIME_30_M = '30m';
    const TIME_1_H = '1h';
    const TIME_2_H = '2h';
    const TIME_3_H = '3h';
    const TIME_4_H = '4h';
    const TIME_6_H = '6h';
    const TIME_12_H = '12h';
    const TIME_ONCE = 'once';

    const DAYS_ALL = 'all';
    const DAYS_WEEKDAYS = 'weekdays';
    const DAYS_WEEKENDS = 'weekends';

    /**
     * @var string
     *
     * @ORM\Column(length=5)
     * @Assert\Choice(callback="getAvailableTime")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(length=9)
     * @Assert\Choice(callback="getAvailableDays")
     */
    private $days;

    /**
     * Map between available time values and crontab schedule string.
     *
     * @var string[]
     */
    private static $timeMap = [
        self::TIME_15_M => 'T15M',
        self::TIME_30_M => 'T30M',
        self::TIME_1_H => 'T1H',
        self::TIME_2_H => 'T2H',
        self::TIME_3_H => 'T3H',
        self::TIME_4_H => 'T4H',
        self::TIME_6_H => 'T6H',
        self::TIME_12_H => 'T12H',
        self::TIME_ONCE => '1D',
    ];


    /**
     * Get available time values.
     *
     * @return string[]
     */
    public static function getAvailableTime()
    {
        return [
            self::TIME_15_M,
            self::TIME_30_M,
            self::TIME_1_H,
            self::TIME_2_H,
            self::TIME_3_H,
            self::TIME_4_H,
            self::TIME_6_H,
            self::TIME_12_H,
            self::TIME_ONCE,
        ];
    }

    /**
     * Get available days values.
     *
     * @return string[]
     */
    public static function getAvailableDays()
    {
        return [
            self::DAYS_ALL,
            self::DAYS_WEEKDAYS,
            self::DAYS_WEEKENDS,
        ];
    }

    /**
     * Set time
     *
     * @param string $time One of TIME_ constant's.
     *
     * @return DailyNotificationSchedule
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set days
     *
     * @param string $days One of DAYS_ constant's.
     *
     * @return DailyNotificationSchedule
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return string
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'daily';
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createString('time', [ 'schedule' ]),
            PropertyMetadata::createString('days', [ 'schedule' ]),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'schedule' ];
    }

    /**
     * @param array $data Normalized DailyNotificationSchedule.
     *
     * @return DailyNotificationSchedule
     */
    public static function denormalize(array $data)
    {
        if (! isset($data['time'], $data['days'])) {
            throw new \LogicException('Normalized DailyNotificationSchedule data must have \'time\' and \'days\' fields.');
        }

        return DailyNotificationSchedule::create()
            ->setTime($data['time'])
            ->setDays($data['days']);
    }

    /**
     * Return key identifier for current schedule.
     *
     * @return string
     */
    public function getKey()
    {
        return sprintf('daily_%s_%s', $this->time, $this->days);
    }

    /**
     * Compute all date's for this schedule in specified period.
     *
     * @param \DateTime $start Start of computing period.
     * @param \DateTime $end   End of computing period.
     *
     * @return \DateTime[]
     */
    protected function doComputeDates(\DateTime $start, \DateTime $end)
    {
        $period = new \DatePeriod(
            $start,
            new \DateInterval('P'. self::$timeMap[$this->time]),
            $end,
            \DatePeriod::EXCLUDE_START_DATE
        );

        $results = [];
        switch ($this->days) {
            case self::DAYS_ALL:
                $results = iterator_to_array($period);
                break;

            case self::DAYS_WEEKDAYS:
                /** @var \DateTime $date */
                foreach ($period as $date) {
                    if ($date->format('N') <= 5) {
                        $results[] = $date;
                    }
                }
                break;

            case self::DAYS_WEEKENDS:
                /** @var \DateTime $date */
                foreach ($period as $date) {
                    if ($date->format('N') > 5) {
                        $results[] = $date;
                    }
                }
                break;
        }

        return $results;
    }
}
