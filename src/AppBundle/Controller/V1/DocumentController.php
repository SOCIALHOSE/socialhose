<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Security\AccessChecker\AccessCheckerInterface;
use ApiBundle\Security\Inspector\InspectorInterface;
use ApiDocBundle\Controller\Annotation\AppApiDoc;
use AppBundle\Controller\Traits\AccessCheckerTrait;
use AppBundle\Controller\Traits\FormFactoryAwareTrait;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Entity\EmailedDocument;
use AppBundle\Form\EmailedDocumentType;
use CacheBundle\Comment\Manager\CommentManagerInterface;
use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;
use CacheBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class DocumentController
 * @package AppBundle\Controller\V1
 *
 * @Route("/documents", service="app.controller.document")
 */
class DocumentController extends AbstractV1Controller
{

    use
        TokenStorageAwareTrait,
        FormFactoryAwareTrait,
        AccessCheckerTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * @var ProducerInterface
     */
    private $emailProducer;

    /**
     * DocumentController constructor.
     *
     * @param TokenStorageInterface   $tokenStorage   A TokenStorageInterface
     *                                                instance.
     * @param FormFactoryInterface    $formFactory    A FormFactoryInterface
     *                                                instance.
     * @param AccessCheckerInterface  $accessChecker  A AccessCheckerInterface
     *                                                instance.
     * @param EntityManagerInterface  $em             A EntityManagerInterface
     *                                                instance.
     * @param CommentManagerInterface $commentManager A CommentManagerInterface
     *                                                instance.
     * @param ProducerInterface       $emailProducer  A producer interface for
     *                                                emailing documents.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        AccessCheckerInterface $accessChecker,
        EntityManagerInterface $em,
        CommentManagerInterface $commentManager,
        ProducerInterface $emailProducer
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->accessChecker = $accessChecker;
        $this->em = $em;
        $this->commentManager = $commentManager;
        $this->emailProducer = $emailProducer;
    }

    /**
     * Create new comment for specified document.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{documentId}/comments",
     *     requirements={ "documentId"="\d+" },
     *     methods={ "POST" }
     * )
     * @AppApiDoc(
     *  section="Document",
     *  resource=true,
     *  input={
     *      "class"="CacheBundle\Form\CommentType",
     *      "name"=false
     *  },
     *  output={
     *      "class"="CacheBundle\Entity\Comment",
     *      "groups"={ "comment", "id" }
     *  },
     *  statusCodes={
     *     200="Comment successfully saved.",
     *     400="Invalid data provided."
     *  }
     * )
     *
     * @param Request $request    A Request instance.
     * @param integer $documentId Commented Document entity id.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    public function createCommentAction(Request $request, $documentId)
    {
        $document = $this->em->getRepository(Document::class)->find($documentId);

        if (! $document instanceof Document) {
            return $this->generateResponse([[
                'message' => 'Document not found',
                'transKey' => 'commentDocumentInvalidDocument',
                'type' => 'error',
                'parameters' => [ 'current' => $documentId ],
            ], ], 404);
        }

        $comment = new Comment($this->getCurrentUser(), '');

        $form = $this->createForm($comment->getCreateFormClass(), $comment);

        // Submit data into form.
        $form->submit($request->request->all());
        if ($form->isValid()) {
            //
            // Check that current user can create this entity.
            // If user don't have rights to create this entity we should send all
            // founded restrictions to client.
            //
            $reasons = $this->checkAccess(InspectorInterface::CREATE, $comment);
            if (count($reasons) > 0) {
                //
                // User don't have rights to create this entity so send all
                // founded restriction reasons to client.
                //
                return $this->generateResponse($reasons, 403);
            }

            $this->commentManager->addComment($comment, $document);

            $this->em->persist($comment);
            $this->em->flush();

            return $comment;
        }

        // Client send invalid data.
        return $this->generateResponse($form, 400);
    }

    /**
     * Get list of comments for specified document.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/{documentId}/comments",
     *     requirements={ "documentId"="\d+" },
     *     methods={ "GET" }
     * )
     * @AppApiDoc(
     *  section="Document",
     *  resource=true,
     *  filters={
     *     {
     *          "name"="offset",
     *          "dataType"="integer",
     *          "description"="Offset from beginning of collection, start from 1",
     *          "requirements"="\d+",
     *          "default"="1"
     *     },
     *     {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "description"="Max entities per page, default 10",
     *          "requirements"="\d+",
     *          "default"="10"
     *     },
     *  },
     *  output={
     *      "class"="Paginated<CacheBundle\Entity\Comment>",
     *      "groups"={ "comment", "id" }
     *  },
     *  statusCodes={
     *     200="List of comments returned.",
     *     404="Invalid document id."
     *  }
     * )
     * @param Request $request    A Request instance.
     * @param integer $documentId A Document entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function getCommentsAction(Request $request, $documentId)
    {
        $document = $this->em->getRepository(Document::class)->find($documentId);

        if (! $document instanceof Document) {
            return $this->generateResponse([[
                'message' => 'Document not found',
                'transKey' => 'getDocumentCommentsInvalidDocument',
                'type' => 'error',
                'parameters' => [ 'current' => $documentId ],
            ], ], 404);
        }

        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Comment::class);
        $qb = $repository->getListForDocument($documentId);

        $offset = $request->query->getInt('offset', CommentManagerInterface::NEW_COMMENT_POOL_SIZE);
        $limit = $request->query->getInt('limit', 10);

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $this->generateResponse(new Paginator($qb), 200, [ 'id', 'comment' ]);
    }

    /**
     * Send specified documents content to recipients.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route(
     *     "/email",
     *     methods={ "POST" }
     * )
     * @AppApiDoc(
     *  section="Document",
     *  resource=true,
     *  input={
     *      "class"="AppBundle\Form\EmailedDocumentType",
     *      "name"=false
     *  },
     *  statusCodes={
     *     204="Email's sent.",
     *     400="Invalid data."
     *  }
     * )
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function emailAction(Request $request)
    {
        $emailedDocument = new EmailedDocument();
        $form = $this->createForm(EmailedDocumentType::class, $emailedDocument);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($emailedDocument);
            $this->em->flush();

            $this->emailProducer->publish($emailedDocument->getId());

            return $this->generateResponse();
        }

        return $this->generateResponse($form, 400);
    }
}
