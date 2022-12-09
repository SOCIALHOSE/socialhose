<?php

namespace AppBundle\Manager\Source;

use AppBundle\Response\SearchResponse;
use CacheBundle\Entity\Query\AbstractQuery;
use CacheBundle\Entity\SourceList;
use CacheBundle\Repository\SourceListRepository;
use Common\Enum\FieldNameEnum;
use Common\Enum\LanguageEnum;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\External\ExternalIndexInterface;
use IndexBundle\Index\External\InternalHoseIndex;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\Model\SourceDocument;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Psr\Log\LoggerInterface;
use UserBundle\Entity\User;

/**
 * Class SourceManager
 * @package AppBundle\Manager\Source
 */
class SourceManager implements SourceManagerInterface
{

    /**
     * Number of sources in bucket before index it.
     */
    const SOURCE_FETCH_BUCKET_SIZE = 200;

    /**
     * Filename which is used for storing last update date.
     */
    const UPDATE_FILE_NAME = 'source_update.date';

    /**
     * Filename which is used for storing current source hash filter value.
     */
    const LAST_PROCESSED_FILE_NAME = 'last_processed_value';

    /**
     * @var SourceIndexInterface
     */
    private $sourceIndex;

    /**
     * @var ExternalIndexInterface
     */
    private $externalIndex;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $pathToDateFile;

    /**
     * @var string
     */
    private $pathToLastProcessedFile;

    /**
     * SourceManager constructor.
     *
     * @param SourceIndexInterface   $sourceIndex   A SourceIndexInterface instance.
     * @param ExternalIndexInterface $externalIndex A ExternalIndexInterface instance.
     * @param EntityManagerInterface $em            A EntityManagerInterface instance.
     * @param LoggerInterface        $logger        A LoggerInterface instance.
     * @param string                 $varDir        A path to var directory.
     */
    public function __construct(
        SourceIndexInterface $sourceIndex,
        ExternalIndexInterface $externalIndex,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        $varDir
    ) {
        $this->sourceIndex = $sourceIndex;
        $this->externalIndex = $externalIndex;
        $this->em = $em;
        $this->logger = $logger;
        $this->pathToDateFile = realpath(rtrim($varDir, DIRECTORY_SEPARATOR))
            . DIRECTORY_SEPARATOR . self::UPDATE_FILE_NAME;
        $this->pathToLastProcessedFile = realpath(rtrim($varDir, DIRECTORY_SEPARATOR))
            . DIRECTORY_SEPARATOR . self::LAST_PROCESSED_FILE_NAME;
    }

    /**
     * Find all sources matched to specified builder.
     * If $sourceList is not null that make additional filter by specified
     * source list id.
     *
     * @param SearchRequestBuilderInterface $builder    A
     *                                                  SearchRequestBuilderInterface
     *                                                  instance.
     * @param SourceList                    $sourceList A SourceList entity
     *                                                  instance.
     *
     * @return SearchResponse
     */
    public function find(
        SearchRequestBuilderInterface $builder,
        SourceList $sourceList = null
    ) {
        //
        // Convert specified search request builder into source cache search
        // builder.
        //
        $builder = $this->sourceIndex->createRequestBuilder()
            ->fromSearchRequestBuilder($builder)
            ->setQuery($this->prepareQuery($builder->getQuery()))
            ->setFields([
                FieldNameEnum::SOURCE_TITLE,
                FieldNameEnum::SOURCE_LINK,
            ]);

        //
        // Get sources from specified source list if it requested.
        //
        if ($sourceList !== null) {
            $filterFactory = $builder->getFilterFactory();
            $builder->addFilter($filterFactory->eq('listIds', $sourceList->getId()));
        }

        $response = $builder->build()->execute();

        //
        // Now we should clean all source id's and left only exists but only
        // if we got specified user.
        //
        $sources = $response->getDocuments();
        if (count($sources) && ($builder->getUser() instanceof User)) {
            // Get all used source ids.
            $ids = array_keys(array_flip(\nspl\a\flatten(\nspl\a\map(
                \nspl\op\propertyGetter('listIds'),
                $sources
            ))));

            // Get only ids which exists.
            if (count($ids) > 0) {
                /** @var SourceListRepository $sourceListRepository */
                $sourceListRepository = $this->em->getRepository(SourceList::class);
                $ids = array_map(function (array $row) {
                    return $row['id'];
                }, $sourceListRepository->createQueryBuilder('List')
                    ->select('List.id')
                    ->where('List.id in (' . implode(',', $ids) . ') AND List.user = :id')
                    ->setParameter('id', $builder->getUser()->getId())
                    ->getQuery()
                    ->getArrayResult());
            }

            //
            // Filter all source document's and left only exists list ids in
            // listIds field.
            //
            $sources = array_map(function (SourceDocument $document) use ($ids) {
                $document->listIds = array_values(array_intersect($document->listIds, $ids));

                return $document;
            }, $sources);
        }

        return new SearchResponse(
            $sources,
            $response->getAggregationResults(),
            $response->getTotalCount(),
            $response->getUniqueCount()
        );
    }

