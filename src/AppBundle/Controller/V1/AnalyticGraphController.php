<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\AbstractCRUDController;
use ApiBundle\Controller\Annotation\Roles;
use CacheBundle\Entity\Analytic\Analytic;
use DateInterval;
use DatePeriod;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use CacheBundle\Repository\AnalyticRepository;
use CacheBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AnalyticGraphController
 * @package AppBundle\Controller\V1
 *
 * @Route(service="app.controller.analytic-graph")
 */
class AnalyticGraphController extends AbstractCRUDController
{

    /**
     * Get data for influence list
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/influencer/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     *
     * @param Request $request
     * @param $id
     *
     * @return array|\ApiBundle\Response\ViewInterface
     *
     */
    public function getInfluenceAction(Request $request, $id)
    {
        $isAuthorType = $request->request->get('isAuthorType', false);
        $groupByField = 'source_hashcode';
        if ($isAuthorType === true) {
            $groupByField = 'author_name';
        }

        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $queryId = [];
        $clipFeedId = [];
        foreach ($feeds as $feedsVal) {
            if ($feedsVal->getSubType() == 'query_feed') {
                $queryId[] = $feedsVal->getQuery()->getId();
            } else {
                $clipFeedId[] = $feedsVal->getId();
            }
        }
        $filters = $analyticContext->getFilters();
        $influenceData = [];
        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {

                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);

                foreach ($feeds as $key => $feedsVal) {
                    $influenceData[$key]['name'] = $feedsVal->getName();
                    $influenceData[$key]['data'] = [];
                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists($groupByField, $document['data'])) {
                                        $engagementCount = 0;
                                        if (array_key_exists("likes", $document['data'])) {
                                            $engagementCount = $document['data']['likes'];
                                        }
                                        if (array_key_exists("dislikes", $document['data'])) {
                                            $engagementCount += $document['data']['dislikes'];
                                        }
                                        if (array_key_exists("comments", $document['data'])) {
                                            $engagementCount += $document['data']['comments'];
                                        }
                                        if (array_key_exists("shares", $document['data'])) {
                                            $engagementCount += $document['data']['shares'];
                                        }
                                        $tempInfluenceData = $influenceData[$key]['data'];
                                        if (count($tempInfluenceData) > 0) {
                                            $sourceHashCodeKey = array_search($document['data'][$groupByField], array_column($tempInfluenceData, $groupByField));
                                            if ($sourceHashCodeKey === false) {
                                                $tempData = [$groupByField => $document['data'][$groupByField], 'influence' => $document['data']['source_link'],
                                                    'source_type' => $document['data']['source_publisher_type'], 'engagement' => $engagementCount, 'totalSentiment' => 0];
                                                if (array_key_exists("sentiment", $document['data'])) {
                                                    $sentiment = 1;
                                                    $tempData['totalSentiment'] = $sentiment;
                                                    $tempData[$document['data']['sentiment']] = $sentiment;
                                                }
                                                array_push($tempInfluenceData, $tempData);
                                                $influenceData[$key]['data'] = $tempInfluenceData;
                                            } else {
                                                if (array_key_exists("sentiment", $document['data'])) {
                                                    $influenceData[$key]['data'][$sourceHashCodeKey]['totalSentiment'] += 1;
                                                    if (array_key_exists($document['data']['sentiment'], $influenceData[$key]['data'][$sourceHashCodeKey])) {
                                                        $influenceData[$key]['data'][$sourceHashCodeKey][$document['data']['sentiment']] += 1;
                                                    } else {
                                                        $influenceData[$key]['data'][$sourceHashCodeKey][$document['data']['sentiment']] = 1;
                                                    }

                                                }
                                                $influenceData[$key]['data'][$sourceHashCodeKey]['engagement'] += $engagementCount;
                                            }
                                        } else {
                                            $tempData = [$groupByField => $document['data'][$groupByField], 'influence' => $document['data']['source_link'],
                                                'source_type' => $document['data']['source_publisher_type'], 'engagement' => $engagementCount, 'totalSentiment' => 0];
                                            if (array_key_exists("sentiment", $document['data'])) {
                                                $sentiment = 1;
                                                $tempData['totalSentiment'] = $sentiment;
                                                $tempData[$document['data']['sentiment']] = $sentiment;
                                            }
                                            $influenceData[$key]['data'][0] = $tempData;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach ($clipDocuments as $clipDocument) {
                        if ($feedsVal->getSubType() == 'clip_feed') {
                            if ($feedsVal->getId() == $clipDocument['clipFeedId']) {
                                $publishDate = substr($clipDocument['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists($groupByField, $clipDocument['data'])) {
                                        $engagementCount = 0;
                                        if (array_key_exists("likes", $clipDocument['data'])) {
                                            $engagementCount = $clipDocument['data']['likes'];
                                        }
                                        if (array_key_exists("dislikes", $clipDocument['data'])) {
                                            $engagementCount += $clipDocument['data']['dislikes'];
                                        }
                                        if (array_key_exists("comments", $clipDocument['data'])) {
                                            $engagementCount += $clipDocument['data']['comments'];
                                        }
                                        if (array_key_exists("shares", $clipDocument['data'])) {
                                            $engagementCount += $clipDocument['data']['shares'];
                                        }
                                        $tempInfluenceData = $influenceData[$key]['data'];
                                        if (count($tempInfluenceData) > 0) {
                                            $sourceHashCodeKey = array_search($clipDocument['data'][$groupByField], array_column($tempInfluenceData, $groupByField));
                                            if ($sourceHashCodeKey === false) {
                                                $tempData = [$groupByField => $clipDocument['data'][$groupByField], 'influence' => $clipDocument['data']['source_link'],
                                                    'source_type' => $clipDocument['data']['source_publisher_type'], 'engagement' => $engagementCount, 'totalSentiment' => 0];
                                                if (array_key_exists("sentiment", $clipDocument['data'])) {
                                                    $sentiment = 1;
                                                    $tempData['totalSentiment'] = $sentiment;
                                                    $tempData[$clipDocument['data']['sentiment']] = $sentiment;
                                                }
                                                array_push($tempInfluenceData, $tempData);
                                                $influenceData[$key]['data'] = $tempInfluenceData;
                                            } else {
                                                if (array_key_exists("sentiment", $clipDocument['data'])) {
                                                    $influenceData[$key]['data'][$sourceHashCodeKey]['totalSentiment'] += 1;
                                                    if (array_key_exists($clipDocument['data']['sentiment'], $influenceData[$key]['data'][$sourceHashCodeKey])) {
                                                        $influenceData[$key]['data'][$sourceHashCodeKey][$clipDocument['data']['sentiment']] += 1;
                                                    } else {
                                                        $influenceData[$key]['data'][$sourceHashCodeKey][$clipDocument['data']['sentiment']] = 1;
                                                    }
                                                }
                                                $influenceData[$key]['data'][$sourceHashCodeKey]['engagement'] += $engagementCount;
                                            }
                                        } else {

                                            $tempData = [$groupByField => $clipDocument['data'][$groupByField], 'influence' => $clipDocument['data']['source_link'],
                                                'source_type' => $clipDocument['data']['source_publisher_type'], 'engagement' => $engagementCount, 'totalSentiment' => 0];
                                            if (array_key_exists("sentiment", $clipDocument['data'])) {
                                                $sentiment = 1;
                                                $tempData['totalSentiment'] = $sentiment;
                                                $tempData[$clipDocument['data']['sentiment']] = $sentiment;
                                            }
                                            $influenceData[$key]['data'][0] = $tempData;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        foreach ($influenceData as $key => $influenceDataVal) {
            usort($influenceData[$key]['data'], function ($a, $b) {
                return $b['totalSentiment'] <=> $a['totalSentiment'];
            });
        }

        foreach ($influenceData as $key => $dataVal) {
            $influenceData[$key]['data'] = array_slice($dataVal['data'], 0, 10);
        }

        return $this->generateResponse([
            'data' => $influenceData
        ], 200, []);
    }


    /**
     * @param $filters
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDuration($filters)
    {
        $duration = [];
        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $period = new DatePeriod(
                    $filters['date']->getFilters()[0]->getValue(),
                    new DateInterval('P1D'),
                    $filters['date']->getFilters()[1]->getValue()
                );
                foreach ($period as $key => $value) {
                    $duration[$value->format('Y-m-d')] = 0;
                }
            }
        }

        return $duration;
    }
}
