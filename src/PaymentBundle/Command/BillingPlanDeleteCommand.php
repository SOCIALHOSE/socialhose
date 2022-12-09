<?php

namespace PaymentBundle\Command;

use PayPal\Api\Plan;
use PayPal\Rest\ApiContext;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BillingPlanDeleteCommand
 * @package PaymentBundle\Command
 */
class BillingPlanDeleteCommand extends Command
{
    const NAME = 'socialhose:billing:delete-plans';

    /** @var ApiContext */
    private $apiContext;

    /**
     * BillingPlanDeleteCommand constructor.
     * @param ApiContext $apiContext
     */
    public function __construct(ApiContext $apiContext)
    {
        parent::__construct(self::NAME);
        $this->apiContext = $apiContext;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName(self::NAME)
            ->setDescription('Delete all plans.')
            ->addOption('force', null, InputOption::VALUE_NONE);
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
        if (! $input->getOption('force')) {
            $message = 'Provide --force option if you really want to remove plans.';
            $output->writeln($message);
            return 0;
        }

        $availablePayPalPlans = Plan::all([
            'page_size' => 10,
            'status' => 'ACTIVE',
        ], $this->apiContext)->getPlans();

        foreach ($availablePayPalPlans as $plan) {
            $plan->delete($this->apiContext);
        }

        echo 'Mission completed';
        return 0;
    }
}
