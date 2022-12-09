<?php

namespace PaymentBundle\Gateway;

use PaymentBundle\Agreement\AgreementManagerInterface;
use PaymentBundle\Entity\Model\Money;
use PaymentBundle\Enum\PaymentStatusEnum;
use PaymentBundle\Model\BillingSubscription;
use PaymentBundle\Model\PaymentNotification;
use PayPal\Rest\ApiContext;
use PayPal\Api as PayPalApi;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Plan;
use UserBundle\Entity\Subscription\AbstractSubscription;

/**
 * Class PayPalPaymentGateway
 *
 * @package PaymentBundle\Gateway
 */
class PayPalPaymentGateway implements PaymentGatewayInterface
{

    /**
     * Map between PayPal payment status and application payment status.
     *
     * @var array
     */
    private static $paymentStatusMap = [
        'Canceled_Reversal' => PaymentStatusEnum::CANCELED,
        'Completed' => PaymentStatusEnum::SUCCESS,
        'Declined' => PaymentStatusEnum::CANCELED,
        'Expired' => PaymentStatusEnum::FAILED,
        'Failed' => PaymentStatusEnum::FAILED,
        'In-Progress' => PaymentStatusEnum::PENDING,
        'Partially_Refunded' => PaymentStatusEnum::PENDING,
        'Pending' => PaymentStatusEnum::PENDING,
        'Processed' => PaymentStatusEnum::PENDING,
        'Refunded' => PaymentStatusEnum::REFUND,
        'Reversed' => PaymentStatusEnum::PENDING,
        'Voided' => PaymentStatusEnum::FAILED,
        'Max_Failed' => PaymentStatusEnum::FAILED,
    ];

    /**
     * Path to live IPN verification endpoint.
     */
    const LIFE_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr';

    /**
     * Path to sandbox IPN verification endpoint.
     */
    const SANDBOX_URL = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';

    /**
     * Response from PayPal indicating validation was successful.
     */
    const VALID = 'VERIFIED';

    /**
     * @var AgreementManagerInterface
     */
    private $agreementManager;

    /**
     * @var ApiContext
     */
    private $apiContext;

    /**
     * @var string
     */
    private $returnUrl;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var PayPalApi\Plan[]
     */
    private $availablePayPalPlans;

    /**
     * PayPalPaymentGateway constructor.
     *
     * @param AgreementManagerInterface $agreementManager A AgreementManagerInterface
     *                                                    instance.
     * @param ApiContext                $apiContext       A PayPal api context
     *                                                    instance.
     * @param string                    $returnUrl        Url on which user will
     *                                                    be redirected after
     *                                                    payment processing.
     * @param string                    $mode             PayPal mode.
     */
    public function __construct(
        AgreementManagerInterface $agreementManager,
        ApiContext $apiContext,
        $returnUrl,
        $mode
    ) {
        $this->agreementManager = $agreementManager;
        $this->apiContext = $apiContext;
        $this->returnUrl = $returnUrl;
        $this->mode = $mode;
    }

    /**
     * Update or create specific billing plan for specified application billing
     * plan.
     *
     * @param Plan $plan A application Plan entity instance.
     *
     * @return void
     */
    public function updatePlan(Plan $plan)
    {
        //
        // Get list of available plan.
        //
        $payPalPlans = $this->getAvailablePlans();
        ($payPalPlans === null) && $payPalPlans = [];

        //
        // Find PayPal plan with same name.
        //
        $payPalPlan = $this->findProperPlan($plan);

        if ($payPalPlan instanceof PayPalApi\Plan) {
            //
            // We got same plan already so we should remove it because PayPal
            // don't give to us ability to update activated plan.
            //
            $this->removePlan($plan);
        }

        if ($plan->isFree()) {
            //
            // Don't create free plan on paypal.
            //
            return;
        }

        //
        // Create new PayPal billing plan.
        //
        $amount = new PayPalApi\Currency([
            'value' => (string) $plan->getPrice(),
            'currency' => 'USD',
        ]);

        $payPalPlan = new PayPalApi\Plan();
        $payPalPlan
            ->setName($plan->getInnerName())
            ->setDescription(ucfirst($plan->getInnerName()))
            ->setType('INFINITE')
            ->setMerchantPreferences(new PayPalApi\MerchantPreferences())
            ->setPaymentDefinitions([ new PayPalApi\PaymentDefinition() ]);

        $payPalPlan->getMerchantPreferences()
            ->setSetupFee($amount)
            ->setReturnUrl($this->returnUrl .'?accept=')
            ->setCancelUrl($this->returnUrl .'?cancel=')
            ->setAutoBillAmount('YES')
            ->setInitialFailAmountAction('CONTINUE');

        // Create payment definition.
        $payPalPlan->getPaymentDefinitions()[0]
            ->setName('Regular')
            ->setType('REGULAR')
            ->setFrequency('MONTH')
            ->setFrequencyInterval('1')
            ->setAmount($amount);

        $payPalPlan = $payPalPlan->create($this->apiContext);

        //
        // By default plan is not active so we should make another request.
        // https://developer.paypal.com/docs/integration/direct/billing-plans-and-agreements/#activate-a-plan
        //
        $patch = new PayPalApi\Patch();

        $patch->setOp('replace')
            ->setPath('/')
            ->setValue([ 'state' => 'ACTIVE' ]);
        $patchRequest = new PayPalApi\PatchRequest();
        $patchRequest->addPatch($patch);

        $payPalPlan->update($patchRequest, $this->apiContext);
    }

