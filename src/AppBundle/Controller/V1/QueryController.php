<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use AppBundle\Controller\Traits\FormFactoryAwareTrait;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Exception\LimitExceedException;
use AppBundle\Form\SearchRequest\SimpleQuerySearchRequestType;
use AppBundle\Manager\SimpleQuery\SimpleQueryManagerInterface;
use AppBundle\Manager\Source\SourceManagerInterface;
use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use Common\Enum\FieldNameEnum;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Enum\AppLimitEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class QueryController
 * @package AppBundle\Controller\V1
 *
 * @Route("/query", service="app.controller.query")
 */
class QueryController extends AbstractV1Controller
{

    use
        FormFactoryAwareTrait,
        TokenStorageAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SourceManagerInterface
     */
    private $sourceManager;

    /**
     * @var SimpleQueryManagerInterface
     */
    private $queryManager;

    /**
     * @var DocumentContentExtractorInterface
     */
    private $extractor;

    /**
     * QueryController constructor.
     *
     * @param FormFactoryInterface              $formFactory   A
     *                                                         FormFactoryInterface
     *                                                         instance.
     * @param EntityManagerInterface            $em            A
     *                                                         RestrictionsRepositoryInterface
     *                                                         instance.
     * @param TokenStorageInterface             $tokenStorage  A
     *                                                         TokenStorageInterface
     *                                                         instance.
     * @param SourceManagerInterface            $sourceManager A
     *                                                         SourceManagerInterface
     *                                                         instance.
     * @param SimpleQueryManagerInterface       $queryManager  A
     *                                                         SimpleQueryManagerInterface
     *                                                         instance.
     * @param DocumentContentExtractorInterface $extractor     A
     *                                                         DocumentContentExtractorInterface
     *                                                         instance.
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        SourceManagerInterface $sourceManager,
        SimpleQueryManagerInterface $queryManager,
        DocumentContentExtractorInterface $extractor
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->sourceManager = $sourceManager;
        $this->queryManager = $queryManager;
        $this->extractor = $extractor;
    }

    /**
     * Make simple search without saving query in database.
     * Fetched documents are cached but not indexed.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/search", methods={ "POST" })
     * @ApiDoc(
     *  resource="Search",
     *  section="Query",
     *  input={
     *     "class"="AppBundle\Form\SearchRequest\SimpleQuerySearchRequestType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *      "documents"={
     *       "class"="Pagination<CacheBundle\Entity\Document>",
     *       "groups"={ "document" }
     *      },
     *      "advancedFilters"={
     *       "dataType"="array",
     *       "required"=true,
     *       "readonly"=true,
     *       "description"="Array of advanced filters values for this search request."
     *      },
     *      "stats"={
     *       "dataType"="object",
     *       "required"=true,
     *       "readonly"=true,
     *       "description"="Internal statistics, showed only in staging and local developers machine.",
     *       "children"={
     *        "totalOnPage"={
     *         "dataType"="integer",
     *         "required"=true,
     *         "readonly"=true,
     *         "description"="Total founded document on current page."
     *        },
     *        "newDocuments"={
     *         "dataType"="integer",
     *         "required"=true,
     *         "readonly"=true,
     *         "description"="Number of documents that were not in our database."
     *        },
     *        "alreadyExistsDocuments"={
     *         "dataType"="integer",
     *         "required"=true,
     *         "readonly"=true,
     *         "description"="Number of documents that already in our database."
     *        },
     *        "fromCache"={
     *         "dataType"="boolean",
     *         "required"=true,
     *         "readonly"=true,
     *         "description"="Flag, all documents fetched from our internal cache if set."
     *        },
     *        "expiresAt"={
     *         "dataType"="datetime",
     *         "required"=true,
     *         "readonly"=true,
     *         "description"="When this query is expired."
     *        }
     *       }
     *      }
     *     }
     *  },
     *  statusCodes={
     *     200="Search completed.",
     *     400="Invalid data provided"
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function searchAction(Request $request)
    {
        $form = $this->createForm(SimpleQuerySearchRequestType::class);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            $user = $this->getCurrentUser();

            try {
                $user->useLimit(AppLimitEnum::searches());
            } catch (LimitExceedException $exception) {
                return $this->generateResponse([
                    'failedRestriction' => AppLimitEnum::SEARCHES,
                    'restrictions' => $user->getRestrictions(),
                ], 402);
            }

            $this->em->persist($user);
            $this->em->flush();

            /** @var SearchRequestBuilderInterface $builder */
            $builder = $form->getData();

            $searchRequest = $builder
                ->setFields([
                    FieldNameEnum::TITLE,
                    FieldNameEnum::MAIN,
                ])
                ->addSort(FieldNameEnum::PUBLISHED, 'desc')
                ->build();

            $response = $this->queryManager->searchAndCache(
                $searchRequest,
                $request->request->get('filters', []),
                $request->request->get('advancedFilters', [])
            );

            $query = $response->getQuery();

            $response->mapDocuments(function (ArticleDocumentInterface $document) use ($query) {
                return $document->mapNormalizedData(function (array $data) use ($query) {
                    $result = $this->extractor->extract(
                        $data['content'],
                        $query->getRaw(),
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

            $result = [
                'documents' => $this->paginate($response, $builder->getPage(), $builder->getLimit()),
                'advancedFilters' => $searchRequest->getAvailableAdvancedFilters()  ?: (object) [],
            ];

            //
            // Return internal statistic.
            //
            $result['stats'] = [
                'newDocuments' => $response->getUniqueCount(),
                'alreadyExistsDocuments' => $response->count() - $response->getUniqueCount(),
                'fromCache' => $response->isFromCache(),
                'expiresAt' => $query->getExpirationDate()->format('c'),
            ];

            //
            // Return meta information about query.
            //
            $sources = $this->sourceManager->getSourcesForQuery($query, [ 'id', 'title', 'type' ]);
            $sourceLists = $this->sourceManager->getSourceListsForQuery($query, [ 'id', 'name' ]);

            $result['meta'] = [
                'type' => 'query',
                'status' => 'synced',
                'search' => [
                    'query' => $query->getRaw(),
                    'filters' => $query->getRawFilters() ?: (object) [],
                    'advancedFilters' => $query->getRawAdvancedFilters() ?: (object) [],
                ],
                'sources' => $sources,
                'sourceLists' => $sourceLists,
            ];

            return $this->generateResponse($result);
        }

        return $this->generateResponse($form, 400);
    }
}
