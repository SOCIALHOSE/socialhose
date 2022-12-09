<?php

namespace CacheBundle\Repository;

use CacheBundle\Entity\Category;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\ORM\EntityRepository;

/**
 * Class CategoryRepository
 * @package CacheBundle\Repository
 */
class CategoryRepository extends EntityRepository
{

    /**
     * Get single entity from repository.
     *
     * @param integer $id     A Entity instance id.
     * @param string  $method A CRUD method name.
     *
     * @return Category|null
     */
    public function getOne($id, $method)
    {
        $expr = $this->_em->getExpressionBuilder();
        $condition = $expr->andX($expr->eq('Category.id', ':id'));
        $parameters = [ 'id' => $id ];

        if ($method !== 'get') {
            $condition->add($expr->eq('Category.internal', false));
        }

        return $this->createQueryBuilder('Category')
            ->addSelect('partial Query.{id, raw, status}')
            ->where($condition)
            ->setParameters($parameters)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Compute feed count in specified category and all childes categories.
     *
     * @param integer $id A Category entity id.
     *
     * @return integer
     */
    public function computeFeedCounts($id)
    {
        return (int) $this->_em->getConnection()->fetchColumn("
            SELECT COUNT(feeds.id)
            FROM feeds
            WHERE
                category_id in (         
                    SELECT id
                    FROM (
                        SELECT *
                        FROM categories
                        ORDER BY parent_id, id
                    ) categories_sorted,
                    (SELECT @pv := '{$id}') initialisation
                    WHERE
                        (
                            FIND_IN_SET(parent_id, @pv) > 0
                            OR id = {$id}
                        )
                        AND @pv := CONCAT(@pv, ',', id)
                )
        ");
    }



    /**
     * Export all feeds inside this category.
     *
     * @param integer $category A Category entity id.
     * @param boolean $export   Export all feeds if true and unexport otherwise.
     *
     * @return void
     */
    public function exportFeedsIn($category, $export = true)
    {
        $this->_em->getConnection()->transactional(function (Connection $conn) use ($category, $export) {
            $conn->exec(sprintf("
                UPDATE feeds
                SET exported = %d
                WHERE
                    category_id in (         
                        SELECT id
                        FROM (
                            SELECT *
                            FROM categories
                            ORDER BY parent_id, id
                        ) categories_sorted,
                        (SELECT @pv := '%s') initialisation
                        WHERE
                            (
                                FIND_IN_SET(parent_id, @pv) > 0
                                OR id = %s
                            )
                            AND @pv := CONCAT(@pv, ',', id)
                    )
            ", $export, $category, $category));
            $conn->exec(sprintf("
                UPDATE categories
                SET exported = %d
                WHERE
                    id in (         
                        SELECT id
                        FROM (
                            SELECT *
                            FROM categories
                            ORDER BY parent_id, id
                        ) categories_sorted,
                        (SELECT @pv := '%s') initialisation
                        WHERE
                            (
                                FIND_IN_SET(parent_id, @pv) > 0
                                OR id = %s
                            )
                            AND @pv := CONCAT(@pv, ',', id)
                    )
            ", $export, $category, $category));
        });
    }

    /**
     * Get active category.
     *
     * @param integer      $id   A Category entity id.
     * @param integer      $user Filter categories by specified owner if set.
     * @param string|array $type Filter by category types.
     *
     * @return Category|null
     */
    public function get($id, $user = null, $type = null)
    {
        $expr = $this->_em->getExpressionBuilder();
        $condition = $expr->andX($expr->eq('Category.id', ':id'));
        $parameters = [ 'id' => $id ];

        if ($type) {
            $type = (array) $type;
            $condition->add($expr->in('Category.type', (array) $type));
        }

        if ($user !== null) {
            $condition->add($expr->eq('Category.user', ':user'));
            $parameters['user'] = $user;
        }

        return $this->createQueryBuilder('Category')
            ->where($condition)
            ->setParameters($parameters)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get array of specified user categories.
     *
     * @param integer $user A User entity id.
     *
     * @return Category[]
     */
    public function getList($user)
    {
        $expr = $this->_em->getExpressionBuilder();

        return $this->createQueryBuilder('Category')
            ->select(
                'partial Category.{id, name, type}',
                'partial Child.{id, name, type}',
                'Feed'
            )
            ->leftJoin('Category.parent', 'Parent')
            ->leftJoin('Category.childes', 'Child')
            ->leftJoin('Category.feeds', 'Feed')
            ->where($expr->andX(
                $expr->eq('Category.user', ':user'),
                $expr->isNull('Category.parent')
            ))
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Check that specified 'child' category is child of 'parent'.
     *
     * @param integer $child  A Category entity id which may by child.
     * @param integer $parent A Category entity id which must by parent of
     *                        specified child.
     *
     * @return boolean
     */
    public function isChildOf($child, $parent)
    {
        //
        // TODO: May be exists more efficient way to do it.
        //
        $position = (int) $this->_em
            ->getConnection()
            ->fetchColumn("
                SELECT
                    FIND_IN_SET({$child}, lvl) AS result
                FROM (
                SELECT
                    GROUP_CONCAT(lvl SEPARATOR ',') AS lvl
                FROM (
                    SELECT @parent := (SELECT GROUP_CONCAT(id SEPARATOR ',')
                    FROM categories
                    WHERE FIND_IN_SET(parent_id, @parent)
                ) AS lvl FROM categories JOIN (SELECT @parent := {$parent}) tmp ) a ) b;
            ");

        return $position !== 0;
    }
}
