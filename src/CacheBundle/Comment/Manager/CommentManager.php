<?php

namespace CacheBundle\Comment\Manager;

use CacheBundle\Entity\Comment;
use CacheBundle\Entity\Document;
use CacheBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CommentManager
 * @package CacheBundle\Comment\Manager
 */
class CommentManager implements CommentManagerInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * CommentManager constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Add new comment to specified entity.
     *
     * @param Comment  $comment  A Comment entity instance.
     * @param Document $document A Document entity instance.
     *
     * @return Comment
     */
    public function addComment(Comment $comment, Document $document)
    {
        $comment->setDocument($document);
        $document->incCommentsCount();

        $this->em->persist($document);
        $this->em->persist($comment);
        $this->em->flush();

        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Comment::class);
        $repository->updateCommentMarks($document->getId(), self::NEW_COMMENT_POOL_SIZE);

        return $comment;
    }
}
