<?php

namespace UserBundle\Entity\Notification\Schedule;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MonthlyNotificationSchedule
 *
 * @ORM\Entity
 */
class MonthlyNotificationSchedule extends AbstractNotificationSchedule
{

    const DAY_LAST = 'last';

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
     * Get available day's.
     *
     * @return string[]
     */
    public static function getAvailableDay()
    {
        $days = range(1, 31);
        $days[] = self::DAY_LAST;

        return $days;
    }

    /**
     * Set day
     *
     * @param string $day One of DAY_ const's.
     *
     * @return MonthlyNotificationSchedule
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
     * @return MonthlyNotificationSchedule
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
     * Set minutes
     *
     * @param integer $minute Schedule minutes.
     *
     * @return MonthlyNotificationSchedule
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
        return 'monthly';
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
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
     * @param array $data Normalized MonthlyNotificationSchedule.
     *
     * @return MonthlyNotificationSchedule
     */
    public static function denormalize(array $data)
    {
        if (! isset($data['day'], $data['hour'], $data['minute'])) {
            throw new \LogicException('Normalized MonthlyNotificationSchedule data must have \'day\', \'hour\' and \'minute\' fields.');
        }

        return MonthlyNotificationSchedule::create()
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
            'monthly_%s_%s_%s',
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

        if ($this->day === self::DAY_LAST) {
            $date->modify('last day of this month');
            while ($date <= $end) {
                $tmp = clone $date;
                $dates[] = $tmp->setTime($this->hour, $this->minute);
                $date->modify('last day of next month');
            }
        } else {
            $currentNum = $date->format('j');

            if ($currentNum > $this->day) {
                $date
                    ->modify('first day of next month')
                    ->modify(sprintf('%d day', $this->day - 1));
            } elseif ($currentNum < $this->day) {
                $date->modify(sprintf('%d day', $this->day - $currentNum));
            }

            while ($date <= $end) {
                $tmp = clone $date;
                $dates[] = $tmp->setTime($this->hour, $this->minute);
                $date
                    ->modify('first day of next month')
                    ->modify(sprintf('%d day', $this->day - 1));
            }
        }

        return $dates;
    }
}
