<?php

namespace CacheBundle\Command;

use CacheBundle\Entity\Page;
use CacheBundle\Entity\Query\SimpleQuery;
use CacheBundle\Repository\SimpleQueryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveOldQueriesCommand
 * @package CacheBundle\Command
 */
class RemoveOldQueriesCommand extends Command
{

    const NAME = 'socialhose:query:remove_old';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * RemoveOldQueriesCommand constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(self::NAME);

        $this->em = $em;
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Remove old queries from cache');
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
     * @throws \LogicException When this abstract method is not implemented.
     *
     * @see setCode()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var SimpleQueryRepository $queryRepository */
        $queryRepository = $this->em->getRepository(SimpleQuery::class);
        /** @var EntityRepository $pageRepository */
        $pageRepository = $this->em->getRepository(Page::class);

        $expr = $this->em->getExpressionBuilder();

        $ids = $queryRepository->getOld();
        $pageRepository->createQueryBuilder('Page')
            ->delete()
            ->where($expr->in('Page.query', $ids))
            ->getQuery()
            ->execute();

        $queryRepository->createQueryBuilder('Query')
            ->delete()
            ->where($expr->in('Query.id', $ids))
            ->getQuery()
            ->execute();

        return 0;
    }
}
