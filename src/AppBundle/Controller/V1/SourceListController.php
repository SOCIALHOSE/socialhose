<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Security\Inspector\InspectorInterface;
use AppBundle\AppBundleServices;
use AppBundle\Manager\Source\SourceManagerInterface;
use CacheBundle\CacheBundleServices;
use CacheBundle\Entity\SourceList;
use CacheBundle\Entity\SourceToSourceList;
use CacheBundle\Form\Sources\SourceListSearchType;
use CacheBundle\Form\Sources\SourceListType;
use CacheBundle\Form\Sources\SourceSearchType;
use CacheBundle\Repository\SourceListRepository;
use CacheBundle\Security\Inspector\SourceListInspector;
use IndexBundle\SearchRequest\SearchRequestBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\Annotation\Roles;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class SourceIndexController
 * @package AppBundle\Controller\V1
 *
 * @Route("/source-list", service="app.controller.source-list")
 */
class SourceListController extends AbstractApiController
{

    /**
     * Get list of sources for the user
     *
     * @Route("/list", methods={ "POST" })
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *    resource=true,
     *    section="Source List",
     *    input={
     *     "class"="CacheBundle\Form\Sources\SourceListSearchType",
     *     "name"=false
     *    },
     *    output={
     *     "class"="Pagination<CacheBundle\Entity\SourceList>",
     *     "groups"={ "id", "source_list" }
     *    }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(SourceListSearchType::class);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $page = (int) $data['page'];
            $limit = (int) $data['limit'];
            $onlyShared = (boolean) $data['onlyShared'];

            $user = $this->getCurrentUser();
            $em = $this->getManager();

            /** @var SourceListRepository $sourceListRepository */
            $sourceListRepository = $em->getRepository(SourceList::class);
            /** @var PaginatorInterface $paginator */
            $paginator = $this->get('knp_paginator');

            $qb = $sourceListRepository->getSourcesListsQB($user->getId(), $data['sort'], $onlyShared);
            $pagination = $paginator->paginate(
                $qb,
                $page,
                $limit
            );

            $sort = $data['sort'] ;
            $sort = [
                'field' => array_search(key($sort), SourceListSearchType::$fields),
                'direction' => current($sort),
            ];

            /** @var NormalizerInterface $normalizer */
            $normalizer = $this->get('serializer');
            $result = $normalizer->normalize($pagination, null, ['id', 'source_list']);
            $result['sort'] = $sort;

