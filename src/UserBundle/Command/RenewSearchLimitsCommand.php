<?php

namespace UserBundle\Command;

use AppBundle\Command\AbstractSingleCopyCommand;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Repository\SubscriptionRepository;

/**
 * Class RenewSearchLimitsCommand
 *
 * @package UserBundle\Command
 */
class RenewSearchLimitsCommand extends AbstractSingleCopyCommand
{

    const NAME = 'socialhose:renew-search-limits';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RenewSearchLimitsCommand constructor.
     *
     * @param EntityManagerInterface $em     A EntityManagerInterface instance.
     * @param LoggerInterface        $logger A LoggerInterface instance.
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        parent::__construct(self::NAME, $logger);

        $this->em = $em;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Set all searchPerDay limits to zero');
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(AbstractSubscription::class);
        $repository->renewSearchLimits();

        return 0;
    }
}
