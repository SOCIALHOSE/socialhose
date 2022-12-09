<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Security\AccessChecker\AccessCheckerInterface;
use ApiBundle\Security\Inspector\InspectorInterface;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Exception\LimitExceedException;
use AppBundle\Form\FeedDocumentSearchType;
use AppBundle\Form\FeedType;
use AppBundle\Manager\Feed\FeedManagerInterface;
use AppBundle\Manager\StoredQuery\StoredQueryManagerInterface;
use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Category;
use CacheBundle\Entity\Document;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Feed\Fetcher\Factory\FeedFetcherFactoryInterface;
use CacheBundle\Repository\CategoryRepository;
use CacheBundle\Repository\ClipFeedRepository;
use CacheBundle\Repository\CommonFeedRepository;
use CacheBundle\Repository\DocumentRepository;
use Common\Enum\CollectionTypeEnum;
use Common\Enum\FieldNameEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Filter\Filters\AndFilter;
use IndexBundle\Filter\Filters\GteFilter;
use IndexBundle\Filter\Filters\LteFilter;
use IndexBundle\Filter\Filters\NotFilter;
use IndexBundle\Filter\GroupFilterInterface;
use IndexBundle\Filter\SingleFilterInterface;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\RecentlyUsedFeed;
use UserBundle\Entity\User;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;
use UserBundle\Repository\RecentlyUsedFeedRepository;

/**
 * Class FeedController
 * @package AppBundle\Controller\V1
 *
 * @Route("/feed", service="app.controller.feed")
 */
class FeedController extends AbstractV1CrudController
{

    use
        TokenStorageAwareTrait;

    /**
     * @var FeedManagerInterface
     */
    private $feedManager;

    /**
     * @var DocumentContentExtractorInterface
     */
    private $documentExtractor;

    /**
     * @var FeedFetcherFactoryInterface
     */
    private $feedFetcherFactory;

    /**
     * @var HttpKernelInterface
     */
    private $kernel;

    /**
     * @var StoredQueryManagerInterface
     */
    private $storedQueryManager;

    /**
     * @var ProducerInterface
     */
    private $fetchProducer;

    /** @var integer */
    private $feedDocumentLimit;

    /**
     * FeedController constructor.
     *
     * @param FormFactoryInterface              $formFactory        A FormFactoryInterface
     *                                                              instance.
     * @param AccessCheckerInterface            $accessChecker      A AccessCheckerInterface
     *                                                              instance.
     * @param EntityManagerInterface            $em                 A EntityManagerInterface
     *                                                              instance.
     * @param string                            $entity             Used entity name.
     * @param TokenStorageInterface             $tokenStorage       A TokenStorageInterface
     *                                                              instance.
     * @param FeedManagerInterface              $feedManager        A FeedManagerInterface
     *                                                              instance.
     * @param DocumentContentExtractorInterface $documentExtractor  A DocumentContentExtractorInterface
     *                                                              instance.
     * @param FeedFetcherFactoryInterface       $feedFetcherFactory A FeedFetcherFactoryInterface
     *                                                              instance.
     * @param HttpKernelInterface               $kernel             A HttpKernelInterface
     *                                                              instance.
     * @param StoredQueryManagerInterface       $storedQueryManager A StoredQueryManagerInterface
     *                                                              instance.
     * @param ProducerInterface                 $fetchProducer      A ProducerInterface
     *                                                              instance.
     * @param string                            $feedDocumentLimit
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        AccessCheckerInterface $accessChecker,
        EntityManagerInterface $em,
        $entity,
        TokenStorageInterface $tokenStorage,
        FeedManagerInterface $feedManager,
        DocumentContentExtractorInterface $documentExtractor,
        FeedFetcherFactoryInterface $feedFetcherFactory,
        HttpKernelInterface $kernel,
        StoredQueryManagerInterface $storedQueryManager,
        ProducerInterface $fetchProducer,
        $feedDocumentLimit
    ) {
        parent::__construct($formFactory, $accessChecker, $em, $entity);
        $this->tokenStorage = $tokenStorage;
        $this->feedManager = $feedManager;
        $this->documentExtractor = $documentExtractor;
        $this->feedFetcherFactory = $feedFetcherFactory;
        $this->kernel = $kernel;
        $this->storedQueryManager = $storedQueryManager;
        $this->fetchProducer = $fetchProducer;
        $this->feedDocumentLimit = $feedDocumentLimit;
    }

    /**
     * Create feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("", methods={ "POST" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  input={
     *      "class"="AppBundle\Form\FeedType",
     *      "name"=false
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Feed\AbstractFeed",
     *      "groups"={ "feed", "id" }
     *  },
     *  statusCodes={
     *     200="Feed successfully saved.",
     *     400="Invalid data provided."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(FeedType::class)
            ->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \CacheBundle\Entity\Feed\QueryFeed $feed */
            $feed = $form->get('feed')->getData();
            /** @var SearchRequestBuilderInterface $builder */
            $builder = $form->get('search')->getData();

