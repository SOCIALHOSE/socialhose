<?php

namespace AppBundle\Controller\V1;

use AppBundle\Controller\Traits\FormFactoryAwareTrait;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Manager\Source\SourceManagerInterface;
use CacheBundle\Entity\SourceList;
use CacheBundle\Form\Sources\SourceSearchType;
use CacheBundle\Repository\SourceListRepository;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Model\SourceDocument;
use IndexBundle\SearchRequest\SearchRequestBuilder;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\Annotation\Roles;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class SourceIndexController
 * @package AppBundle\Controller\V1
 *
 * @Route("/source-index", service="app.controller.source-index")
 */
class SourceIndexController extends AbstractV1Controller
{

    use
        TokenStorageAwareTrait,
        FormFactoryAwareTrait;

    /**
     * @var SourceManagerInterface
     */
    private $sourceManager;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * SourceIndexController constructor.
     *
     * @param TokenStorageInterface  $tokenStorage  A TokenStorageInterface
     *                                              instance.
     * @param FormFactoryInterface   $formFactory   A FormFactoryInterface
     *                                              instance.
     * @param SourceManagerInterface $sourceManager A SourceManagerInterface
     *                                              instance.
     * @param EntityManagerInterface $em            A EntityManagerInterface
     *                                              instance.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        SourceManagerInterface $sourceManager,
        EntityManagerInterface $em
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->sourceManager = $sourceManager;
        $this->em = $em;
    }


    /**
     * Fetch all sources from our cache.
     *
     * @Route("/", methods={ "POST" })
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Source Index",
     *  input={
     *     "class"="CacheBundle\Form\Sources\SourceSearchType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="Pagination<IndexBundle\Model\SourceDocument>",
     *     "groups"={ "id", "source" }
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface|\ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        $form = $this->createForm(SourceSearchType::class);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            /** @var SearchRequestBuilder $searchRequestBuilder */
            $searchRequestBuilder = $form->getData();
            $searchRequestBuilder->setUser($this->getCurrentUser());

            $response = $this->sourceManager->find($searchRequestBuilder);

            $advancedFilters = $this->sourceManager->getAvailableFilters($searchRequestBuilder);

            $sort = $searchRequestBuilder->getSorts();
            $sort = [
                'field' => array_search(key($sort), SourceSearchType::$fields),
                'direction' => current($sort),
            ];

            return $this->generateResponse([
                'sources' => $this->paginate($response, $searchRequestBuilder->getPage(), $searchRequestBuilder->getLimit()),
                'advancedFilters' => $advancedFilters ?: (object) [],
                'meta' => [
                    'query' => $request->request->get('query'),
                    'advancedFilters' => $request->request->get('advancedFilters', [])?: (object) [],
                    'sort' => $sort,
                ],
            ], 200, [ 'id', 'source' ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Replace source lists for specified source.
     *
     * @Route("/{id}/list", methods={ "POST" })
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *  resource=true,
     *  section="Source Index",
     *  parameters={
     *     "sourceList"={
     *      "name"="sourceLists",
     *      "dataType"="array",
     *      "description"="Array of source lists ids."
     *     }
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Source entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function replaceListAction(Request $request, $id)
    {
        if (count($this->sourceManager->getIndex()->has($id)) > 0) {
            return $this->generateResponse([ [
                'message' => "Can't find source with id {$id}",
                'transKey' => 'replaceSourceUnknown',
                'type' => 'error',
                'parameters' => [ 'current' => $id ],
            ], ], 404);
        }

        $user = $this->getCurrentUser();
        $sourceLists = $request->request->get('sourceLists');

        if (! is_array($sourceLists)) {
            return $this->generateResponse([[
                'message' => 'sourceLists: This value should not be empty.',
                'transKey' => 'replaceSourceListsEmpty',
                'type' => 'error',
                'parameters' => [ 'current' => null ],
            ], ], 400);
        }

        /** @var SourceListRepository $repository */
        $repository = $this->em->getRepository(SourceList::class);
        $foundedIds = $repository->sanitizeIds($sourceLists, $user->getId());

        if (count($foundedIds) !== count($sourceLists)) {
            //
            // Some of provided id is not found or not owned by current user.
            //
            return $this->generateResponse([ [
                'message' => 'sourceLists: This value is invalid.',
                'transKey' => 'replaceSourceListInvalid',
                'type' => 'error',
                'parameters' => [
                    'current' => $sourceLists,
                    'invalid' => array_diff($sourceLists, $foundedIds),
                ],
            ], ], 400);
        }

        $this->sourceManager->replaceRelation($id, $foundedIds);

        return $this->generateResponse();
    }

    /**
     * Add Sources to Sources lists
     *
     * @Route("/add-to-sources-list", methods={ "POST" })
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *    resource=true,
     *    section="Source Index",
     *    parameters={
     *      "sources"={
     *          "name"="sources",
     *          "dataType"="integer",
     *          "actualType"="collection",
     *          "required"=true,
     *          "description"="Array of Source id."
     *      },
     *      "sourceLists"={
     *          "name"="sourceLists",
     *          "dataType"="integer",
     *          "actualType"="collection",
     *          "required"=true,
     *          "description"="Array of Sources Lists id."
     *      },
     *   }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function addToSourceListAction(Request $request)
    {
        $sources = (array) $request->request->get('sources', []);
        $sourceLists = (array) $request->request->get('sourceLists', []);

        //
        // Check that all fields are provided.
        //
        if (count($sources) === 0) {
            return $this->generateResponse(
                [
                    [
                        'message' => 'Sources should be selected.',
                        'transKey' => 'sourceToListsSourcesEmpty',
                        'type' => 'error',
                        'parameters' => [
                            'current' => $sources,
                        ],
                    ],
                ],
                400
            );
        }

        if (count($sourceLists) === 0) {
            return $this->generateResponse(
                [
                    [
                        'message' => 'Source lists should be selected.',
                        'transKey' => 'sourceToListsSourceListsEmpty',
                        'type' => 'error',
                        'parameters' => [
                            'current' => $sourceLists,
                        ],
                    ],
                ],
                400
            );
        }

        $user = $this->getCurrentUser();

        //
        // Validate specified sources and source lists ids.
        //
        /** @var SourceListRepository $repository */
        $repository = $this->em->getRepository('CacheBundle:SourceList');

        $existsSources = $this->sourceManager->getIndex()->get($sources, 'id');
        $existsSources = array_map(function (SourceDocument $document) {
            return $document['id'];
        }, $existsSources);
        if (count($sources) !== count($existsSources)) {
            return $this->generateResponse(
                [
                    [
                        'message' => 'sources: This value is invalid.',
                        'transKey' => 'sourceToListsSourcesInvalid',
                        'type' => 'error',
                        'parameters' => $sources,
                    ],
                ],
                400
            );
        }

        $existsSourceLists = $repository->sanitizeIds($sourceLists, $user->getId());
        if (count($sourceLists) !== count($existsSourceLists)) {
            return $this->generateResponse(
                [
                    [
                        'message' => 'sourceLists: This value is invalid.',
                        'transKey' => 'sourceToListsSourceListsInvalid',
                        'type' => 'error',
                        'parameters' => $sourceLists,
                    ],
                ],
                400
            );
        }

        $this->sourceManager->bindSourcesToLists($user, $sources, $sourceLists);

        return $this->generateResponse();
    }
}
