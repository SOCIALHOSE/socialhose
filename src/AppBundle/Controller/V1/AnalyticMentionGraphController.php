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
 * Class AnalyticMentionGraphController
 * @package AppBundle\Controller\V1
 *
 * @Route(service="app.controller.analytic-mention-graph")
 */
class AnalyticMentionGraphController extends AbstractCRUDController
{
    /**
     * Get data for mention bar graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/mention-bar-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getMentionBarGraphAction($id)
    {
        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $data = [];
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

        $duration = $this->getDuration($filters);

        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$key]['name'] = $feedsVal->getName();
                    $data[$key]['data'] = $duration;
                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists($publishDate, $data[$key]['data'])) {
                                        $data[$key]['data'][$publishDate] += 1;
                                    } else {
                                        $data[$key]['data'][$publishDate] = 1;
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
                                    if (array_key_exists($publishDate, $data[$key]['data'])) {
                                        $data[$key]['data'][$publishDate] += 1;
                                    } else {
                                        $data[$key]['data'][$publishDate] = 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
        ], 200, []);
    }


    /**
     * Get data for mention pie graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/mention-pie-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getMentionPieGraphAction($id)
    {
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
        $data = [];
        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$feedsVal->getName()] = 0;
                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    $data[$feedsVal->getName()] += 1;
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
                                    $data[$feedsVal->getName()] += 1;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
        ], 200, []);
    }

    /**
     * Get data for mention pie graph according to type param
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/mention-over-time-pie-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param Request $request
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     */
    public function getMentionOverTimePieGraphAction(Request $request, $id)
    {
        if (!$request->request->has('type')) {
            return $this->generateResponse("Missing type parameter.", 404);
        } else {
            $type = $request->request->get('type');
            if (!isset($type) || trim($type) === '') {
                return $this->generateResponse("Invalid value for type parameter.", 404);
            } else {
                $defaultKey = [];
                switch ($type) {
                    case "sentiment":
                        $groupByField = 'sentiment';
                        $defaultKey = ['POSITIVE' => 0, 'NEUTRAL' => 0, 'NEGATIVE' => 0];
                        break;
                    case "language":
                        $groupByField = 'lang';
                        break;
                    case "country":
                        $groupByField = 'geo_country';
                        break;
                    case "media":
                        $groupByField = 'source_publisher_subtype';
                        break;
                    case "gender":
                        $groupByField = 'author_gender';
                        break;
                    default:
                        return $this->generateResponse("Invalid value for type parameter.", 404);
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
                $data = [];

                if (count($filters) > 0) {
                    if (array_key_exists('date', $filters)) {
                        $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                        $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                        $repository = $this->getManager()->getRepository(Document::class);
                        $documents = $repository->getByQuery($queryId);
                        $clipDocuments = $repository->getByClip($clipFeedId);

                        foreach ($feeds as $key => $feedsVal) {
                            $data[$feedsVal->getName()] = $defaultKey;
                            foreach ($documents as $document) {
                                if ($feedsVal->getSubType() == 'query_feed') {
                                    if ($feedsVal->getQuery()->getId() == $document['id']) {
                                        $publishDate = substr($document['data']['published'], 0, 10);
                                        $publishDate = date('Y-m-d', strtotime($publishDate));
                                        if (($publishDate >= $startDt) && ($publishDate <= $endDt) && array_key_exists($groupByField, $document['data'])) {
                                            if ($groupByField == 'author_gender' && $document['data'][$groupByField] == 'UNKNOWN') {
                                                continue;
                                            }
                                            if (array_key_exists($document['data'][$groupByField], $data[$feedsVal->getName()])) {
                                                $data[$feedsVal->getName()][$document['data'][$groupByField]] += 1;
                                            } else {
                                                $data[$feedsVal->getName()][$document['data'][$groupByField]] = 1;
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
                                        if (($publishDate >= $startDt) && ($publishDate <= $endDt) && array_key_exists($groupByField, $clipDocument['data'])) {
                                            if ($groupByField == 'author_gender' && $clipDocument['data'][$groupByField] == 'UNKNOWN') {
                                                continue;
                                            }
                                            if (array_key_exists($clipDocument['data'][$groupByField], $data[$feedsVal->getName()])) {
                                                $data[$feedsVal->getName()][$clipDocument['data'][$groupByField]] += 1;
                                            } else {
                                                $data[$feedsVal->getName()][$clipDocument['data'][$groupByField]] = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                if ($type == 'media') {
                    foreach ($data as $dataKey => $dataValue) {
                        foreach ($dataValue as $mediaKey => $mediaVal) {
                            $data[$dataKey][$this->determineMediaType($mediaKey)] = $mediaVal;
                            unset($data[$dataKey][$mediaKey]);
                        }

                    }
                }

                return $this->generateResponse([
                    'data' => $data,
                ], 200, []);

            }
        }
    }

    /**
     * Get data for mention bar graph according to type param
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/mention-over-time-bar-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param Request $request
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getMentionOverTimeBarGraphAction(Request $request, $id)
    {

        if (!$request->request->has('type')) {
            return $this->generateResponse("Missing type parameter.", 404);
        } else {
            $type = $request->request->get('type');
            if (!isset($type) || trim($type) === '') {
                return $this->generateResponse("Invalid value for type parameter.", 404);
            } else {
                $defaultKey = [];
                switch ($type) {
                    case "sentiment":
                        $groupByField = 'sentiment';
                        $defaultKey = ['POSITIVE' => 0, 'NEUTRAL' => 0, 'NEGATIVE' => 0];
                        break;
                    case "language":
                        $groupByField = 'lang';
                        break;
                    case "country":
                        $groupByField = 'geo_country';
                        break;
                    case "media":
                        $groupByField = 'source_publisher_subtype';
                        break;
                    default:
                        return $this->generateResponse("Invalid value for type parameter.", 404);
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
                $data = [];
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

                $duration = $this->getDuration($filters);
                foreach ($duration as $key => $value) {
                    $duration[$key] = $defaultKey;
                }
                if (count($filters) > 0) {
                    if (array_key_exists('date', $filters)) {
                        $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                        $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                        $repository = $this->getManager()->getRepository(Document::class);
                        $documents = $repository->getByQuery($queryId);
                        $clipDocuments = $repository->getByClip($clipFeedId);
                        foreach ($feeds as $key => $feedsVal) {
                            $data[$key]['name'] = $feedsVal->getName();
                            $data[$key]['data'] = $duration;

                            foreach ($documents as $document) {
                                if ($feedsVal->getSubType() == 'query_feed') {
                                    if ($feedsVal->getQuery()->getId() == $document['id']) {
                                        $publishDate = substr($document['data']['published'], 0, 10);
                                        $publishDate = date('Y-m-d', strtotime($publishDate));
                                        if (($publishDate >= $startDt) && ($publishDate <= $endDt) && array_key_exists($groupByField, $document['data'])) {
                                            if (array_key_exists($document['data'][$groupByField], $data[$key]['data'][$publishDate])) {
                                                $data[$key]['data'][$publishDate][$document['data'][$groupByField]] += 1;
                                            } else {
                                                $data[$key]['data'][$publishDate][$document['data'][$groupByField]] = 1;
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
                                        if (($publishDate >= $startDt) && ($publishDate <= $endDt) && array_key_exists($groupByField, $clipDocument['data'])) {
                                            if (array_key_exists($clipDocument['data'][$groupByField], $data[$key]['data'][$publishDate])) {
                                                $data[$key]['data'][$publishDate][$clipDocument['data'][$groupByField]] += 1;
                                            } else {
                                                $data[$key]['data'][$publishDate][$clipDocument['data'][$groupByField]] = 1;
                                            }

                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                if ($type == 'media') {
                    foreach ($data as $dataKey => $dataValue) {
                        foreach ($dataValue['data'] as $dateKey => $dateValue) {
                            foreach ($dateValue as $mediaKey => $mediaVal) {
                                $data[$dataKey]['data'][$dateKey][$this->determineMediaType($mediaKey)] = $mediaVal;
                                unset($data[$dataKey]['data'][$dateKey][$mediaKey]);
                            }
                        }
                    }
                }


                return $this->generateResponse([
                    'data' => $data,
                ], 200, []);
            }
        }
    }

    /**
     * Get data for engagement pie graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/engagement-over-time-pie-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param Request $request
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getEngagementOverTimePieGraphAction(Request $request, $id)
    {

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
        $data = [];
        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$feedsVal->getName()] = 0;
                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists("likes", $document['data'])) {
                                        $data[$feedsVal->getName()] += $document['data']['likes'];
                                    }
                                    if (array_key_exists("dislikes", $document['data'])) {
                                        $data[$feedsVal->getName()] += $document['data']['dislikes'];
                                    }
                                    if (array_key_exists("comments", $document['data'])) {
                                        $data[$feedsVal->getName()] += $document['data']['comments'];
                                    }
                                    if (array_key_exists("shares", $document['data'])) {
                                        $data[$feedsVal->getName()] += $document['data']['shares'];
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
                                    if (array_key_exists("likes", $clipDocument['data'])) {
                                        $data[$feedsVal->getName()] += $clipDocument['data']['likes'];
                                    }
                                    if (array_key_exists("dislikes", $clipDocument['data'])) {
                                        $data[$feedsVal->getName()] += $clipDocument['data']['dislikes'];
                                    }
                                    if (array_key_exists("comments", $clipDocument['data'])) {
                                        $data[$feedsVal->getName()] += $clipDocument['data']['comments'];
                                    }
                                    if (array_key_exists("shares", $clipDocument['data'])) {
                                        $data[$feedsVal->getName()] += $clipDocument['data']['shares'];
                                    }

                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
        ], 200, []);
    }

    /**
     * Get data for engagement bar graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/engagement-over-time-bar-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getEngagementOverTimeBarGraphAction($id)
    {
        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $data = [];
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

        $duration = $this->getDuration($filters);

        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$key]['name'] = $feedsVal->getName();
                    $data[$key]['data'] = $duration;
                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists("likes", $document['data'])) {
                                        $data[$key]['data'][$publishDate] += $document['data']['likes'];
                                    }
                                    if (array_key_exists("dislikes", $document['data'])) {
                                        $data[$key]['data'][$publishDate] += $document['data']['dislikes'];
                                    }
                                    if (array_key_exists("comments", $document['data'])) {
                                        $data[$key]['data'][$publishDate] += $document['data']['comments'];
                                    }
                                    if (array_key_exists("shares", $document['data'])) {
                                        $data[$key]['data'][$publishDate] += $document['data']['shares'];
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
                                    if (array_key_exists("likes", $clipDocument['data'])) {
                                        $data[$key]['data'][$publishDate] += $clipDocument['data']['likes'];
                                    }
                                    if (array_key_exists("dislikes", $clipDocument['data'])) {
                                        $data[$key]['data'][$publishDate] += $clipDocument['data']['dislikes'];
                                    }
                                    if (array_key_exists("comments", $clipDocument['data'])) {
                                        $data[$key]['data'][$publishDate] += $clipDocument['data']['comments'];
                                    }
                                    if (array_key_exists("shares", $clipDocument['data'])) {
                                        $data[$key]['data'][$publishDate] += $clipDocument['data']['shares'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
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

    /**
     * Get data for theme bar graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/theme-over-time-bar-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getThemeOverTimeBarGraphAction($id)
    {

        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $data = [];
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

        $duration = $this->getDuration($filters);

        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$key]['name'] = $feedsVal->getName();
                    $data[$key]['data'] = [];

                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists("tags", $document['data'])) {
                                        foreach ($document['data']['tags'] as $val) {
                                            $tempTagData = $data[$key]['data'];
                                            if (count($tempTagData) > 0) {

                                                $tagKey = array_search($val, array_column($tempTagData, 'name'));
                                                if ($tagKey === false) {
                                                    $tempTagDataCount = count($tempTagData);
                                                    $tempTagData[$tempTagDataCount]['name'] = $val;
                                                    $tempTagData[$tempTagDataCount]['data'] = $duration;
                                                    $tempTagData[$tempTagDataCount]['data'][$publishDate] = 1;

                                                } else {
                                                    $tempTagData[$tagKey]['data'][$publishDate] += 1;
                                                }
                                            } else {
                                                $tempTagDataCount = count($tempTagData);
                                                $tempTagData[$tempTagDataCount]['name'] = $val;
                                                $tempTagData[$tempTagDataCount]['data'] = $duration;
                                                $tempTagData[$tempTagDataCount]['data'][$publishDate] = 1;
                                            }
                                            $data[$key]['data'] = $tempTagData;
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
                                    if (array_key_exists("tags", $clipDocument['data'])) {
                                        foreach ($clipDocument['data']['tags'] as $val) {
                                            $tempTagData = $data[$key]['data'];
                                            if (count($tempTagData) > 0) {
                                                $tagKey = array_search($val, array_column($tempTagData, 'name'));
                                                if ($tagKey === false) {
                                                    $tempTagDataCount = count($tempTagData);
                                                    $tempTagData[$tempTagDataCount]['name'] = $val;
                                                    $tempTagData[$tempTagDataCount]['data'] = $duration;
                                                    $tempTagData[$tempTagDataCount]['data'][$publishDate] = 1;
                                                } else {
                                                    $tempTagData[$tagKey]['data'][$publishDate] += 1;
                                                }
                                            } else {
                                                $tempTagDataCount = count($tempTagData);
                                                $tempTagData[$tempTagDataCount]['name'] = $val;
                                                $tempTagData[$tempTagDataCount]['data'] = $duration;
                                                $tempTagData[$tempTagDataCount]['data'][$publishDate] = 1;
                                            }
                                            $data[$key]['data'] = $tempTagData;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
        ], 200, []);
    }


    /**
     * Get data for theme bar graph
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/theme-over-time-pie-graph/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getThemeOverTimePieGraphAction($id)
    {
        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $data = [];
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

        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$key]['name'] = $feedsVal->getName();
                    $data[$key]['data'] = [];

                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists("tags", $document['data'])) {
                                        foreach ($document['data']['tags'] as $val) {
                                            $tempTagData = $data[$key]['data'];
                                            if (count($tempTagData) > 0) {
                                                if (array_key_exists($val, $tempTagData)) {
                                                    $tempTagData[$val] += 1;
                                                } else {
                                                    $tempTagData[$val] = 1;
                                                }
                                            } else {
                                                $tempTagData[$val] = 1;
                                            }
                                            $data[$key]['data'] = $tempTagData;
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
                                    if (array_key_exists("tags", $clipDocument['data'])) {
                                        foreach ($clipDocument['data']['tags'] as $val) {
                                            $tempTagData = $data[$key]['data'];
                                            if (count($tempTagData) > 0) {

                                                if (array_key_exists($val, $tempTagData)) {
                                                    $tempTagData[$val] += 1;
                                                } else {
                                                    $tempTagData[$val] = 1;
                                                }
                                            } else {
                                                $tempTagData[$val] = 1;
                                            }
                                            $data[$key]['data'] = $tempTagData;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data,
        ], 200, []);
    }

    /**
     * Get data for world map
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/world-map/{id}",
     *     requirements={
     *      "id": "\d+",
     *     },
     *     methods={ "POST" }
     * )
     * @param $id
     *
     * @return \ApiBundle\Response\ViewInterface
     *
     * @throws \Exception
     */
    public function getWorldMapAction($id)
    {
        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $analyticContext = $analytic->getContext();
        $feeds = $analyticContext->getFeeds();
        $data = [];
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
        if (count($filters) > 0) {
            if (array_key_exists('date', $filters)) {
                $startDt = $filters['date']->getFilters()[0]->getValue()->format('Y-m-d');
                $endDt = $filters['date']->getFilters()[1]->getValue()->format('Y-m-d');
                $repository = $this->getManager()->getRepository(Document::class);
                $documents = $repository->getByQuery($queryId);
                $clipDocuments = $repository->getByClip($clipFeedId);
                foreach ($feeds as $key => $feedsVal) {
                    $data[$key]['name'] = $feedsVal->getName();
                    $data[$key]['data'] = [];

                    foreach ($documents as $document) {
                        if ($feedsVal->getSubType() == 'query_feed') {
                            if ($feedsVal->getQuery()->getId() == $document['id']) {
                                $publishDate = substr($document['data']['published'], 0, 10);
                                $publishDate = date('Y-m-d', strtotime($publishDate));
                                if (($publishDate >= $startDt) && ($publishDate <= $endDt)) {
                                    if (array_key_exists("sentiment", $document['data']) && array_key_exists("geo_country", $document['data'])
                                        && array_key_exists("geo_point", $document['data'])) {
                                        $tempData = $data[$key]['data'];
                                        $mapDataKey = array_search($document['data']['geo_country'], array_column($tempData, 'name'));
                                        if ($mapDataKey === false) {
                                            $count = count($tempData);
                                            $tempData[$count]['name'] = $document['data']['geo_country'];
                                            $tempData[$count]['POSITIVE'] = 0;
                                            $tempData[$count]['NEUTRAL'] = 0;
                                            $tempData[$count]['NEGATIVE'] = 0;
                                            $tempData[$count][$document['data']['sentiment']] += 1;
                                            $tempData[$count]['LatLng'] = $document['data']['geo_point'];
                                        } else {
                                            $tempData[$mapDataKey][$document['data']['sentiment']] += 1;
                                        }
                                        $data[$key]['data'] = $tempData;
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
                                    if (array_key_exists("sentiment", $clipDocument['data']) && array_key_exists("geo_country", $clipDocument['data'])
                                        && array_key_exists("geo_point", $clipDocument['data'])) {
                                        $tempData = $data[$key]['data'];
                                        $mapDataKey = array_search($clipDocument['data']['geo_country'], array_column($tempData, 'name'));
                                        if ($mapDataKey === false) {
                                            $count = count($tempData);
                                            $tempData[$count]['name'] = $clipDocument['data']['geo_country'];
                                            $tempData[$count]['POSITIVE'] = 0;
                                            $tempData[$count]['NEUTRAL'] = 0;
                                            $tempData[$count]['NEGATIVE'] = 0;
                                            $tempData[$count][$clipDocument['data']['sentiment']] += 1;
                                            $tempData[$count]['LatLng'] = $clipDocument['data']['geo_point'];
                                        } else {
                                            $tempData[$mapDataKey][$clipDocument['data']['sentiment']] += 1;
                                        }
                                        $data[$key]['data'] = $tempData;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->generateResponse([
            'data' => $data
        ], 200, []);
    }

    /**
     * @param $type
     * @return mixed
     */
    public function determineMediaType($type)
    {
        $mediaType = ['WEBLOG' => 'Blogs', 'MAINSTREAM_NEWS' => 'News', 'reddit' => 'Reddit', 'twitter' => 'Twitter', 'instagram' => 'Instagram'];
        if (array_key_exists($type, $mediaType)) {
            return $mediaType[$type];
        }

        return $type;
    }
}
