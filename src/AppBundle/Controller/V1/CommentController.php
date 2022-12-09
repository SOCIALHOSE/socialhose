<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Security\Inspector\InspectorInterface;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package AppBundle\Controller\V1
 *
 * @Route("/comments", service="app.controller.comment")
 */
class CommentController extends AbstractV1CrudController
{

    /**
     * Update specified comment.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  "/{commentId}",
     *  requirements={ "commentId"="\d+" },
     *  methods={ "PUT" }
     * )
     * @AppApiDoc(
     *  section="Comment",
     *  resource=false,input={
     *      "class"="CacheBundle\Form\CommentType",
     *      "name"=false
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Comment",
     *      "groups"={ "comment", "id" }
     *  },
     *  statusCodes={
     *     200="Comment successfully updated.",
     *     400="Invalid data provided.",
     *     403="You don't have permissions to update this comment.",
     *     404="Can't find comment by specified id."
     *  }
     * )
     *
     * @param Request $request   A Request instance.
     * @param integer $commentId A one of comment entity id.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $commentId)
    {
        return parent::putEntity($request, $commentId);
    }

    /**
     * Delete specified comment.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *  "/{commentId}",
     *  requirements={ "commentId"="\d+" },
     *  methods={ "DELETE" }
     * )
     * @AppApiDoc(
     *  section="Comment",
     *  resource=false,
     *  statusCodes={
     *     204="Comment successfully deleted.",
     *     403="You don't have permissions to delete this comment.",
     *     404="Can't find comment by specified id."
     *  }
     * )
     *
     * @param integer $commentId A Comment entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteAction($commentId)
    {
        $entity = $this->em->getRepository(Comment::class)->find($commentId);

        if ($entity === null) {
            return $this->generateResponse("Can't find comment with id {$commentId}.", 404);
        }

        $reasons = $this->checkAccess(InspectorInterface::DELETE, $entity);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        $this->em->getRepository(Document::class)
            ->createQueryBuilder('Document')
            ->update()
            ->set('Document.commentsCount', 'Document.commentsCount - 1')
            ->where('Document.id = :id')
            ->setParameter('id', \app\op\invokeIf($entity->getDocument(), 'getId'))
            ->getQuery()
            ->execute();

        $this->em->remove($entity);
        $this->em->flush();

        return $this->generateResponse();
    }
}
