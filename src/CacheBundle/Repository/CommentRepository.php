<?php

namespace CacheBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CommentRepository
 * @package CacheBundle\Repository
 */
class CommentRepository extends EntityRepository
{

    /**
     * Get list of comments for documents.
     *
     * @param integer $document A Document entity id.
     * @param array   $fields   Array of comment fields. Fetch only specified
     *                          fields if set.
     * @param integer $count    Number of comments.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListForDocument($document, array $fields = [], $count = null)
    {
        $qb = $this->createQueryBuilder('Comment')
            ->where('Comment.document = :document')
            ->addOrderBy('Comment.createdAt', 'desc')
            ->setParameter('document', $document);

        if (count($fields) > 0) {
            $authorFields = [];
            if (isset($fields['author'])) {
                $authorFields = $fields['author'];
                unset($fields['author']);
            }

            $qb->select('partial Comment.{id, '. implode(',', $fields) .'}');
            if (count($authorFields) > 0) {
                $qb
                    ->join('Comment.author', 'Author')
                    ->addSelect('partial Author.{id, '. implode(',', $authorFields) .'}');
            }
        } else {
            $qb
                ->join('Comment.author', 'Author')
                ->addSelect('Author');
        }

        if ($count !== null) {
            $qb->setMaxResults($count);
        }

        return $qb;
    }

    /**
     * @param integer $documentId A Document entity id.
     * @param integer $poolSize   Max new comments for specified document.
     *
     * @return void
     */
    public function updateCommentMarks($documentId, $poolSize)
    {
        $this->_em->getConnection()->executeUpdate(sprintf('
            UPDATE comments
            SET new = 0
            WHERE id IN (
                SELECT id
                FROM (
                    SELECT id
                    FROM comments
                    WHERE document_id = :document AND new = 1
                    ORDER BY created_at DESC LIMIT %d, %d
                ) AS
            u)
        ', $poolSize, 1000), [ 'document' => $documentId ]);
    }
}
