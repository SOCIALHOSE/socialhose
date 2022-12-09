<?php

namespace PaymentBundle\Gateway;

use PaymentBundle\Agreement\AgreementManagerInterface;
use PayPal\Rest\ApiContext;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PayPalPaymentGatewayFactory
 *
 * @package PaymentBundle\Gateway
 */
class PayPalPaymentGatewayFactory
{

    /**
     * @var AgreementManagerInterface
     */
    private $agreementManager;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $returnRoute;

    /**
     * @var string
     */
    private $mode;

    /**
     * PayPalPaymentGatewayFactory constructor.
     *
     * @param AgreementManagerInterface $agreementManager A AgreementManagerInterface
     *                                                    instance.
     * @param UrlGeneratorInterface     $urlGenerator     A UrlGeneratorInterface
     *                                                    instance.
     * @param string                    $returnRoute      A route on which we
     *                                                    should return after
     *                                                    successful plan subscription.
     * @param string                    $mode             PayPal mode.
     */
    public function __construct(
        AgreementManagerInterface $agreementManager,
        UrlGeneratorInterface $urlGenerator,
        $returnRoute,
        $mode
    ) {
        $this->agreementManager = $agreementManager;
        $this->urlGenerator = $urlGenerator;
        $this->returnRoute = $returnRoute;
        $this->mode = $mode;
    }

    /**
     * @param ApiContext $apiContext A ApiContext instance.
     *
     * @return PayPalPaymentGateway
     */
    public function createPayPalGateway(ApiContext $apiContext)
    {
        return new PayPalPaymentGateway(
            $this->agreementManager,
            $apiContext,
            $this->urlGenerator->generate(
                $this->returnRoute,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            $this->mode
        );
    }
}
