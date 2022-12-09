<?php

namespace PaymentBundle\Controller;

use PaymentBundle\Agreement\AgreementManagerInterface;
use PaymentBundle\Entity\Payment;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Enum\PaymentStatusEnum;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use PaymentBundle\PaymentBundleServices;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class IpnController
 *
 * @package PaymentBundle\Controller
 */
class IpnController extends Controller
{

    /**
     * Handle payment gateway notification.
     *
     * @Route("/notification/{gateway}", requirements={ "gateway": "\w+" })
     *
     * @param Request $request A HTTP Request.
     * @param string  $gateway A payment gateway.
     *
     * @return Response
     */
    public function notificationAction(Request $request, $gateway)
    {
        /** @var PaymentGatewayFactoryInterface $factory */
        $factory = $this->get(PaymentBundleServices::PAYMENT_GATEWAY_FACTORY);
        /** @var AgreementManagerInterface $agreementManager */
        $agreementManager = $this->get(PaymentBundleServices::AGREEMENT_MANAGER);
        $em = $this->getDoctrine()->getManager();
        /** @var LoggerInterface $logger */
        $logger = $this->get('monolog.logger.payment_api');

        $logger->info('Got payment notification from '. $gateway .'. Content: '. $request->getContent());
        if (! PaymentGatewayEnum::isValid($gateway)) {
            $logger->error('Unknown gateway: '. $gateway);
            throw Response::create(null, 404);
        }

        $gatewayEnum = new PaymentGatewayEnum($gateway);
        $paymentGateway = $factory->getGateway($gatewayEnum);
        $notification = $paymentGateway->processNotification($request);

        $logger->info('Notification processed successfully');
        $subscription = $agreementManager->getSubscription($gatewayEnum, $notification->getAgreementId());
        if ($subscription === null) {
            //
            // Because we don't want to get invalid notification again.
            //
            $logger->error('Can\'t find proper billing subscription.');
            return new Response();
        }

        $logger->info('Store payment information');
        $payment = Payment::create()
            ->setAmount($notification->getAmount())
            ->setStatus($notification->getStatus())
            ->setSubscription($subscription)
            ->setTransactionId($notification->getTransactionId())
            ->setGateway($gatewayEnum);

        $subscription
            ->setPayed($notification->getStatus()->is(PaymentStatusEnum::success()));

        $em->persist($subscription);
        $em->persist($payment);
        $em->flush();

        return new Response();
    }
}
