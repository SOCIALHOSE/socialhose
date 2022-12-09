<?php

namespace PaymentBundle\Command;

use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Gateway\Factory\PaymentGatewayFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\Plan;
use UserBundle\Repository\PlanRepository;

/**
 * Class BillingPlanSyncCommand
 *
 * @package PaymentBundle\Command
 */
class BillingPlanSyncCommand extends Command
{

    const NAME = 'socialhose:billing:sync-plans';

    /**
     * @var PlanRepository
     */
    private $planRepository;

    /**
     * @var PaymentGatewayFactoryInterface
     */
    private $gatewayFactory;

    /**
     * BillingPlanSyncCommand constructor.
     *
     * @param PlanRepository                 $planRepository A PlanRepository
     *                                                       instance.
     * @param PaymentGatewayFactoryInterface $gatewayFactory A PaymentGatewayFactoryInterface
     *                                                       instance.
     */
    public function __construct(
        PlanRepository $planRepository,
        PaymentGatewayFactoryInterface $gatewayFactory
    ) {
        parent::__construct(self::NAME);

        $this->planRepository = $planRepository;
        $this->gatewayFactory = $gatewayFactory;
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
        /** @var Plan[] $plans */
        $plans = $this->planRepository->findAll();

        foreach (PaymentGatewayEnum::getAvailables() as $name) {
            $gateway = $this->gatewayFactory->getGateway(new PaymentGatewayEnum($name));

            $output->writeln("Sync plan with <comment>{$name}</comment> payment gateway");
            foreach ($plans as $plan) {
                $output->write("\tPlan <comment>{$plan->getInnerName()}</comment> ... ");
                try {
                    $gateway->updatePlan($plan);
                    $output->writeln('[ <info>OK</info> ]');
                } catch (\Exception $e) {
                    $output->writeln('[ <error>ERROR</error> ]');
                    throw $e;
                }
            }
        }

        return 0;
    }
}
