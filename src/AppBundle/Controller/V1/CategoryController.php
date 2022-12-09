<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\AbstractCRUDController;
use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Response\ViewInterface;
use ApiBundle\Security\Inspector\InspectorInterface;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use CacheBundle\Entity\Category;
use CacheBundle\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UserBundle\Enum\AppLimitEnum;

/**
 * Class CategoryController
 * @package AppBundle\Controller\V1
 *
 * @Route("/categories", service="app.controller.category")
 */
class CategoryController extends AbstractCRUDController
{

    /**
     * Move specified feed to another category.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{movedId}/move_to/{destinationId}",
     *     requirements={
     *      "movedId": "\d+",
     *      "destinationId": "\d+"
     *     },
     *     methods={ "POST" }
     * )
     * @AppApiDoc(
     *  resource=true,
     *  section="Category",
     *  output={
     *     "class"="Pagination<CacheBundle\Entity\Category>",
     *     "groups"={ "id", "category_tree", "feed_tree" }
     *  },
     *  statusCodes={
     *     200="List of updated categories successfully returned.",
     *     400="Invalid data provided.",
     *     403="You don't have permissions to move this category.",
     *     404="Can't find moved or destination category."
     *  }
     * )
     *
     * @param integer $movedId       A moved Category entity id.
     * @param integer $destinationId A Category entity id where the category is
     *                               moved.
     *
     * @return \ApiBundle\Response\ViewInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function moveAction($movedId, $destinationId)
    {
        $movedId = (integer) $movedId;
        $destinationId = (integer) $destinationId;
        $userId = \app\op\invokeIf($this->getCurrentUser(), 'getId');

        /** @var CategoryRepository $repository */
        $repository = $this->getManager()->getRepository(Category::class);

        $moved = $repository->get($movedId, $userId);
        if (! $moved instanceof Category) {
            return $this->generateResponse("Can't find category with id {$movedId}.", 404);
        }

        //
        // Check that user don't try to move internal category.
        //
        if ($moved->isInternal()) {
            return $this->generateResponse('Can\'t move internal category.', 403);
        }

        //
        // We should don't make any changes if client try to move category into
        // the same category.
        //

        if ($moved->getParent()->getId() !== $destinationId) {
            $destination = $repository->get($destinationId, $userId, [
                Category::TYPE_CUSTOM,
                Category::TYPE_MY_CONTENT,
            ]);
            if (! $destination instanceof Category) {
                return $this->generateResponse("Can't find category with id {$destinationId}.", 404);
            }

            //
            // All ok, now we need to validate destination category id.
            //
            $moved->setParent($destination);

            /** @var ValidatorInterface $validator */
            $validator = $this->get('validator');
            $errors = $validator->validate($moved);

            if (count($errors) > 0) {
                //
                // Get all violation errors and send it to client.
                //
                $errors = array_map(function (ConstraintViolationInterface $violation) {
                    return $violation->getMessage();
                }, iterator_to_array($errors));

                return $this->generateResponse($errors, 400);
            }

            //
            // Validation passed, update entity.
            //
            $this->getManager()->persist($moved);
            $this->getManager()->flush();
        }

        return $this->forward('app.controller.category:listAction');
    }

    /**
     * Create new category for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(methods={ "POST" })
     * @AppApiDoc(
     *  resource=true,
     *  section="Category",
     *  input={
     *      "class"="CacheBundle\Form\CategoryType",
     *      "name"=false
     *  },
     *  output={
     *     "class"="CacheBundle\Entity\Category",
     *     "groups"={ "id", "category" }
     *  },
     *  statusCodes={
     *     200="Category successfully created.",
     *     400="Invalid data provided.",
     *     403="You don't have permissions to create category."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \CacheBundle\Entity\Category|\ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        return parent::createEntity($request, new Category($this->getCurrentUser()));
    }

    /**
     * Get list of categories for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(methods={ "GET" })
     * @AppApiDoc(
     *  section="Category",
     *  output={
     *     "class"="Pagination<CacheBundle\Entity\Category>",
     *     "groups"={ "id", "category_tree", "feed_tree" }
     *  },
     *  statusCodes={
     *     200="List of categories successfully returned."
     *  }
     * )
     *
     * @return ViewInterface
     */
    public function listAction()
    {
        /** @var CategoryRepository $repository */
        $repository = $this->getManager()->getRepository(Category::class);

        $user = $this->getCurrentUser();
        $categories = $repository->getList($user->getId());
        $count = count($categories);

        // Simulate pagination serialization.
        return $this->generateResponse([
            'data' => $categories,
            'count' => $count,
            'totalCount' => $count,
            'page' => 1,
            'limit' => $count,
        ], 200, [
            'id',
            'category_tree',
            'feed_tree',
        ]);
    }

    /**
     * Get specified category by id.
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
     *  section="Category",
     *  output={
     *     "class"="CacheBundle\Entity\Category",
     *     "groups"={ "id", "category", "feed_tree" }
     *  },
     *  statusCodes={
     *     200="Category successfully returned.",
     *     403="You don't have permissions to view this category.",
     *     404="Can't find category by specified id."
     *  }
     * )
     *
     * @param integer $id A Category entity id.
     *
     * @return \CacheBundle\Entity\Category|\ApiBundle\Response\ViewInterface
     */
    public function getAction($id)
    {
        return parent::getEntity($id);
    }

    /**
     * Update specified category.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  "/{id}",
     *  requirements={ "id"="\d+" },
     *  methods={ "PUT" }
     * )
     * @AppApiDoc(
     *  resource=true,
     *  section="Category",
     *  input={
     *      "class"="CacheBundle\Form\CategoryType",
     *      "name"=false
     *  },
     *  output={
     *     "class"="CacheBundle\Entity\Category",
     *     "groups"={ "id", "category" }
     *  },
     *  statusCodes={
     *     200="Category successfully updated.",
     *     400="Invalid data provided.",
     *     403="You don't have permissions to update this category.",
     *     404="Can't find category by specified id."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Category entity id.
     *
     * @return \CacheBundle\Entity\Category|\ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $id)
    {
        return parent::putEntity($request, $id);
    }

    /**
     * Delete specified category.
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
     *  section="Category",
     *  statusCodes={
     *     204="Category successfully deleted.",
     *     403="You don't have permissions to delete this category.",
     *     404="Can't find category by specified id."
     *  }
     * )
     *
     * @param integer $id A Category entity id.
     *
     * @return array|\ApiBundle\Response\ViewInterface
     */
    public function deleteAction($id)
    {
        /** @var CategoryRepository $repository */
        $repository = $this->getManager()->getRepository($this->entity);
        $category = $repository->find($id);

        if ($category === null) {
            return $this->generateResponse("Can't find category with id {$id}.", 404);
        }

        $reasons = $this->checkAccess(InspectorInterface::DELETE, $category);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        //
        // Update restriction limit for current user only if deleted category has
        // some feeds.
        //
        $feedCount = $repository->computeFeedCounts($id);
        if ($feedCount > 0) {
            $user = $this->getCurrentUser();
            $user->releaseLimit(AppLimitEnum::feeds(), $feedCount);

            $this->getManager()->persist($user);
        }

        $this->getManager()->remove($category);
        $this->getManager()->flush();

        return $this->generateResponse();
    }
}
