<?php

namespace PayPal;

use PayPal\Log\PayPalLogFactory;
use Psr\Log\LoggerInterface;

/**
 * Class MonologPayPalLogFactory
 *
 * Hack used to inject monolog logger into PayPal logger manager.
 *
 * @package PayPal
 */
class MonologPayPalLogFactory implements PayPalLogFactory
{

    /**
     * @var LoggerInterface
     */
    public static $logger;

    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className A Logger class name.
     *
     * @return LoggerInterface Instance of logger object implementing
     *                         LoggerInterface.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    // @codingStandardsIgnoreStart
    public function getLogger($className)
    {
        // @codingStandardsIgnoreEnd
        return self::$logger;
    }
}