            $user = $this->getCurrentUser();
            $exceptionLimitResponse = $this->generateResponse([
                'failedRestriction' => AppLimitEnum::FEEDS,
                'restrictions' => $user->getRestrictions(),
            ], 402);

            //
            //Check that feed contains < $this->feedDocumentLimit records
            //
            try {
                $user->checkLimit(AppLimitEnum::feeds());
            } catch (LimitExceedException $exception) {
                return $exceptionLimitResponse;
            }
            if (!$feed instanceof ClipFeed) {
                $searchData = $request->request->get('search');
                $total = $this->storedQueryManager->getTotal(
                    $builder,
                    isset($searchData['filters']) ? $searchData['filters'] : [],
                    isset($searchData['advancedFilters']) ? $searchData['advancedFilters'] : []
                );
                if ($total > $this->feedDocumentLimit) {
                    return  $this->generateResponse(['Your request is too broad. Please narrow it down.'], 401);
                }
            }



            try {
                $user->useLimit(AppLimitEnum::feeds());
            } catch (LimitExceedException $exception) {
                return $exceptionLimitResponse;
            }

            $this->em->persist($user);

            $feed->setUser($this->getCurrentUser());

            if ($feed instanceof ClipFeed) {
                $builder->getFilters();
                $feed->setFilters($builder->build()->getFilters());

                $this->em->persist($feed);
                $this->em->flush();
            } else {
                $this->createQueryFeed($request, $builder, $feed);
            }

            if (count($feed->getExcludedDocuments()) > 0) {
                //
                // We may already have documents so we should remove it here.
                //
                $this->feedManager->deleteDocuments($feed, \nspl\a\map(
                    \nspl\op\methodCaller('getSequence'),
                    $feed->getExcludedDocuments()
                ));
            }

