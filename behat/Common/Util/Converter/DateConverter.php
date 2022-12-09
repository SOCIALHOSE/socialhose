<?php

namespace Common\Util\Converter;

/**
 * Class DateConverter
 * @package Common\Util\Converter
 */
class DateConverter
{

    /**
     * Map between date format and proper regular patterns.
     *
     * @var string[]
     */
    private static $datePatternsMap = [
        'Y-m-d' => '\d{4}-\d{2}-\d{2}',
        'Y-m-d H:i:s' => '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}',
        'Y-m-d\TH:i:sP' => '\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(:?(:?\+|\-)\d{2}:\d{2}|\w)',
    ];

    /**
     * Cache of formats for specified dates.
     *
     * @var string
     */
    private static $dateFormatCache = [];

    /**
     * Check that specified string can be converted to \DateTime instance.
     *
     * @param string $date Date string.
     *
     * @return boolean
     */
    public static function can($date)
    {
        return self::getFormat($date) !== null;
    }

    /**
     * @param string $date Date string.
     *
     * @return \DateTime|false
     */
    public static function convert($date)
    {
        $format = self::getFormat($date);

        if ($format === null) {
            throw new \InvalidArgumentException('Invalid date '. $date);
        }

        return date_create_from_format($format, $date);
    }

    /**
     * Get proper date format for specified date string.
     *
     * @param string $date Date string.
     *
     * @return string|null
     */
    private static function getFormat($date)
    {
        if (! is_string($date)) {
            return false;
        }

        if (! isset(self::$dateFormatCache[$date])) {
            self::$dateFormatCache[$date] = null;

            // Check specified date against all available patterns and find
            // proper format.
            foreach (self::$datePatternsMap as $format => $pattern) {
                if ((preg_match('/^' . $pattern . '$/', $date) === 1)
                    && (date_create_from_format($format, $date) !== false)) {
                        self::$dateFormatCache[$date] = $format;
                        break;
                }
            }
        }

        return self::$dateFormatCache[$date];
    }
}