    /**
     * Remove specified billing plan.
     *
     * @param Plan $plan A removed application billing Plan entity instance.
     *
     * @return void
     */
    public function removePlan(Plan $plan)
    {
        //
        // Get list of available plan.
        //
        $payPalPlans = $this->getAvailablePlans();
        ($payPalPlans === null) && $payPalPlans = [];

        //
        // Find PayPal plan with same name.
        //
        $payPalPlan = $this->findProperPlan($plan);

        if ($payPalPlan instanceof PayPalApi\Plan) {
            //
            // We got same plan already so we should remove it because PayPal
            // don't give to us ability to update activated plan.
            //
            $payPalPlan->delete($this->apiContext);
        }
    }

    /**
     * Execute specified subscription.
     *
     * @param BillingSubscription $subscription A Subscription instance.
     *
     * @return void
     */
    public function executeSubscription(BillingSubscription $subscription)
    {
        $plan = $subscription->getPlan();
        $subscriptionEntity = $subscription->getSubscription();
        $creditCard = $subscription->getCreditCard();

        if ($plan->isFree()) {
            $subscriptionEntity->setPayed(true);

            return;
        }

        if ($creditCard === null) {
            throw new \LogicException('Subscription credit card is null');
        }

        $address = $creditCard->getAddress();

        $fundingInstrument = new PayPalApi\FundingInstrument();
        $fundingInstrument->setCreditCard(new PayPalApi\CreditCard());
        $fundingInstrument->getCreditCard()
            ->setFirstName($creditCard->getFirstName())
            ->setLastName($creditCard->getLastName())
            ->setType(strtolower($creditCard->getSchema()))
            ->setNumber($creditCard->getNumber())
            ->setExpireMonth((string) $creditCard->getExpiresAt()->format('m'))
            ->setExpireYear((string) $creditCard->getExpiresAt()->format('Y'))
            ->setCvv2((string) $creditCard->getCvv())
            ->setBillingAddress(new PayPalApi\Address());

        $fundingInstrument->getCreditCard()->getBillingAddress()
            ->setCountryCode($address->getCountry())
            ->setCity($address->getCity())
            ->setLine1($address->getStreet())
            ->setPostalCode($address->getPostalCode());

        $payer = new PayPalApi\Payer();
        $payer
            ->setPaymentMethod('credit_card')
            ->setFundingInstruments([ $fundingInstrument ]);

        //
        // Create subscription agreement.
        //
        $agreement = new PayPalApi\Agreement();
        $agreement
            ->setName($plan->getTitle() .' subscription agreement')
            ->setDescription($plan->getTitle() .' subscription agreement')
            ->setStartDate(date_create()->modify('+ 1 month')->format('c'))
            ->setPayer($payer);

        $payPalPlan = $this->findProperPlan($plan);

        if ($payPalPlan === null) {
            throw new \RuntimeException('Can\'t find proper plan, maybe plans not synced?');
        }

        $agreement->setPlan(new PayPalApi\Plan());
        $agreement->getPlan()->setId($payPalPlan->getId());
        $agreement->create($this->apiContext);

        //
        // PayPal don't give to us any options to pass current user information
        // with subscription agreement like 'custom' field in payment, so we store
        // it in agreement manager instead.
        //
        // We should create subscription agreement here, because credit card
        // payment not redirect back to our application like paypal payment's.
        //
        $this->agreementManager->storeAgreement($subscriptionEntity, $agreement->getId());
    }

    /**
     * Process payment notification.
     *
     * @param Request $request A HTTP Request instance.
     *
     * @return PaymentNotification
     */
    public function processNotification(Request $request)
    {
        if (!$request->isMethod(Request::METHOD_POST)) {
            return PaymentNotification::createFailed(new \Exception('Wrong HTTP method.'));
        }

        $parameters = $request->request->all();
        if (($response = $this->verify($parameters)) !== null) {
            return $response;
        }

        if (isset($parameters['initial_payment_status'])) {
            //
            // This notification about executed billing plan subscription.
            //
            switch ($parameters['txn_type']) {
                case 'recurring_payment_profile_created':
                case 'recurring_payment':
                case 'cart':
                    $status = array_key_exists('initial_payment_status', $parameters)
                        ? $parameters['initial_payment_status']
                        : $parameters['payment_status'];
                    $amount = array_key_exists('amount', $parameters)
                        ? $parameters['amount']
                        : $parameters['mc_gross'];
                    $currency = array_key_exists('currency_code', $parameters)
                        ? $parameters['currency_code']
                        : $parameters['mc_currency'];
                    $transactionId = array_key_exists('initial_payment_txn_id', $parameters)
                        ? $parameters['initial_payment_txn_id']
                        : $parameters['txn_id'];

                    return new PaymentNotification(
                        new Money($amount, $currency),
                        new PaymentStatusEnum(self::$paymentStatusMap[$status]),
                        $parameters['recurring_payment_id'],
                        $transactionId
                    );
            }
        }

        //
        // Single payment.
        //
        return PaymentNotification::createFailed(new \Exception('Unknown notification.'));
    }