            return $this->generateResponse($feed, 200, [
                'feed',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Rename feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/rename", methods={ "PUT" }, requirements={ "id":"\d+" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  input={
     *      "class"="",
     *      "data"={
     *          "name"={
     *              "dataType"="string",
     *              "description"="new feed name",
     *              "required"=true,
     *              "readonly"=false
     *          }
     *      }
     *  },
     *  statusCodes={
     *     204="Feed successfully renamed.",
     *     400="Invalid data provided."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Feed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function renameAction(Request $request, $id)
    {
        /** @var User $user */
        $user = $this->getCurrentUser();

        $newName = trim($request->request->get('name'));
        if ($newName === '') {
            return $this->generateResponse('Name not provided.', 400);
        }

        /** @var CommonFeedRepository $feedRepository */
        $feedRepository = $this->em->getRepository(AbstractFeed::class);

        $feed = $feedRepository->getOne($id, $user->getId());
        if (! $feed instanceof AbstractFeed) {
            return $this->generateResponse("Can't find feed with id {$id}.", 404);
        }

        $feed->setName($newName);
        $this->em->persist($feed);
        $this->em->flush();

        return $this->generateResponse();
    }


    /**
     * Update feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}", methods={ "PUT" }, requirements={ "id"="\d+" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  input={
     *      "class"="AppBundle\Form\FeedType",
     *      "name"=false
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Feed\AbstractFeed",
     *      "groups"={ "feed", "id" }
     *  },
     *  statusCodes={
     *     200="Feed successfully updated.",
     *     400="Invalid data provided."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Feed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $id)
    {
        /** @var User $user */
        $user = $this->getCurrentUser();
        /** @var CommonFeedRepository $feedRepository */
        $feedRepository = $this->em->getRepository(AbstractFeed::class);

        $feed = $feedRepository->getOne($id, $user->getId());
        if (! $feed instanceof AbstractFeed) {
            return $this->generateResponse("Can't find feed with id {$id}.", 404);
        }

        $form = $this->createForm(FeedType::class, [
            'feed' => $feed,
        ]);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            /** @var \CacheBundle\Entity\Feed\QueryFeed $feed */
            $feed = $form->get('feed')->getData();
            /** @var SearchRequestBuilderInterface $builder */
            $builder = $form->get('search')->getData();

            if ($feed instanceof ClipFeed) {
                $builder->getFilters();
                $feed->setFilters($builder->build()->getFilters());

                $this->em->persist($feed);
                $this->em->flush();
            } else {
                $this->createQueryFeed($request, $builder, $feed);
            }

            return $this->generateResponse($feed, 200, [
                'feed',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Get documents for specified feeds.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/documents", methods={ "POST" }, requirements={ "id": "\d+" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Documents",
     *  input={
     *      "class"="AppBundle\Form\FeedDocumentSearchType",
     *      "name"=false
     *  },
     *  statusCodes={
     *     200="Stored query successfully saved.",
     *     404="Invalid feed id."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A AbstractFeed id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function documentsAction(Request $request, $id)
    {
        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);
        $feed = $repository->getOne($id, \app\op\invokeIf($this->getCurrentUser(), 'getId'));

        if ($feed === null) {
            return $this->generateResponse([
                'message' => 'Feed not found',
                'transKey' => 'getFeedDocumentsInvalidFeed',
                'type' => 'error',
                'parameters' => [ 'current' => $id ],
            ], 404);
        }

        $form = $this->createForm(FeedDocumentSearchType::class)
            ->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SearchRequestBuilderInterface $builder */
            $builder = $form->getData();

            /** @var FeedFetcherFactoryInterface $factory */
            $result = $this->feedFetcherFactory->get(get_class($feed))->fetch($feed, $builder);

            $response = $result->getResponse();

            $query = '';
            if ($feed instanceof QueryFeed) {
                $query = $feed->getQuery()->getRaw();
            }

            $response->mapDocuments(function (ArticleDocumentInterface $document) use ($query) {
                return $document->mapNormalizedData(function (array $data) use ($query) {
                    $result = $this->documentExtractor->extract(
                        $data['content'],
                        $query,
                        ThemeOptionExtractEnum::start(),
                        true
                    );

                    $data['content'] = $result->getText() . (
                        mb_strlen($data['content']) > $result->getLength()
                            ? '...'
                            : ''
                        );

                    return $data;
                });
            });

            return $this->generateResponse([
                'feed' => $id,
                'meta' => $result->getMeta($request),
                'documents' => $this->paginate($response, $builder->getPage(), $builder->getLimit()),
                'advancedFilters' => $result->getAdvancedFilters(),
            ], 200, [
                'comment',
                'document',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Clip documents into specified clip feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/documents/clip", methods={ "POST" }, requirements={ "id": "\d+" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Documents",
     *  input={
     *     "class"="",
     *     "data"={
     *      "ids"={
     *       "dataType"="Array of document ids",
     *       "actualType"="collection",
     *       "subtype"="string",
     *       "required"=true,
     *       "readonly"=true
     *      }
     *     }
     *  },
     *  statusCodes={
     *     204="Document successfully clipped.",
     *     400="Invalid document ids.",
     *     404="Invalid feed id."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A ClipFeed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function clipAction(Request $request, $id)
    {
        $ids = $request->request->get('ids', []);
        $currentUserId = \app\op\invokeIf($this->getCurrentUser(), 'getId');

        if (count($ids) <= 0) {
            return $this->generateResponse([
                'message' => 'ids: Should not be blank',
                'transKey' => 'clipDocumentsIdsBlank',
                'type' => 'error',
                'parameters' => [ 'current' => $ids ],
            ], 400);
        }

        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);
        $feed = $repository->getOne($id, $currentUserId);

        if (! $feed instanceof ClipFeed) {
            return $this->generateResponse([
                'message' => 'Feed not found',
                'transKey' => 'clipDocumentsInvalidFeed',
                'type' => 'error',
                'parameters' => [ 'current' => $id ],
            ], 404);
        }

        /** @var DocumentRepository $repository */
        $repository = $this->em->getRepository(Document::class);
        $notExists = $repository->checkIds($ids);

        if (count($notExists) > 0) {
            return $this->generateResponse([
                'message' => 'Invalid documents',
                'transKey' => 'clipDocumentsInvalidDocuments',
                'type' => 'error',
                'parameters' => [ 'current' => $ids, 'unknown' => $notExists ],
            ], 400);
        }

        $ids = $repository->sanitizeIds($feed->getId(), CollectionTypeEnum::FEED, $ids);

        //
        // Add documents to clip feed.
        //
        $this->feedManager->clip($feed, $ids);

        /** @var RecentlyUsedFeedRepository $repository */
        $repository = $this->em->getRepository(RecentlyUsedFeed::class);
        $recentlyUsed = $repository->getAlreadyUsed($currentUserId, $feed->getId());

        if ($recentlyUsed instanceof RecentlyUsedFeed) {
            $recentlyUsed->setUsedAt(new \DateTime());
            $this->em->persist($recentlyUsed);
            $this->em->flush();
        } else {
            $repository->addRecentlyUsedFor($this->getCurrentUser(), $feed);
        }

        return $this->generateResponse();
    }

    /**
     * Clip documents into specified clip feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/readLater/{documentId}", methods={ "POST" }, requirements={ "documentId": "\d+" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Documents",
     *  statusCodes={
     *     204="Document successfully clipped.",
     *     400="Invalid document id."
     *  }
     * )
     *
     * @param integer $documentId A Document entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function readLaterAction($documentId)
    {
        /** @var ClipFeedRepository $repository */
        $repository = $this->em->getRepository(ClipFeed::class);
        $user = $this->getCurrentUser();

        $feed = $repository->getReadLater($user->getId());

        if (! $feed instanceof ClipFeed) {
            try {
                $user->useLimit(AppLimitEnum::feeds());
            } catch (LimitExceedException $exception) {
                return $this->generateResponse([
                    'failedRestriction' => AppLimitEnum::FEEDS,
                    'restrictions' => $user->getRestrictions(),
                ], 402);
            }

            $this->em->persist($user);
            $this->em->flush();

            $feed = $repository->createReadLater($user->getId());
        }

        /** @var DocumentRepository $repository */
        $repository = $this->em->getRepository(Document::class);
        $notExists = $repository->checkIds([ $documentId ]);

        if (count($notExists) > 0) {
            return $this->generateResponse([
                'message' => 'Invalid document',
                'transKey' => 'readLaterDocumentInvalidDocument',
                'type' => 'error',
                'parameters' => [ 'current' => $documentId ],
            ], 400);
        }

        $ids = $repository->sanitizeIds(
            $feed->getId(),
            CollectionTypeEnum::FEED,
            [ $documentId ]
        );

        //
        // Add documents to clip feed.
        //
        $this->feedManager->clip($feed, $ids);

        return $this->generateResponse();
    }

    /**
     * Get recently used feeds for clipping.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/recentClip",
     *     methods={ "GET" }
     * )
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  output={
     *     "class"="Pagination<CacheBundle\Entity\Category>",
     *     "groups"={ "id", "category_tree", "feed_tree" }
     *  },
     *  statusCodes={
     *     200="List of recent feeds used for clipping successfully returned."
     *  }
     * )
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function recentClipFeedAction()
    {
        /** @var RecentlyUsedFeedRepository $repository */
        $repository = $this->em->getRepository(RecentlyUsedFeed::class);
        $recentlyUsedFeeds = $repository->getRecentlyUsedFor(\app\op\invokeIf($this->getCurrentUser(), 'getId'));

        return $this->generateResponse($recentlyUsedFeeds, 200, [
            'id',
            'feed',
        ]);
    }

    /**
     * Move specified feed to another category.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{feedId}/move_to/{categoryId}",
     *     requirements={
     *      "feedId": "\d+",
     *      "categoryId": "\d+"
     *     },
     *     methods={ "POST" }
     * )
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  output={
     *     "class"="Pagination<CacheBundle\Entity\Category>",
     *     "groups"={ "id", "category_tree", "feed_tree" }
     *  },
     *  statusCodes={
     *     200="List of updated categories successfully returned."
     *  }
     * )
     *
     * @param Request $request    A http Request instance.
     * @param integer $feedId     A moving Feed entity id.
     * @param integer $categoryId A Category entity id where the feed is moved.
     *
     * @return \ApiBundle\Response\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function moveAction(Request $request, $feedId, $categoryId)
    {
        $feedId = (integer) $feedId;
        $categoryId = (integer) $categoryId;
        $userId = \app\op\invokeIf($this->getCurrentUser(), 'getId');

        /** @var CommonFeedRepository $feedRepository */
        $feedRepository = $this->em->getRepository(AbstractFeed::class);
        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);

        $feed = $feedRepository->getOne($feedId, $userId);
        if (! $feed instanceof AbstractFeed) {
            return $this->generateResponse("Can't find feed with id {$feedId}.", 404);
        }

        //
        // We should don't make any changes if client try to move feed into the
        // same category.
        //

        if ($feed->getCategory()->getId() !== $categoryId) {
            $category = $categoryRepository->get($categoryId, $userId, [
                Category::TYPE_CUSTOM,
                Category::TYPE_MY_CONTENT,
            ]);
            if (! $category instanceof Category) {
                return $this->generateResponse("Can't find category with id {$categoryId}.", 404);
            }

            //
            // All ok, we had valid feed and category entity so we just update
            // feed.
            //

            $feed->setCategory($category);

            $this->em->persist($feed);
            $this->em->flush();
        }

        $path['_forwarded'] = $request->attributes;
        $path['_controller'] = 'app.controller.category:listAction';
        $subRequest = $request->duplicate([], null, []);

        return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Get information about feed by id.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{id}",
     *     requirements={ "id": "\d+" },
     *     methods={ "GET" }
     * )
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  output={
     *      "class"="CacheBundle\Entity\Feed\AbstractFeed",
     *      "groups"={ "feed", "id" }
     *  },
     *  statusCodes={
     *     200="Feed successfully returned.",
     *     403="You don't have permissions to view this feed.",
     *     404="Can't find feed by specified id."
     *  }
     * )
     *
     * @param integer $id A one of feed entity id.
     *
     * @return \CacheBundle\Entity\Feed\AbstractFeed|\ApiBundle\Response\ViewInterface
     */
    public function getAction($id)
    {
        return parent::getEntity($id);
    }

    /**
     * Delete specified feed by id.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{id}",
     *     requirements={ "id": "\d+" },
     *     methods={ "DELETE" }
     * )
     * @AppApiDoc(
     *  section="Feed",
     *  resource=true,
     *  statusCodes={
     *     204="Feed successfully deleted.",
     *     403="You don't have permissions to delete this feed.",
     *     404="Can't find feed by specified id."
     *  }
     * )
     *
     * @param integer $id A one of feed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteAction($id)
    {
        $repository = $this->em->getRepository($this->entity);

        //
        // Get entity by id and check whether it exists or not.
        // Send proper error message if not.
        //
        $entity = $repository->find($id);
        if (! $entity instanceof AbstractFeed) {
            $name = \app\c\getShortName($this->entity);
            // Remove 'Abstract' prefix if it exists.
            if (strpos($name, 'Abstract') !== false) {
                $name = substr($name, 8);
            }

            return $this->generateResponse("Can't find {$name} with id {$id}.", 404);
        }
        //
        // Check that current user can delete this entity.
        // If user don't have rights to delete this entity we should send all
        // founded restrictions to client.
        //
        $reasons = $this->checkAccess(InspectorInterface::DELETE, $entity);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        //
        // Remove deleted feed from recently used.
        //
        /** @var RecentlyUsedFeedRepository $repository */
        $repository = $this->em->getRepository(RecentlyUsedFeed::class);
        $repository->removeForFeed($entity->getId());

        //
        // Remove associations between this feed and all documents which it has.
        // We should remove it before deleting entities 'cause after that id of
        // entity will be null.
        //
        $this->feedManager->deleteDocuments($entity);
        $user = $this->getCurrentUser();
        $user->releaseLimit(AppLimitEnum::feeds());

        $this->em->remove($entity);
        $this->em->persist($user);
        $this->em->flush();

        return $this->generateResponse();
    }

    /**
     * Delete document from feed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{id}/documents/delete",
     *     requirements={ "id": "\d+" },
     *     methods={ "POST" }
     * )
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Documents",
     *  input={
     *     "class"="",
     *     "data"={
     *      "ids"={
     *       "dataType"="Array of document ids",
     *       "actualType"="collection",
     *       "subtype"="string",
     *       "required"=true,
     *       "readonly"=true
     *      }
     *     }
     *  },
     *  statusCodes={
     *     204="Documents from feeds successfully deleted.",
     *     400="Invalid data."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param string  $id      A Feed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteDocumentsAction(Request $request, $id)
    {
        $ids = $request->request->get('ids', []);

        if (count($ids) <= 0) {
            return $this->generateResponse([
                'message' => 'ids: Should not be blank',
                'transKey' => 'deleteDocumentsIdsBlank',
                'type' => 'error',
                'parameters' => [ 'current' => $ids ],
            ], 400);
        }

        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);
        $feed = $repository->getOne($id, \app\op\invokeIf($this->getCurrentUser(), 'getId'));

        if (! $feed instanceof AbstractFeed) {
            return $this->generateResponse([
                'message' => 'Feed not found',
                'transKey' => 'deleteDocumentsInvalidFeed',
                'type' => 'error',
                'parameters' => [ 'current' => $id ],
            ], 404);
        }

        /** @var DocumentRepository $repository */
        $repository = $this->em->getRepository(Document::class);
        $notExists = $repository->checkIds($ids);

        if (count($notExists) > 0) {
            return $this->generateResponse([
                'message' => 'Invalid documents',
                'transKey' => 'deleteDocumentsInvalidDocuments',
                'type' => 'error',
                'parameters' => [ 'current' => $ids, 'unknown' => $notExists ],
            ], 400);
        }

        $this->feedManager->deleteDocuments($feed, $ids);

        return $this->generateResponse();
    }

