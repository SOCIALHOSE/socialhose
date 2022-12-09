<?php

namespace AppBundle\Command;

use Common\Enum\FieldNameEnum;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use IndexBundle\Index\Strategy\HoseIndexStrategy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReindexDocumentsCommand
 * @package AppBundle\Command
 */
class ReindexDocumentsCommand extends AbstractSingleCopyCommand
{

    /**
     * Command name.
     */
    const NAME = 'socialhose:reindex:documents';

    /**
     * Size of document which is fetched for reindex.
     */
    const BUCKET_SIZE = 10;

    /**
     * Name of the file where command should store idx of failed bucket.
     */
    const FAILED_IDX_FILE = 'document_reindex_fail_idx';

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $varPath;

    /**
     * @var HoseIndexStrategy
     */
    private $strategy;

    /**
     * FetchSourcesCommand constructor.
     *
     * @param LoggerInterface $logger  A LoggerInterface instance.
     * @param string          $host    A elasticsearch host name.
     * @param string          $port    A elasticsearch port.
     * @param string          $varPath Path to directory where failed idx file
     *                                 should stored.
     */
    public function __construct(
        LoggerInterface $logger,
        $host,
        $port,
        $varPath
    ) {
        parent::__construct(self::NAME, $logger);

        $this->host = $host;
        $this->port = $port;
        $this->varPath = realpath($varPath);

        if ($this->varPath === false) {
            throw new \InvalidArgumentException(sprintf(
                '$varPath value \'%s\' is invalid path.',
                $varPath
            ));
        }

        if (! is_dir($this->varPath)) {
            throw new \InvalidArgumentException(sprintf(
                '$varPath value \'%s\' is not a directory.',
                $varPath
            ));
        }

        if (! is_writable($this->varPath)) {
            throw new \InvalidArgumentException(sprintf(
                '$varPath value \'%s\' is not available for writing.',
                $varPath
            ));
        }
    }

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setDescription('Migrate documents from one document index to another')
            ->addArgument('src', InputArgument::REQUIRED, 'Source index name')
            ->addArgument('dest', InputArgument::REQUIRED, 'Destination index name');
    }

    /**
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     *
     * @return null|integer
     *
     * @throws \Exception If can't reindex documents.
     */
    protected function doExecute(InputInterface $input, OutputInterface $output)
    {
        $currentBucketIdx = null;

        try {
            $destIndex = $input->getArgument('dest');

            $client = ClientBuilder::create()
                ->setHosts([
                    [
                        'host' => $this->host,
                        'port' => $this->port,
                    ],
                ])
                ->build();

            $response = $client->search([
                'body' => ['query' => ['match_all' => (object) []]],
                'index' => $input->getArgument('src'),
                'type' => 'document',
                'scroll' => '1m',
                'size' => self::BUCKET_SIZE,
            ]);

            $totalBucketCount = $response['hits']['total'] / self::BUCKET_SIZE;

            $scrollId = $response['_scroll_id'];
            $currentBucketIdx = $this->getCurrentBucketIdx();

            //
            // Scroll required number of bucket.
            //
            // We can't use offset 'cause ElasticSearch are limiting max allowed
            // offset value. Also because of it we use scroll api instead of just
            // make search with offset.
            //
            for ($i = 0; $i < $currentBucketIdx; ++$i) {
                $response = $client->scroll([ 'scroll_id' => $scrollId ]);
            }

            //
            // Reindex all documents.
            //
            while ($this->indexDocuments($response['hits']['hits'], $client, $destIndex)) {
                $output->writeln(sprintf(
                    'Process %d from %d buckets',
                    $currentBucketIdx,
                    $totalBucketCount
                ));

                $response = $client->scroll([
                    'scroll_id' => $scrollId,
                    'scroll' => '1m',
                ]);
                $currentBucketIdx++;
            }

            if (file_exists($this->varPath . DIRECTORY_SEPARATOR . self::FAILED_IDX_FILE)) {
                unlink($this->varPath . DIRECTORY_SEPARATOR . self::FAILED_IDX_FILE);
            }
        } catch (\Exception $exception) {
            if ($currentBucketIdx !== null) {
                file_put_contents(
                    $this->varPath . DIRECTORY_SEPARATOR . self::FAILED_IDX_FILE,
                    $currentBucketIdx
                );
            }
            throw $exception;
        }

        return 0;
    }

    /**
     * @param array  $documents Array of raw documents.
     * @param Client $client    A ElasticSearch Client instance.
     * @param string $index     A index name.
     *
     * @return boolean
     */
    private function indexDocuments(array $documents, Client $client, $index)
    {
        if (count($documents) === 0) {
            return false;
        }

        // We should split documents into several buckets 'cause we may exceed allowed
        // request size for ElasticSearch (10mb for AWS instance).
        $buckets = [];

        $idx = 0;
        $count = 0;
        foreach ($documents as $document) {
            if (++$count > self::BUCKET_SIZE / 2) {
                $idx++;
                $count = 0;
            }

            $data = $document['_source'];
            if (isset($data['collection_id'])) {
                $data[FieldNameEnum::COLLECTION_ID] = $data['collection_id'];
                $data[FieldNameEnum::COLLECTION_TYPE] = $data['collection_type'];
            }

            if (isset($data['deleted_from'])) {
                $data[FieldNameEnum::DELETE_FROM] = $data['deleted_from'];
            }

            if (! isset($data[FieldNameEnum::DELETE_FROM])) {
                $data[FieldNameEnum::DELETE_FROM] = [];
            }

            $buckets[$idx][] = [
                'index' => [
                    '_index' => $index,
                    '_type' => 'document',
                ],
            ];
            $buckets[$idx][] = $this->getStrategy()->getIndexableData($data);
        }

        foreach ($buckets as $bucket) {
            $client->bulk(['body' => $bucket]);
        }

        return true;
    }

    /**
     * @return HoseIndexStrategy
     */
    private function getStrategy()
    {
        if ($this->strategy === null) {
            $this->strategy = new HoseIndexStrategy();
        }

        return $this->strategy;
    }

    /**
     * @return integer
     */
    private function getCurrentBucketIdx()
    {
        $filePath = $this->varPath . DIRECTORY_SEPARATOR . self::FAILED_IDX_FILE;

        if (file_exists($filePath)) {
            return (int) file_get_contents($filePath);
        }

        return 0;
    }
}
