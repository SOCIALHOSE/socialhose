<?php

namespace PaymentBundle\Command;

use PayPal\Api\Plan;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BillingPlanListCommand
 * @package PaymentBundle\Command
 */
class BillingPlanListCommand extends Command
{
    const NAME = 'socialhose:billing:list-plans';

    /** @var ApiContext */
    private $apiContext;

    /**
     * BillingPlanListCommand constructor.
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
        $availablePayPalPlans = Plan::all(
            ['page_size' => 10, 'status' => 'ACTIVE'],
            $this->apiContext
        )->getPlans();

        if ($availablePayPalPlans === null) {
            echo 'Empty' . PHP_EOL;;
            return 0;
        }

        foreach ($availablePayPalPlans as $plan) {
            echo 'All: ' . $plan->getId() . PHP_EOL;
        }
        return 0;
    }
}
