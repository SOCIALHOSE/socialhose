<?php

namespace UserBundle\Repository;

use CacheBundle\Entity\Feed\AbstractFeed;
use Doctrine\ORM\EntityRepository;
use UserBundle\Entity\RecentlyUsedFeed;
use UserBundle\Entity\User;

/**
 * RecentlyUsedFeedRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RecentlyUsedFeedRepository extends EntityRepository
{

    /**
     * @param integer $user A User entity instance.
     * @param integer $feed A Feed entity instance.
     *
     * @return RecentlyUsedFeed|null
     */
    public function getAlreadyUsed($user, $feed)
    {
        return $this->createQueryBuilder('Recently')
            ->where('Recently.user = :user AND Recently.feed = :feed')
            ->setParameters([
                'user' => $user,
                'feed' => $feed,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get recently used feeds for specified user.
     *
     * @param integer $user A User entity instance.
     *
     * @return AbstractFeed[]
     */
    public function getRecentlyUsedFor($user)
    {
        return array_map(function (RecentlyUsedFeed $usedFeed) {
            return $usedFeed->getFeed();
        }, $this->createQueryBuilder('Recently')
            ->select('partial Recently.{id}, Feed')
            ->join('Recently.feed', 'Feed')
            ->where('Recently.user = :user')
            ->setParameter('user', $user)
            ->orderBy('Recently.usedAt', 'desc')
            ->getQuery()
            ->getResult());
    }

    /**
     * Add feed to recently used for specified user.
     *
     * @param User         $user A User entity instance.
     * @param AbstractFeed $feed A AbstractFeed entity instance.
     *
     * @return void
     */
    public function addRecentlyUsedFor(User $user, AbstractFeed $feed)
    {
        $entity = RecentlyUsedFeed::create()
            ->setUser($user)
            ->setFeed($feed);

        $this->_em->persist($entity);
        $this->_em->flush($entity);

        $this->_em->getConnection()->executeQuery(sprintf('
            DELETE FROM recently_used_feeds
            WHERE id IN (
                SELECT id FROM (
                    SELECT id
                    FROM recently_used_feeds
                    WHERE user_id = :user
                    ORDER BY used_at DESC LIMIT %d, %d
                ) x
            )
        ', RecentlyUsedFeed::POOL_SIZE, 1000), [ 'user' => $user->getId() ]);
        //
        // We set limit because mysql can't make offset
        // without limit ...
        // Limit it's a magic number. I think is biggest enough.
        //
    }

    /**
     * Remove recently used feed for feed with specified id.
     *
     * @param integer $feed A Feed entity id.
     *
     * @return void
     */
    public function removeForFeed($feed)
    {
        $this->createQueryBuilder('Recently')
            ->delete()
            ->where('Recently.feed = :feed')
            ->setParameter('feed', $feed)
            ->getQuery()
            ->execute();
    }
}
