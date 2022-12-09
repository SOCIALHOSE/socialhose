<?php

namespace UserBundle\Entity\Notification\Schedule;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WeeklyNotificationSchedule
 *
 * @ORM\Entity
 */
class WeeklyNotificationSchedule extends AbstractNotificationSchedule
{

    const PERIOD_EVERY = 'every';
    const PERIOD_FIRST = 'first';
    const PERIOD_SECOND = 'second';
    const PERIOD_THIRD = 'third';
    const PERIOD_FOURTH = 'fourth';
    const PERIOD_LAST = 'last';

    const DAY_MONDAY = 'monday';
    const DAY_TUESDAY = 'tuesday';
    const DAY_WEDNESDAY = 'wednesday';
    const DAY_THURSDAY = 'thursday';
    const DAY_FRIDAY = 'friday';
    const DAY_SATURDAY = 'saturday';
    const DAY_SUNDAY = 'sunday';

    /**
     * @var string
     *
     * @ORM\Column(length=7)
     * @Assert\Choice(callback="getAvailablePeriod")
     */
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(length=10)
     * @Assert\Choice(callback="getAvailableDay")
     */
    private $day;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $hour;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $minute;

    /**
     * Map between day and corntab schedule. Make sense only for PERIOD_EVERY.
     *
     * @var array[]
     */
    private static $dayMap = [
        self::DAY_MONDAY => 1,
        self::DAY_TUESDAY => 2,
        self::DAY_WEDNESDAY => 3,
        self::DAY_TUESDAY => 4,
        self::DAY_FRIDAY => 5,
        self::DAY_SATURDAY => 6,
        self::DAY_SUNDAY => 7,
    ];

    /**
     * Get available period's.
     *
     * @return string[]
     */
    public static function getAvailablePeriod()
    {
        return [
            self::PERIOD_EVERY,
            self::PERIOD_FIRST,
            self::PERIOD_SECOND,
            self::PERIOD_THIRD,
            self::PERIOD_FOURTH,
            self::PERIOD_LAST,
        ];
    }

    /**
     * Get available day's.
     *
     * @return string[]
     */
    public static function getAvailableDay()
    {
        return [
            self::DAY_MONDAY,
            self::DAY_TUESDAY,
            self::DAY_WEDNESDAY,
            self::DAY_TUESDAY,
            self::DAY_FRIDAY,
            self::DAY_SATURDAY,
            self::DAY_SUNDAY,
        ];
    }

    /**
     * Set period
     *
     * @param string $period One of PERIOD_ const's.
     *
     * @return WeeklyNotificationSchedule
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set day
     *
     * @param string $day One of DAY_ const's.
     *
     * @return WeeklyNotificationSchedule
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set hour
     *
     * @param integer $hour Schedule hours.
     *
     * @return WeeklyNotificationSchedule
     */
    public function setHour($hour)
    {
        $this->hour = $hour;

        return $this;
    }

    /**
     * Get hour
     *
     * @return integer
     */
    public function getHour()
    {
        return $this->hour;
    }

    /**
     * Set minute
     *
     * @param integer $minute Schedule minute.
     *
     * @return WeeklyNotificationSchedule
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;

        return $this;
    }

    /**
     * Get minute
     *
     * @return integer
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'weekly';
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createString('period', [ 'schedule' ]),
            PropertyMetadata::createString('day', [ 'schedule' ]),
            PropertyMetadata::createString('hour', [ 'schedule' ]),
            PropertyMetadata::createString('minute', [ 'schedule' ]),
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
     * @param array $data Normalized WeeklyNotificationSchedule.
     *
     * @return WeeklyNotificationSchedule
     */
    public static function denormalize(array $data)
    {
        if (! isset($data['period'], $data['day'], $data['hour'], $data['minute'])) {
            throw new \LogicException('Normalized WeeklyNotificationSchedule data must have \'period\', \'day\', \'hour\' and \'minute\' fields.');
        }

        return WeeklyNotificationSchedule::create()
            ->setPeriod($data['period'])
            ->setDay($data['day'])
            ->setHour($data['hour'])
            ->setMinute($data['minute']);
    }

    /**
     * Return key identifier for current schedule.
     *
     * @return string
     */
    public function getKey()
    {
        return sprintf(
            'weekly_%s_%s_%s_%s',
            $this->period,
            $this->day,
            $this->hour,
            $this->minute
        );
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
        $dates = [];
        $date = clone $start;

        if ($this->period === self::PERIOD_EVERY) {
            //
            // Process every weekdays days.
            //

            if ($date->format('N') !== self::$dayMap[$this->day]) {
                //
                // If start date is not required weekday we should proceed to next
                // required weekday.
                //
                $date->modify('next '. $this->day);
            }
            while ($date <= $end) {
                $tmp = clone $date;
                $dates[] = $tmp->setTime($this->hour, $this->minute);
                $date->modify('next '. $this->day);
            }
        } else {
            //
            // Process specified weekday like second monday, third friday and etc.
            //

            $date->modify(sprintf(
                '%s %s of this month',
                $this->period,
                $this->day
            ));
            while ($date <= $end) {
                $tmp = clone $date;
                $dates[] = $tmp->setTime($this->hour, $this->minute);
                $date->modify(sprintf(
                    '%s %s of next month',
                    $this->period,
                    $this->day
                ));
            }
        }

        return $dates;
    }
}