    /**
     * Export feed
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/toggleExport", methods={ "PUT" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Feed",
     *  input={
     *     "class"="",
     *     "data"={
     *      "export"={
     *       "dataType"="Exported boolean status",
     *       "actualType"="boolean",
     *       "subtype"="integer",
     *       "required"=true,
     *       "readonly"=true
     *      }
     *     }
     *  },
     *  statusCodes={
     *     204="Feeds is exported or not exported.",
     *     400="Invalid data."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param string  $id      A Feed entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function toggleExportAction(Request $request, $id)
    {
        $exported = $request->request->get('export');
        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);
        $feed = $repository->find($id);
        if (! $feed instanceof AbstractFeed) {
            return $this->generateResponse([
                'message' => 'Feed not found',
                'transKey' => 'deleteDocumentsInvalidFeed',
                'type' => 'error',
                'parameters' => [ 'current' => $id ],
            ], 404);
        }
        $feed->setExported($exported);
        $this->em->flush($feed);

        return $this->generateResponse();
    }

    /**
     * Export Feeds in specified category
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/toggleExport/{category}", methods={ "PUT" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Feed",
     *  input={
     *     "class"="",
     *     "data"={
     *      "export"={
     *       "dataType"="Exported boolean status",
     *       "actualType"="boolean",
     *       "subtype"="integer",
     *       "required"=true,
     *       "readonly"=true
     *      }
     *     }
     *  },
     *  statusCodes={
     *     204="Feeds is exported or not exported.",
     *     400="Invalid data."
     *  }
     * )
     * @param Request $request  A http Request instance.
     * @param string  $category A Category entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function toggleExportInCategoryAction(Request $request, $category)
    {
        $exported = $request->request->getBoolean('export');
        /** @var CategoryRepository $repository */
        $repository = $this->em->getRepository(Category::class);
        $category = $repository->find($category);
        if (! $category instanceof Category) {
            return $this->generateResponse([
                'message' => 'Category not found',
                'transKey' => 'toggleExportsInCategoryInvalidCategory',
                'type' => 'error',
                'parameters' => [ 'current' => $category ],
            ], 404);
        }

