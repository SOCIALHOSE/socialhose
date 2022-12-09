<?php

namespace PaymentBundle\Command;

use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BillingPlanCreateCommand
 * @package PaymentBundle\Command
 */
class BillingPlanCreateCommand extends Command
{

    const NAME = 'socialhose:billing:create-plans';

    /** @var ApiContext */
    private $apiContext;

    /**
     * BillingPlanCreateCommand constructor.
     * @param ApiContext $apiContext
     */
    public function __construct(ApiContext $apiContext)
    {
        parent::__construct(self::NAME);
        $this->apiContext = $apiContext;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer null or 0 if everything went fine, or an error code.
     *
     * @see setCode()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @throws \Exception Got any exception while update plans.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = [
            [
                'name' => 'starter',
                'description' => 'Starter Plan',
                'amount' => 59
            ],
            [
                'name' => 'premium',
                'description' => 'Premium Plan',
                'amount' => 149
            ],
        ];
        foreach ($options as $option) {
            $this->createPlan($option);
        }
        return 0;
    }

    private function createPlan(array $options)
    {
        // Create a new billing plan
        $plan = new Plan();
        $plan->setName($options['name'])
            ->setDescription($options['description'])
            ->setType('INFINITE');

        // Set billing plan definitions
        $paymentDefinition = new PaymentDefinition();
        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval('1')
            ->setAmount(new Currency(array('value' => $options['amount'], 'currency' => 'USD')));

        // Set charge models
        $paymentDefinition->setChargeModels([]);

        // Set merchant preferences
        $merchantPreferences = new MerchantPreferences();
        $merchantPreferences->setReturnUrl('http://socialhose.local/auth/register-finish')
            ->setCancelUrl('http://socialhose.local/auth/register-finish')
            ->setAutoBillAmount('yes')
            ->setInitialFailAmountAction('CONTINUE')
            ->setMaxFailAttempts('0');
        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);

        //create plan
        try {
            $createdPlan = $plan->create($this->apiContext);

            try {
                $patch = new Patch();
                $value = new PayPalModel('{"state":"ACTIVE"}');
                $patch->setOp('replace')
                    ->setPath('/')
                    ->setValue($value);
                $patchRequest = new PatchRequest();
                $patchRequest->addPatch($patch);
                $createdPlan->update($patchRequest, $this->apiContext);
                $plan = Plan::get($createdPlan->getId(), $this->apiContext);

                // Output plan id
                echo 'New: ' . $plan->getId() . PHP_EOL;
            } catch (PayPalConnectionException $ex) {
                echo $ex->getCode();
                echo $ex->getData();
                die($ex);
            } catch (\Exception $ex) {
                die($ex);
            }
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (\Exception $ex) {
            die($ex);
        }

    }
}