<?php

namespace PayPal;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Psr\Log\LoggerInterface;

/**
 * Class ApiContextFactory
 *
 * @package PayPal
 */
class ApiContextFactory
{


    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PayPalApiContextFactory constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $clientId PayPal application client id.
     * @param string $secret   PayPal application secret.
     * @param string $mode     PayPal mode.
     *
     * @return ApiContext
     */
    public function generate($clientId, $secret, $mode)
    {
        $context = new ApiContext(new OAuthTokenCredential($clientId, $secret));

        $config = [ 'mode' => $mode ];
        if ($mode === 'sandbox') {
            MonologPayPalLogFactory::$logger = $this->logger;

            $config['log.LogEnabled'] = true;
            $config['log.FileName'] = 'PayPal.log';
            $config['log.LogLevel'] = 'DEBUG';
            $config['log.AdapterFactory'] = MonologPayPalLogFactory::class;
        }

        $context->setConfig($config);

        return $context;
    }
}
