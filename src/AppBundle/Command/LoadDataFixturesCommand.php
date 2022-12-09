<?php

namespace AppBundle\Command;

use AppBundle\Utils\Purger\TruncateORMPurger;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Fixture\Executor\Factory\IndexFixtureExecutorFactory;
use IndexBundle\Fixture\Loader\IndexFixtureLoader;
use IndexBundle\Index\External\InternalHoseIndex;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\Util\Initializer\ExternalIndexInitializer;
use IndexBundle\Util\Initializer\InternalIndexInitializer;
use IndexBundle\Util\Initializer\SourceIndexInitializer;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class LoadDataFixturesCommand
 * @package AppBundle\Command
 */
class LoadDataFixturesCommand extends AbstractSingleCopyCommand
{

    /**
     * Command name.
     */
    const NAME = 'socialhose:fixtures:load';

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var InternalHoseIndex
     */
    private $externalIndex;

    /**
     * @var InternalIndexInterface
     */
    private $internalIndex;

    /**
     * @var SourceIndexInterface
     */
    private $sourceIndex;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * LoadDataFixturesCommand constructor.
     * @param KernelInterface        $kernel        A KernelInterface instance.
     * @param EntityManagerInterface $em            A EntityManagerInterface
     *                                              instance.
     * @param InternalHoseIndex       $externalIndex A HoseIndex instance.
     * @param InternalIndexInterface $internalIndex A InternalIndexInterface
     *                                              instance.
     * @param SourceIndexInterface   $sourceIndex   A SourceIndexInterface
     *                                              instance.
     * @param ContainerInterface     $container     A ContainerInterface instance.
     * @param LoggerInterface        $logger        A LoggerInterface instance.
     */
    public function __construct(
        KernelInterface $kernel,
        EntityManagerInterface $em,
        InternalHoseIndex $externalIndex,
        InternalIndexInterface $internalIndex,
        SourceIndexInterface $sourceIndex,
        ContainerInterface $container,
        LoggerInterface $logger
    ) {
        parent::__construct(self::NAME, $logger);

        $this->kernel = $kernel;
        $this->em = $em;
        $this->externalIndex = $externalIndex;
        $this->internalIndex = $internalIndex;
        $this->sourceIndex = $sourceIndex;
        $this->container = $container;
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
            ->setDescription('Load database and index fixtures.')
            ->addOption('force', null, InputOption::VALUE_NONE)
            ->addOption(
                'without-index',
                null,
                InputOption::VALUE_NONE,
                'Do not load index fixtures.'
            )
            ->addOption(
                'without-database',
                null,
                InputOption::VALUE_NONE,
                'Do not load database fixtures.'
            );
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $withoutDatabase = $input->getOption('without-database');
        $withoutIndex = $input->getOption('without-index');

        if ($withoutDatabase && $withoutIndex) {
            return 0;
        }

        if (! $input->getOption('force')) {
            // Because this command can rebuild all index we use this option as
            // as flag in order to prevent accidental run.
            $message = 'Provide --force option if you really want to initialize index.';
            $output->writeln($message);
            return 0;
        }

        if (! $input->getOption('no-interaction') && ! $this->confirm($input, $output)) {
            return 0;
        }

        // Get list of available data fixtures paths.
        $paths = $this->getFixturesPaths();

        // Load database fixtures.
        if (! $withoutDatabase) {
            $this->loadDatabase($output, $paths);
        }

        // Load index fixtures.
        if (! $withoutIndex) {
            $this->loadIndex($output, $paths);
        }

        return 0;
    }

    /**
     * @return array
     */
    private function getFixturesPaths()
    {
        $paths = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            $path = $bundle->getPath() . '/DataFixtures/';
            if (is_dir($path)) {
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * @param InputInterface  $input  A InputInterface instance.
     * @param OutputInterface $output A OutputInterface instance.
     *
     * @return boolean
     */
    private function confirm(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Helper\SymfonyQuestionHelper $helper */
        $helper = $this->getHelper('question');

        $output->writeln('');
        $output->writeln('<comment>All data will be purged.</comment>');
        $question = new ConfirmationQuestion('Are you sure (y/N)? ', false);

        return (boolean) $helper->ask($input, $output, $question);
    }

    /**
     * @param OutputInterface $output A OutputInterface instance.
     * @param array           $paths  Array of fixtures directories path.
     *
     * @return void
     */
    private function loadDatabase(OutputInterface $output, array $paths)
    {
        //
        // Purge internal tables.
        //
        $this->em->getConnection()->executeQuery('TRUNCATE internal_notification_scheduling');

        $output->writeln('<comment>Load database fixtures:</comment>');

        $loader = new DataFixturesLoader($this->container);
        foreach ($paths as $path) {
            $loader->loadFromDirectory($path);
        }

        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new \InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- " . implode("\n- ", $paths))
            );
        }

        $purger = new TruncateORMPurger(new ORMPurger($this->em));
        $purger->purge();

        $executor = new ORMExecutor($this->em);
        $executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });
        $executor->execute($fixtures, true);
    }

    /**
     * @param OutputInterface $output A OutputInterface instance.
     * @param array           $paths  Array of fixtures directories path.
     *
     * @return void
     */
    private function loadIndex(OutputInterface $output, array $paths)
    {
        $output->writeln('<comment>Load index fixtures:</comment>');

        $loader = new IndexFixtureLoader($this->container);
        foreach ($paths as $path) {
            $loader->loadFromDirectory($path);
        }

        $fixtures = $loader->getFixtures();

        // Purge indexes.
        ExternalIndexInitializer::initialize($this->externalIndex);
        InternalIndexInitializer::initialize($this->internalIndex);
        SourceIndexInitializer::initialize($this->sourceIndex);

        $executorFactory = new IndexFixtureExecutorFactory();
        $executorFactory->external($this->externalIndex)
            ->setLogger(function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            })
            ->execute($fixtures);
        $executorFactory->internal($this->internalIndex)
            ->setLogger(function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            })
            ->execute($fixtures);
        $executorFactory->source($this->sourceIndex)
            ->setLogger(function ($message) use ($output) {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            })
            ->execute($fixtures);
    }
}