        $category->setExported($exported);
        $repository->exportFeedsIn($category->getId(), $exported);

        $this->em->persist($category);
        $this->em->flush();

        return $this->generateResponse();
    }

    /**
     * Exported Feeds.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/exported", methods={ "GET" })
     * @AppApiDoc(
     *  section="Feed",
     *  resource="Documents",
     *  statusCodes={
     *     204="Exported feeds.",
     *     400="Invalid data."
     *  }
     * )
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function exportedAction()
    {
        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);
        $feed = $repository->findBy(
            ['exported' => true]
        );

        return $this->generateResponse($feed, 200, [
            'id',
            'feed',
        ]);
    }

    /**
     * @param Request                       $request A HTTP Request instance.
     * @param SearchRequestBuilderInterface $builder A SearchRequestBuilderInterface
     *                                               instance.
     * @param QueryFeed                     $feed    A QueryFeed entity instance.
     *
     * @return \ApiBundle\Response\ViewInterface|null
     */
    private function createQueryFeed(
        Request $request,
        SearchRequestBuilderInterface $builder,
        QueryFeed $feed
    ) {
        $searchData = $request->request->get('search');

        $query = $this->storedQueryManager->createQuery(
            $builder,
            isset($searchData['filters']) ? $searchData['filters'] : [],
            isset($searchData['advancedFilters']) ? $searchData['advancedFilters'] : []
        );
        $isNewQuery = $query->getId() === null;

        $filter = $this->getPublisherFilter($builder->getFilters());
        if ($filter !== null) {
            $feed->setPublisherTypes($filter->getValue());
        }

        //Fixed published in query for feed
        $filters = $this->getFiltersWithoutPublishedLte($query->getFilters());
        $query->setFilters($filters);

        $feed
            ->setUser($this->getCurrentUser())
            ->setQuery($query);

        $this->em->persist($query);
        $this->em->persist($feed);
        $this->em->flush();

        if ($isNewQuery) {
            $this->fetchProducer->publish($query->getId());
        }

        return null;
    }

    /**
     * Get publisher filter from specified filters.
     *
     * @param array $filters Array of FilterInterface's.
     *
     * @return SingleFilterInterface|null
     */
    private function getPublisherFilter(array $filters)
    {
        foreach ($filters as $filter) {
            if ($filter instanceof SingleFilterInterface) {
                if ($filter->getFieldName() === FieldNameEnum::SOURCE_PUBLISHER_TYPE) {
                    return $filter;
                } elseif ($filter instanceof GroupFilterInterface) {
                    return $this->getPublisherFilter($filter->getFilters());
                } elseif ($filter instanceof NotFilter) {
                    return $this->getPublisherFilter([ $filter->getFilter() ]);
                }
            }
        }

        return null;
    }

    /**
     * Get filters without published conditions from specified filters.
     *
     * @param array $filters Array of FilterInterface's.
     *
     * @return array
     */
    private function getFiltersWithoutPublishedLte(array $filters)
    {
        return array_map(function ($filter) { //first level array
            if ($filter instanceof AndFilter) {
                $andFilters = $filter->getFilters();
                foreach ($andFilters as $kItem => $item) { //second level array
                    if ($item instanceof LteFilter and $item->getFieldName() === FieldNameEnum::PUBLISHED ) {
                        unset($andFilters[$kItem]);
                    }
                }
                $filter->setFilters($andFilters);
            }
            return $filter;
        }, $filters);
    }
}
