<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\AbstractCRUDController;
use ApiBundle\Controller\Annotation\Roles;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use AppBundle\Exception\NotAllowedException;
use CacheBundle\DTO\AnalyticDTO;
use CacheBundle\Entity\Analytic\Analytic;
use CacheBundle\Form\AnalyticType;
use CacheBundle\Service\Factory\Analytic\AnalyticFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Security\Inspector\InspectorInterface;
use CacheBundle\Repository\AnalyticRepository;

/**
 * Class AnalyticController
 * @package AppBundle\Controller\V1
 *
 * @Route("/analysis", service="app.controller.analytic")
 */
class AnalyticController extends AbstractCRUDController
{

    /**
     * @var AnalyticFactoryInterface
     */
    private $analyticFactory;


    /**
     * Create new analytic entity.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  methods={ "POST" }
     * )
     * @AppApiDoc(
     *  section="Analytic",
     *  resource=false,
     *  input={
     *      "class"="CacheBundle\Form\AnalyticType"
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Analytic\Analytic",
     *      "groups"={ "analytic", "id" }
     *  },
     *  statusCodes={
     *     200="Analytics successfully created.",
     *     400="Invalid data provided.",
     *     403="You don't have permissions to create analytics."
     *  }
     * )
     *
     * @param Request $request A Http Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function postAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $this->analyticFactory = $this->get('cache.analytic_factory');

        $form = $this->createForm(AnalyticType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var AnalyticDTO $dto */
            $dto = $form->getData();

            try {
                $analytic = $this->analyticFactory->createAnalytic($dto, $user);
            } catch (NotAllowedException $exception) {
                return $this->generateResponse('You not allowed to make analytics');
            }

            $this->getManager()->persist($analytic);
            $this->getManager()->flush();

            return $this->generateResponse($analytic, 200, ['id', 'analytic']);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Get specified analytic by id.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  "/{id}",
     *  requirements={ "id"="\d+" },
     *  methods={ "GET" }
     * )
     * @AppApiDoc(
     *  resource=true,
     *  section="Analytic",
     *  output={
     *     "class"="CacheBundle\Entity\Analytic\Analytic",
     *     "groups"={"id"}
     *  },
     *  statusCodes={
     *     200="Analytics successfully returned.",
     *     403="You don't have permissions to view this analytics.",
     *     404="Can't find analytic by specified id."
     *  }
     * )
     *
     * @param integer $id Analytic entity id.
     *
     * @return \CacheBundle\Entity\Analytic\Analytic|\ApiBundle\Response\ViewInterface
     */
    public function getAction($id)
    {
        return parent::getEntity($id);
    }


    /**
     * Delete specified analytic.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  "/{id}",
     *  requirements={ "id"="\d+" },
     *  methods={ "DELETE" }
     * )
     * @AppApiDoc(
     *  resource=true,
     *  section="Analytic",
     *  statusCodes={
     *     204="Analytic successfully deleted.",
     *     403="You don't have permissions to delete this analytic.",
     *     404="Can't find analytic by specified id."
     *  }
     * )
     *
     * @param integer $id A Analytic entity id.
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function deleteAction($id)
    {
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $reasons = $this->checkAccess(InspectorInterface::DELETE, $analytic);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }
        $analyticContext = $analytic->getContext();

        if (isset($analyticContext)) {
            ($analyticContext->getAnalytics()->count() == 1) ? $this->getManager()->remove($analyticContext) : "";
        }

        $this->getManager()->remove($analytic);
        $this->getManager()->flush();

        return $this->generateResponse();
    }

    /**
     * Update analytic.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}", methods={ "PUT" }, requirements={ "id"="\d+" })
     * @AppApiDoc(
     *  section="Analytic",
     *  resource=true,
     *  input={
     *      "class"="CacheBundle\Form\AnalyticType",
     *      "name"=false
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Analytic\Analytic",
     *       "groups"={ "analytic", "id" }
     *  },
     *  statusCodes={
     *     200="Analytics successfully updated.",
     *     400="Invalid data provided."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id Analytic entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $id)
    {
        $user = $this->getCurrentUser();
        $this->analyticFactory = $this->get('cache.analytic_factory');

        /** @var AnalyticRepository $analyticRepository */
        $repository = $this->getManager()->getRepository($this->entity);
        /** @var Analytic $analytic */
        $analytic = $repository->find($id);

        if (!$analytic instanceof Analytic) {
            return $this->generateResponse("Can't find analytic with id {$id}.", 404);
        }
        $feeds = $analytic->getContext()->getFeeds();
        $feedsId = [];
        foreach ($feeds as $feedsVal) {
            $feedsId[] = $feedsVal->getId();
        }

        $analyticDto = new AnalyticDTO($feedsId, null, $analytic->getContext()->getFilters(), $analytic->getContext()->getRawFilters());
        $form = $this->createForm(AnalyticType::class, $analyticDto);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            /** @var AnalyticDTO $dto */
            $dto = $form->getData();

            try {
                $analyticContext = $analytic->getContext();
                if (isset($analyticContext)) {
                    ($analyticContext->getAnalytics()->count() == 1) ? $this->getManager()->remove($analyticContext) : "";
                }
                $analytic = $this->analyticFactory->updateAnalytic($dto, $user, $analytic);

            } catch (NotAllowedException $exception) {
                return $this->generateResponse('You not allowed to update analytics');
            }

            $this->getManager()->persist($analytic);
            $this->getManager()->flush();

            return $this->generateResponse($analytic, 200, ['id', 'analytic']);
        }

        return $this->generateResponse($form, 400);
    }


    /**
     * Get list of categories for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(methods={ "GET" })
     * @AppApiDoc(
     *  section="Analytic",
     *  output={
     *     "class"="Pagination<CacheBundle\Entity\Analytic\Analytic>",
     *      "groups"={ "analytic", "id","context" }
     *  },
     *  statusCodes={
     *     200="List of analytic successfully returned."
     *  }
     * )
     *
     * @param Request $request
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        /** @var AnalyticRepository $repository */
        $repository = $this->getManager()->getRepository(Analytic::class);

        $user = $this->getCurrentUser();

        $pagination = $this->paginate(
            $request,
            $repository->getList($user->getId())
        );

        // Simulate pagination serialization.
        return $this->generateResponse([
            $pagination
        ], 200, [
            'analytic',
            'id',
            'context'
        ]);
    }
}