    /**
     * Refund specified payment.
     *
     * @param AbstractSubscription $subscription A application subscription.
     * @param string               $note         A cancel note.
     *
     * @return void
     */
    public function cancelSubscription(AbstractSubscription $subscription, $note)
    {
        $agreementId = $this->agreementManager->getAgreementId($subscription);
        if ($agreementId === '') {
            //
            // We don't have agreement so we should do nothing here.
            //
            return;
        }

        $agreement = PayPalApi\Agreement::get($agreementId, $this->apiContext);

        //
        // We should get all transaction which is maid for this subscription
        // agreement, found last completed and refund it.
        //
        $transactions = PayPalApi\Agreement::searchTransactions($agreementId, [
            'start_date' => date_create()
                ->modify('first day of previous month')
                ->format('Y-m-d'),
            'end_date' => date_create()->format('Y-m-d'),
        ], $this->apiContext)->getAgreementTransactionList();

        //
        // Get last completed payment.
        //
        $index = count($transactions) - 1;
        while (($index >= 0) && (strtolower(trim($transactions[$index]->getStatus())) !== 'completed')) {
            --$index;
        }
        $transaction = $transactions[$index];

        if ($transaction === null) {
            //
            // We don't have transaction.
            // This situation may occurs if previously we change PayPal billing
            // plan on which current subscription is subscribed. So we shouldn't
            // do anything here.
            //
            return;
        }

        if (strtolower(trim($transaction->getStatus())) === 'completed') {
            //
            // We found last completed transaction, so we should refund it.
            //
            // See https://github.com/paypal/PayPal-Python-SDK/issues/115 for
            // more details.
            //
            $sale = PayPalApi\Sale::get($transaction->getTransactionId(), $this->apiContext);
            $refundRequest = new PayPalApi\RefundRequest();

            $amount = new PayPalApi\Amount();
            $amount->setCurrency($transaction->getAmount()->getCurrency());
            $amount->setTotal($transaction->getAmount()->getValue());

            $refundRequest
                ->setAmount($amount)
                ->setDescription('You registration was rejected');
            $sale->refundSale($refundRequest, $this->apiContext);
        }

        $currency = new PayPalApi\Currency();
        $currency->setCurrency('USD');
        $currency->setValue($subscription->getPlan()->getPrice());

        $descriptor = new PayPalApi\AgreementStateDescriptor();
        $descriptor->setAmount($currency);
        $descriptor->setNote($note);

        $agreement->cancel($descriptor, $this->apiContext);
    }

    /**
     * @param Plan $plan A application billing Plan entity instance.
     *
     * @return null|PayPalApi\Plan
     */
    private function findProperPlan(Plan $plan)
    {
        $availablePlans = $this->getAvailablePlans();

        $payPalPlan = null;
        foreach ($availablePlans as $item) {
            if ($item->getName() === $plan->getInnerName()) {
                $payPalPlan = $item;
                break;
            }
        }

        return $payPalPlan;
    }

    /**
     * @return PayPalApi\Plan[]
     */
    private function getAvailablePlans()
    {
        if ($this->availablePayPalPlans === null) {
            $this->availablePayPalPlans = PayPalApi\Plan::all([
                'page_size' => 10, // We assume that we don't get more than 10 billing
                // plans
                'status' => 'ACTIVE',
            ], $this->apiContext)->getPlans();

            if ($this->availablePayPalPlans === null) {
                $this->availablePayPalPlans = [];
            }
        }

        return $this->availablePayPalPlans;
    }

    /**
     * @param array $parameters Array of PayPal notification parameters.
     *
     * @return PaymentNotification|null
     */
    private function verify(array $parameters)
    {
        $parameters['cmd'] = '_notify-validate';

        $curlHandler = curl_init($this->mode === 'sandbox' ? self::SANDBOX_URL : self::LIFE_URL);
        curl_setopt_array($curlHandler, [
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_SSLVERSION => 6,
            CURLOPT_SSL_VERIFYPEER => 1,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['Connection: Close'],
        ]);

        $verificationResult = curl_exec($curlHandler);
        $errorCode = curl_errno($curlHandler);

        if ($errorCode !== 0) {
            $errorMsg = curl_error($curlHandler);
            curl_close($curlHandler);

            return PaymentNotification::createFailed(new \Exception("cURL error: [{$errorCode}] {$errorMsg}"));
        }
        $httpCode = curl_getinfo($curlHandler)['http_code'];
        curl_close($curlHandler);

        if ($httpCode !== 200) {
            return PaymentNotification::createFailed(new \Exception("PayPal responded with http code $httpCode"));
        }

        if ($verificationResult !== self::VALID) {
            return PaymentNotification::createFailed(new \Exception('Notification not valid'));
        }

        return null;
    }
}
