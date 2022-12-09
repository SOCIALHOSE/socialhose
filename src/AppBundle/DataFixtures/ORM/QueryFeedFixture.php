<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\AbstractFixture;
use AppBundle\Exception\LimitExceedException;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Query\StoredQuery;
use CacheBundle\Entity\Category;
use Common\Enum\FieldNameEnum;
use Common\Enum\PublisherTypeEnum;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;
use UserBundle\Enum\AppLimitEnum;

/**
 * Class QueryFeedFixture
 * @package AppBundle\DataFixtures\ORM
 */
class QueryFeedFixture extends AbstractFixture
{

    /**
     * Max available feeds.
     */
    const MAX_FEEDS = 5;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load(ObjectManager $manager)
    {
        switch (true) {
            case $this->checkEnvironment('dev'):
                $this->loadForDevelopment($manager);
                break;

            case $this->checkEnvironment('test'):
                $this->loadForTesting($manager);
                break;
        }
    }

    /**
     * Get the order of this fixture.
     *
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }

    /**
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    private function loadForDevelopment(ObjectManager $manager)
    {
        /** @var User[] $users */
        $users = [
            $this->getReference('test@email.com'),
            $this->getReference('master@email.com'),
        ];

        for ($i = 0; $i < self::MAX_FEEDS; $i++) {
            $raw = strtolower($this->getFaker()->word);

            $index = random_int(0, count($users) - 1);

            $user = $users[$index];

            try {
                $user->useLimit(AppLimitEnum::feeds());
            } catch (LimitExceedException $exception) {
                continue;
            }

            $categoriesCount = count($user->getCategories());

            $query = $this->createQuery($raw);
            $category = $this->getCategory($user, $categoriesCount);
            $feed = $this->createQueryFeed($query, 'test'. $raw, $user, $category);

            $manager->persist($query);
            $manager->persist($feed);
            $manager->persist($user);
            $this->addReference('feed_'. $i, $feed);
        }

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager A ObjectManager instance.
     *
     * @return void
     */
    private function loadForTesting(ObjectManager $manager)
    {
        /** @var User $testUser */
        $testUser = $this->getReference('test@email.com');
        /** @var User $masterUser */
        $masterUser = $this->getReference('master@email.com');

        $testUser->useLimit(AppLimitEnum::feeds(), 2);

        $query = $this->createQuery('test1');
        $feed = $this->createQueryFeed($query, 'test1', $testUser, $testUser->getCategories()->first());

        $manager->persist($query);
        $manager->persist($feed);
        $this->addReference('feed_test1', $feed);

        $query = $this->createQuery('test2');
        $feed = $this->createQueryFeed($query, 'test2', $testUser, $testUser->getCategories()->first());

        $manager->persist($query);
        $manager->persist($feed);
        $this->addReference('feed_test2', $feed);

        $masterUser->useLimit(AppLimitEnum::feeds());

        $query = $this->createQuery('test3');
        $feed = $this->createQueryFeed($query, 'test3', $masterUser, $masterUser->getCategories()->first());

        $manager->persist($query);
        $manager->persist($feed);
        $this->addReference('feed_test3', $feed);

        $manager->persist($testUser);
        $manager->persist($masterUser);
        $manager->flush();
    }

    /**
     * @param User    $user  A User entity instance.
     * @param integer $count Total count of categories.
     *
     * @return \CacheBundle\Entity\Category
     */
    private function getCategory(User $user, $count)
    {
        static $i = 0;

        if ($i >= $count) {
            $i = 0;
        }

        return $user->getCategories()[$i++];
    }

    /**
     * @param string $searchString That keyword is used for searching.
     *
     * @return StoredQuery
     */
    private function createQuery($searchString)
    {
        return StoredQuery::create()
            ->setRaw($searchString)
            ->setFields([
                FieldNameEnum::TITLE,
                FieldNameEnum::MAIN,
            ])
            ->setTotalCount($this->getFaker()->randomNumber())
            ->setDate(date_create())
            ->setNormalized($searchString)
            ->setHash($this->getFaker()->md5);
    }

    /**
     * @param StoredQuery $query    A StoredQuery instance.
     * @param string      $name     Feed name.
     * @param User        $user     Feed owner.
     * @param Category    $category In which category place feed.
     *
     * @return AbstractFeed
     */
    private function createQueryFeed(StoredQuery $query, $name, User $user, Category $category)
    {
        return QueryFeed::create()
            ->setPublisherTypes($this->getFaker()
                ->randomElements(PublisherTypeEnum::getAvailables(), random_int(1, 2)))
            ->setQuery($query)
            ->setCategory($category)
            ->setName($name)
            ->setUser($user);
    }
}