    /**
     * Place all specified sources into specified lists.
     *
     * @param User              $user    A User entity instance.
     * @param string|string[]   $sources Array of source id or single id.
     * @param integer|integer[] $lists   Array of SourceList entity id or single
     *                                   id.
     *
     * @return void
     */
    public function bindSourcesToLists(User $user, $sources, $lists)
    {
        /** @var SourceDocument[] $sourceDocuments */
        $sourceDocuments = $this->sourceIndex->get($sources, [ 'listIds' ]);
        $conn = $this->em->getConnection();
        $lists = (array) $lists;

        //
        // Get source list ids for current user.
        //
        /** @var SourceListRepository $repository */
        $repository = $this->em->getRepository(SourceList::class);
        $availableIds = $repository->getAvailableIdsForUser($user->getId());

        //
        // Now we should iterate through each source document and find out which
        // lists we should add to it.
        //
        foreach ($sourceDocuments as $sourceDocument) {
            $sourceId = $sourceDocument['id'];
            $sourceLists = $sourceDocument->listIds;

            //
            // Remove all ids which presents in available list from actual list
            // ids of current source document and add new one.
            //
            $cleaned = array_diff($sourceLists, $availableIds);
            $new = array_merge($cleaned, $lists);

            // Add to index.
            $this->sourceIndex->update($sourceId, [
                'listIds' => $new,
            ]);

            //
            // Remove old relations and add new.
            //
            $conn->exec("
                DELETE FROM cross_sources_source_lists WHERE source = '{$sourceId}'
            ");
            $conn->exec(
                'INSERT INTO cross_sources_source_lists (source, list_id) VALUES '
                . implode(',', array_map(function ($list) use ($sourceId) {
                    return "('{$sourceId}', $list)";
                }, $new))
            );

            // Recompute count of sources for each updated list.
            $this->recomputeCount(array_merge($sourceLists, $lists));
        }
    }

    /**
     * Add specified sources to specific source list.
     *
     * @param array   $sources Array of updates sources ids.
     * @param integer $id      A SourceList entity id.
     *
     * @return void
     */
    public function addSourcesToList(array $sources, $id)
    {
        $conn = $this->em->getConnection();

        $filterFactory = $this->sourceIndex->getFilterFactory();
        $request = $this->sourceIndex->createRequestBuilder()
            ->addFilter($filterFactory->in('id', $sources))
            ->build();

        //
        // Add list into all specified sources.
        // If source already has association with specified list then we don't
        // duplicate.
        //
        $this->sourceIndex->updateByQuery(
            $request,
            'ctx._source.listIds.removeIf(item -> item == params.id);ctx._source.listIds.add(params.id)',
            [ 'id' => $id ]
        );

        //
        // Add relations in database.
        //
        $conn->exec(sprintf(
            'INSERT INTO cross_sources_source_lists (source, list_id) VALUE %s',
            implode(',', \nspl\a\map(function ($source) use ($id) {
                return "('{$source}', {$id})";
            }, $sources))
        ));

        //
        // Recompute count of sources for updated list.
        //
        $this->recomputeCount([ $id ]);
    }

    /**
     * Get all source's which used in filter's of specified query.
     *
     * @param AbstractQuery $query  A AbstractQuery entity instance.
     * @param array         $fields Array of requested fields.
     *
     * @return array[]
     */
    public function getSourcesForQuery(AbstractQuery $query, array $fields)
    {
        if (isset($query->getRawFilters()['source'])) {
            $ids = $query->getRawFilters()['source']['ids'];

            return array_map(
                function (DocumentInterface $document) use ($fields) {
                    $result = [];

                    foreach ($fields as $field) {
                        $result[$field] = $document[$field];
                    }

                    return $result;
                },
                $this->sourceIndex->get($ids, $fields)
            );
        }

        return [];
    }

    /**
     * Get all source list's which used in filter's of specified query.
     *
     * @param AbstractQuery $query  A AbstractQuery entity instance.
     * @param array         $fields Array of requested fields.
     *
     * @return array[]
     */
    public function getSourceListsForQuery(AbstractQuery $query, array $fields)
    {
        if (isset($query->getRawFilters()['sourceList'])) {
            $sourceLists = $query->getRawFilters()['sourceList'];
            $ids = [];

            if (isset($sourceLists['include'])) {
                $ids[] = $sourceLists['include'];
            }

            if (isset($sourceLists['exclude'])) {
                $ids[] = $sourceLists['exclude'];
            }

            /** @var SourceListRepository $sourceListRepository */
            $sourceListRepository = $this->em->getRepository(SourceList::class);

            return $sourceListRepository->createQueryBuilder('List')
                ->select('partial List.{'. implode(',', $fields) .'}')
                ->where('List.id in ('. implode(',', \nspl\a\flatten($ids)) .')')
                ->getQuery()
                ->getArrayResult();
        }

        return [];
    }

    /**
     * Get available advanced filters.
     *
     * @param SearchRequestBuilderInterface $builder    A
     *                                                  SearchRequestBuilderInterface
     *                                                  instance.
     * @param SourceList|null               $sourceList A SourceList entity
     *                                                  instance.
     *
     * @return mixed
     */
    public function getAvailableFilters(
        SearchRequestBuilderInterface $builder,
        SourceList $sourceList = null
    ) {
        $request = $this->sourceIndex->createRequestBuilder()
            ->fromSearchRequestBuilder($builder)
            ->setQuery($this->prepareQuery($builder->getQuery()))
            ->setFields([
                FieldNameEnum::SOURCE_TITLE,
                FieldNameEnum::SOURCE_LINK,
            ])
            ->build();

        if ($sourceList !== null) {
            $filterFactory = $builder->getFilterFactory();
            $builder->addFilter($filterFactory->eq('listIds', $sourceList->getId()));
        }

        return $request->getAvailableAdvancedFilters();
    }

    /**
     * Make relation between specified source and source lists.
     * All exists source relation will be overridden.
     *
     * @param integer $source Source id.
     * @param array   $lists  Array of SourceList entity ids.
     *
     * @return void
     */
    public function replaceRelation($source, array $lists)
    {
        $oldLists = current($this->sourceIndex->get($source, 'listIds'))->listIds;

        $this->sourceIndex->update($source, [
            'listIds' => $lists,
        ]);

        $this->em->getConnection()->transactional(function (Connection $conn) use ($source, $oldLists, $lists) {
            // Remove all old relations between source and source lists.
            $conn->exec("
                DELETE FROM cross_sources_source_lists
                WHERE source = '{$source}'
            ");

            // Add new relations.
            $conn->exec(
                'INSERT INTO cross_sources_source_lists (source, list_id) VALUES '
                . implode(',', array_map(function ($list) use ($source) {
                    return "('{$source}', {$list})";
                }, $lists))
            );

            // Recompute count of sources for each updated list.
            $this->recomputeCount(array_merge($oldLists, $lists));
        });
    }

    /**
     * Unbind all binded sources from specified lists.
     *
     * @param integer|integer[] $lists Array of SourceList entity id or single id.
     *
     * @return void
     */
    public function unbindSourcesFromLists($lists)
    {
        $lists = (array) $lists;

        $filterFactory = $this->sourceIndex->getFilterFactory();
        $request = $this->sourceIndex->createRequestBuilder()
            ->setFilters($filterFactory->in('listIds', $lists))
            ->setSources([ '_id' ])
            ->build();

        //
        // See https://docs.oracle.com/javase/8/docs/api/java/util/Collection.html#removeIf%2Djava.util.function.Predicate%2D
        //
        $this->sourceIndex->updateByQuery($request, '
            ctx._source.listIds.removeIf(item -> params.lists.contains(item))
        ', [ 'lists' => $lists ]);
    }

    /**
     * Update source cache.
     *
     * Fetch source from external index and store it into our cache. If we
     * already got sources in our cache se try to get source occurred after
     * oldest source.
     *
     * If our source cache is empty, we just get all available source. This
     * should be done in background.
     *
     * @return void
     */
    public function pullFromExternal()
    {
        $lastUpdate = null;
        if (is_file($this->pathToDateFile)) {
            $lastUpdate = new \DateTime(file_get_contents($this->pathToDateFile));
        }

        //
        // We should fetch all sources if we don't has any sources in our cache.
        //
        if ($lastUpdate === null) {
            if ($this->externalIndex instanceof InternalHoseIndex) {
                $this->fetchSourcesFromFake();
            } else {
                $this->fetchSources();
            }

            file_put_contents($this->pathToDateFile, date_create()->format('c'));
        } else {
            //
            // Do nothing for now.
            // todo uncomment it when we are ready for updating sources.
            //
            // $this->fetchSources($lastUpdate);
            throw new \RuntimeException('Unimplemented');
        }
    }

    /**
     * @return SourceIndexInterface
     */
    public function getIndex()
    {
        return $this->sourceIndex;
    }

    /**
     * Create proper aggregation for fetching unique source.
     *
     * @param IndexInterface $index   A IndexInterface instance.
     * @param array          $sources Array of requested fields.
     *
     * @return \IndexBundle\Aggregation\AggregationInterface
     */
    private function createAggregation(IndexInterface $index, array $sources)
    {
        $aggrFactory = $index->getAggregationFactory();
        $aggr = $index->getAggregation();

        //
        // Get unique documents by source_hashcode.
        //
        $hashAggr = $aggr->getAggregation('hash', $aggrFactory->terms([
            'field_name' => FieldNameEnum::SOURCE_HASHCODE,
            'size' => 1000000, // We should get all available results for
                               // this aggregation.
        ]));

        //
        // Fetch required fields from founded unique douments.
        //
        $documentAggr = $aggr->getAggregation('document', $aggrFactory->topHits([
            'size' => 1, // 'cause we need to get content of sources and we have
                         // already made sure that source are unique.
            'sources' => $sources,
        ]));

        return $hashAggr->addAggregation(
            $documentAggr
        );
    }

    /**
     * @param \DateTime $lastUpdate Last update date.
     *
     * @return void
     */
    private function fetchSources(\DateTime $lastUpdate = null)
    {
        $filterFactory = $this->externalIndex->getFilterFactory();

        $aggregations = $this->createAggregation($this->externalIndex, [
            FieldNameEnum::SOURCE_HASHCODE,
            FieldNameEnum::SOURCE_TITLE,
            FieldNameEnum::SOURCE_FEED_TITLE,
            FieldNameEnum::SOURCE_PUBLISHER_TYPE,
            FieldNameEnum::SOURCE_LINK,
            FieldNameEnum::COUNTRY,
            FieldNameEnum::STATE,
            FieldNameEnum::CITY,
            FieldNameEnum::SECTION,
            FieldNameEnum::LANG,
        ]);

        $lastValue = null;
        if (is_file($this->pathToLastProcessedFile)) {
            $lastValue = trim(file_get_contents($this->pathToLastProcessedFile));
        }

        //
        // We should split request to small pieces by filtering source hashcode
        // by first three symbols from '000' to 'zzz'. We got 46655 unique
        // combinations.
        //
        $valueGenerator = $this->generateSourceHashFilterValue($lastValue);

        foreach ($valueGenerator as $value) {
            sleep(1);
            $this->logger->info(sprintf(
                'Fetching results for sources with hashcode started with \'%s\'',
                $value
            ));
            try {
                $condition = $filterFactory->andX([
                    //
                    // Get only documents
                    //
                    $filterFactory->eq(FieldNameEnum::SOURCE_HASHCODE, "({$value}*)"),
                    //
                    // We should not get documents which language is unknown.
                    //
                    $filterFactory->not($filterFactory->eq(FieldNameEnum::LANG, LanguageEnum::UNKNOWN)),
                ]);

                //
                // Add filter by date if it necessary.
                //
                if ($lastUpdate !== null) {
                    $condition->add(
                        $filterFactory->gt(FieldNameEnum::DATE_FOUND, $lastUpdate)
                    );
                }

                $results = $this->externalIndex->createRequestBuilder()
                    ->setLimit(0)// 'cause we need only result of aggregations.
                    ->addFilter($condition)
                    ->setAggregation($aggregations)
                    ->build()
                    ->execute()
                    ->getAggregationResults();

                //
                // Get result of first aggregations.
                //
                $results = is_array($results) ? current($results) : [];
                $results = is_array($results) ? $results : [];
                $bucket = [];

                $this->logger->info(sprintf(
                    'Got response from external index with %s new sources',
                    count($results)
                ));
                $this->logger->info('Memory usage ' . memory_get_usage());

                foreach ($results as $hashCodeAggr) {
                    $data = current($hashCodeAggr['sub']['document']);

                    /** @var ArticleDocumentInterface $document */
                    $document = $this->externalIndex->getStrategy()
                        ->createDocument($data);

                    $bucket[] = $this->sourceIndex->getStrategy()->createDocument(
                        $document->toSourceDocumentData()
                    );

                    if (count($bucket) >= self::SOURCE_FETCH_BUCKET_SIZE) {
                        $this->sourceIndex->index($bucket);
                        $bucket = [];
                        gc_collect_cycles();
                    }
                }

                //
                // Index remain document in bucket.
                //
                if (count($bucket) > 0) {
                    $this->sourceIndex->index($bucket);
                    unset($bucket);
                    gc_collect_cycles();
                }
            } catch (\Exception $exception) {
                throw new \RuntimeException(sprintf(
                    'Got \'%s\' exception while fetching results for \'%s\' part of source hashcode',
                    $exception->getMessage(),
                    $value
                ));
            } finally {
                file_put_contents($this->pathToLastProcessedFile, $value);
            }
        }
    }

    /**
     * Fetch sources from fake index.
     *
     * @param \DateTime|null $lastUpdate Last update date.
     *
     * @return void
     */
    private function fetchSourcesFromFake(\DateTime $lastUpdate = null)
    {
        $filterFactory = $this->externalIndex->getFilterFactory();

        $aggregations = $this->createAggregation($this->externalIndex, [
            FieldNameEnum::SOURCE_HASHCODE,
            FieldNameEnum::SOURCE_TITLE,
            FieldNameEnum::SOURCE_FEED_TITLE,
            FieldNameEnum::SOURCE_PUBLISHER_TYPE,
            FieldNameEnum::SOURCE_LINK,
            FieldNameEnum::COUNTRY,
            FieldNameEnum::STATE,
            FieldNameEnum::CITY,
            FieldNameEnum::SECTION,
            FieldNameEnum::LANG,
        ]);

        try {
            $condition = $filterFactory->andX();

            //
            // Add filter by date if it necessary.
            //
            if ($lastUpdate !== null) {
                $condition->add(
                    $filterFactory->gt(FieldNameEnum::DATE_FOUND, $lastUpdate)
                );
            }

            $results = $this->externalIndex->createRequestBuilder()
                ->setLimit(0)// 'cause we need only result of aggregations.
                ->addFilter($condition)
                ->setAggregation($aggregations)
                ->build()
                ->execute()
                ->getAggregationResults();

            //
            // Get result of first aggregations.
            //
            $results = is_array($results) ? current($results) : [];
            $results = is_array($results) ? $results : [];
            $bucket = [];

            $this->logger->info(sprintf(
                'Got response from external index with %s new sources',
                count($results)
            ));

            foreach ($results as $hashCodeAggr) {
                $data = current($hashCodeAggr['sub']['document']);

                /** @var ArticleDocumentInterface $document */
                $document = $this->externalIndex->getStrategy()
                    ->createDocument($data);

                $bucket[] = $this->sourceIndex->getStrategy()->createDocument(
                    $document->toSourceDocumentData()
                );

                if (count($bucket) >= self::SOURCE_FETCH_BUCKET_SIZE) {
                    $this->sourceIndex->index($bucket);
                    $bucket = [];
                    gc_collect_cycles();
                }
            }

            //
            // Index remain document in bucket.
            //
            if (count($bucket) > 0) {
                $this->sourceIndex->index($bucket);
                unset($bucket);
                gc_collect_cycles();
            }
        } catch (\Exception $exception) {
            throw new \RuntimeException(sprintf(
                'Got \'%s\' exception while fetching sources',
                $exception->getMessage()
            ));
        }
    }

    /**
     * @param string|null $startValue Value from which we should start.
     *
     * @return \Generator
     */
    private function generateSourceHashFilterValue($startValue = null)
    {
        //
        // Return first generated key.
        //
        $value = $startValue !== null ? $startValue : '000';
        yield $value;
        do {
            //
            // Use 36 number base for generating next value.
            //
            $value = sprintf('%\'.03s', base_convert(base_convert($value, 36, 10) + 1, 10, 36));
            yield $value;
        } while ($value !== 'zzz');
    }

    //
    // TODO uncomment and rewrite if updating is required.
    //
//    /**
//     * Remove duplicate source from cache.
//     *
//     * @return void
//     */
//    private function removeDuplicates()
//    {
//        $response = $this->sourceIndex->createRequestBuilder()
//            ->setLimit(0)
//            ->setAggregation(
//                $this->createAggregation($this->sourceIndex, [ 'listIds' ])
//            )
//            ->build()
//            ->execute();
//
//        $typeGrouping = current($response->getAggregationResults());
//
//        foreach ($typeGrouping as $group1) {
//            $langGroup = $group1['sub']['group_by_lang'];
//            $ids = [];
//
//            foreach ($langGroup as $group2) {
//                $titleGroup = $group2['sub']['group_by_title'];
//
//                foreach ($titleGroup as $group3) {
//                    $hits = array_map(function (array $hit) {
//                        return $hit['_id'];
//                    }, array_filter($group3['sub']['top_hits'], function (array $hit) {
//                        return count($hit['listIds']) === 0;
//                    }));
//
//                    if (count($hits) > 1) {
//                        // We should not remove single source with empty source
//                        // list.
//                        $ids = array_merge($ids, $hits);
//                    }
//                }
//            }
//
//            if (count($ids) > 0) {
//                $this->sourceIndex->remove($ids);
//            }
//        }
//    }

    /**
     * Prepare search query.
     *
     * @param string $query A raw search query.
     *
     * @return string
     */
    private function prepareQuery($query)
    {
        //
        // We should wrap all words in query with asterisk's in order to make
        // partial word search.
        //
        // Also we surround whole query by bracket's for we same reasons.
        //
        $tokens = array_filter(array_map('trim', explode(' ', $query)));
        if (count($tokens) === 0) {
            return '';
        }

        return '('. implode(' ', array_map(function ($token) {
            return "*{$token}*";
        }, $tokens)) .')';
    }

    /**
     * Recompute count of sources on specified lists.
     *
     * @param array $ids Array of SourceList entity ids.
     *
     * @return void
     */
    private function recomputeCount(array $ids)
    {
        $this->em->getConnection()->exec('
            UPDATE source_list sl
            RIGHT JOIN
            (
                SELECT list_id, COUNT(source) as count
                FROM cross_sources_source_lists
                WHERE list_id IN ('. implode(',', $ids) .')
                GROUP BY list_id
            ) x ON x.list_id = sl.id
            SET
                sl.source_number = x.count
        ');
    }
}