            return $this->generateResponse($result, 200);
        }

        return $this->generateResponse($form, 400);
    }


    /**
     * Create a source list
     *
     * @Route("/", methods={ "POST" })
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *    resource=true,
     *    section="Source List",
     *    input={
     *     "class"="CacheBundle\Form\Sources\SourceListType",
     *     "name"=false
     *    },
     *    output={
     *     "class"="CacheBundle\Entity\SourceList",
     *    }
     * )
     *
     * @param Request $request A Request entity instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        $sourceList = new SourceList();
        $sourceList->setUser($this->getCurrentUser());

        $form = $this->createForm(SourceListType::class, $sourceList);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            $em = $this->getManager();
            $em->persist($sourceList);
            $em->flush();

            return $this->generateResponse($sourceList, 200, [
                'source_list',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Rename a source list
     *
     * @Route(
     *  "/{id}",
     *  requirements={ "id": "\d+" },
     *  methods={ "PUT" }
     * )
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *    resource=true,
     *    section="Source List",
     *    parameters={
     *        "name"={
     *            "name"="name",
     *            "dataType"="string",
     *            "required"="true",
     *            "description"="A new name of the source list"
     *        }
     *    }
     * )
     *
     * @param Request    $request    A HTTP Request instance.
     * @param SourceList $sourceList A updated SourceList instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function updateAction(Request $request, SourceList $sourceList)
    {
        $form = $this->createForm(SourceListType::class, $sourceList);

        $form->submit($request->request->all());
        if ($form->isValid()) {
            $em = $this->getManager();

            $reasons = $this->checkAccess(InspectorInterface::UPDATE, $sourceList);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            $sourceList->setUpdatedBy($this->getCurrentUser());

            $em->persist($sourceList);
            $em->flush();

            return $this->generateResponse($sourceList, 200, [
                'source_list',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Delete a source list
     *
     * @Route("/{id}",
     *     requirements={ "id": "\d+" },
     *     methods={ "DELETE" }
     * )
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *    resource=true,
     *    section="Source List",
     *    parameters={
     *         "id"={
     *            "name"="id",
     *            "dataType"="integer",
     *            "required"="true",
     *            "description"="Id of the source list which changing"
     *        }
     *    }
     * )
     *
     * @param SourceList $sourceList A deleted SourceList instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteAction(SourceList $sourceList)
    {
        $reasons = $this->checkAccess(InspectorInterface::DELETE, $sourceList);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        //
        // Remove this source list from all sources which is contains in it.
        //
        /** @var SourceManagerInterface $sourceManager */
        $sourceManager = $this->container->get(CacheBundleServices::SOURCE_CACHE);
        $sourceManager->unbindSourcesFromLists($sourceList->getId());

        $em = $this->getManager();
        $em->remove($sourceList);
        $em->flush();

        return $this->generateResponse();
    }

    /**
     * Get list of sources for specified source list.
     *
     * @Route("/{id}/sources/search",
     *     requirements={ "id": "\d+" },
     *     methods={ "POST" }
     * )
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *     resource="Sources of specified source list",
     *     section="Source List",
     *     input={
     *      "class"="CacheBundle\Form\Sources\SourceSearchType",
     *      "name"=false
     *     }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A SourceList entity id.
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface|\ApiBundle\Response\ViewInterface
     */
    public function sourcesAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        /** @var SourceListRepository $repository */
        $repository = $this->getManager()->getRepository('CacheBundle:SourceList');
        $sourceList = $repository->getSourcesLists($id, $user->getId());

        if ($sourceList === null) {
            return $this->generateResponse("Can't find source list with id $id", 404);
        }

        $form = $this->createForm(SourceSearchType::class);
        $form->submit($request->request->all());

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SearchRequestBuilder $searchRequestBuilder */
            $searchRequestBuilder = $form->getData();
            /** @var SourceManagerInterface $manager */
            $manager = $this->get(AppBundleServices::SOURCE_MANAGER);
            $searchRequestBuilder->setUser($user);

            $response = $manager->find($searchRequestBuilder, $sourceList);

            /** @var PaginatorInterface $paginator */
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $response,
                $searchRequestBuilder->getPage(),
                $searchRequestBuilder->getLimit()
            );

            $sort = $searchRequestBuilder->getSorts();
            $sort = [
                'field' => array_search(key($sort), SourceSearchType::$fields),
                'direction' => current($sort),
            ];

            return $this->generateResponse([
                'sources' => $pagination,
                'filters' => $request->request->get('filters', (object) []),
                'sort' => $sort,
            ], 200, [ 'id', 'source' ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Clone current list.
     *
     * @Route("/{id}/clone",
     *     requirements={ "id": "\d+" },
     *     methods={ "POST" }
     * )
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *     resource="Clone specified source list",
     *     section="Source List",
     *     parameters={
     *      {
     *       "name"="name",
     *       "dataType"="string",
     *       "required"="true"
     *      }
     *     },
     *     output={
     *      "class"="CacheBundle\Entity\SourceList",
     *      "groups"={ "id", "source_list" }
     *     }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A SourceList entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function cloneAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        /** @var SourceListRepository $repository */
        $repository = $this->getManager()->getRepository('CacheBundle:SourceList');
        $sourceList = $repository->getSourcesLists($id, $user->getId());
        $em = $this->getManager();

        $name = $request->request->get('name');

        if ($name === null) {
            return $this->generateResponse('Required field \'name\' is not provided or empty.');
        }

        if ($sourceList === null) {
            return $this->generateResponse("Can't find source list with id $id", 404);
        }

        $clone = $sourceList->cloneList();
        $clone->setName($name);
        /** @var SourceManagerInterface $sourceManager */
        $sourceManager = $this->get(CacheBundleServices::SOURCE_CACHE);

        $sources = $sourceList->getSources()->map(function (SourceToSourceList $source) {
            return $source->getSource();
        })->toArray();

        $em->persist($clone);
        $em->flush();

        //
        // We should add and original id 'cause otherwise he lost his binding.
        //
        $sourceManager->bindSourcesToLists($user, $sources, [ $sourceList->getId(), $clone->getId() ]);

        return $this->generateResponse($clone, 200, [
            'source_list',
            'id',
        ]);
    }

    /**
     * Share specified source list.
     *
     * @Route("/{id}/share",
     *     requirements={ "id": "\d+" },
     *     methods={ "POST" }
     * )
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *     resource="Sharing",
     *     section="Source List"
     * )
     *
     * @param string $id A SourceList entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function shareAction($id)
    {
        $user = $this->getCurrentUser();
        $em = $this->getManager();

        /** @var SourceListRepository $repository */
        $repository = $em->getRepository('CacheBundle:SourceList');
        $sourceList = $repository->getSourcesLists($id, $user->getId());

        if ($sourceList === null) {
            return $this->generateResponse("Can't find source list with id $id", 404);
        }

        $reasons = $this->checkAccess(SourceListInspector::SHARE, $sourceList);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        if (! $sourceList->getIsGlobal()) {
            $sourceList
                ->setUpdatedBy($this->getCurrentUser())
                ->setIsGlobal(true);
            $em->persist($sourceList);
            $em->flush();
        }

        return $this->generateResponse();
    }

    /**
     * Unshare specified source list.
     *
     * @Route("/{id}/unshare",
     *     requirements={ "id": "\d+" },
     *     methods={ "POST" }
     * )
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @ApiDoc(
     *     resource="Sharing",
     *     section="Source List"
     * )
     *
     * @param string $id A SourceList entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function unshareAction($id)
    {
        $user = $this->getCurrentUser();
        $em = $this->getManager();

        /** @var SourceListRepository $repository */
        $repository = $em->getRepository('CacheBundle:SourceList');
        $sourceList = $repository->getSourcesLists($id, $user->getId());

        if ($sourceList === null) {
            return $this->generateResponse("Can't find source list with id $id", 404);
        }

        $reasons = $this->checkAccess(SourceListInspector::UNSHARE, $sourceList);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        if ($sourceList->getIsGlobal()) {
            $sourceList
                ->setUpdatedBy($this->getCurrentUser())
                ->setIsGlobal(false);
            $em->persist($sourceList);
            $em->flush();
        }

        return $this->generateResponse();
    }
}
