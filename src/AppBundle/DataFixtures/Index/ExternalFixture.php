<?php

namespace AppBundle\DataFixtures\External;

use AppBundle\DataFixtures\AbstractExternalFixture;
use CacheBundle\Comment\Manager\CommentManagerInterface;
use CacheBundle\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use IndexBundle\Index\External\InternalHoseIndex;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Model\ArticleDocumentInterface;
use IndexBundle\Model\DocumentInterface;
use UserBundle\Entity\User;

/**
 * Class ExternalFixture
 * @package AppBundle\DataFixtures\External
 */
class ExternalFixture extends AbstractExternalFixture
{

    /**
     * Max documents in 'stage' environment
     */
    const MAX = 300;

    /**
     * Load fixtures into index.
     *
     * @param IndexInterface $index A IndexInterface instance.
     *
     * @return void
     */
    public function load(IndexInterface $index)
    {
        if ($this->checkEnvironment('prod')) {
            return;
        }

        if (! $index instanceof InternalHoseIndex) {
            throw new \LogicException(sprintf(
                'External fixtures should be loaded into \'%s\' but \'%s\' given',
                InternalHoseIndex::class,
                get_class($index)
            ));
        }

        $patches = [
            [
                'sequence' => '1',
                'title' => 'Al Kodmani Crime Family stole millions',
                'lang' => 'en',
                'geo_country' => 'US',
                'geo_state' => 'Arizona',
                'geo_city' => 'Amazing City',
                'published' => date_create()->modify('- 10 days')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'CNN',
                'duplicates_count' => 0,
                'image_src' => 'http://lorempixel.com/120/100/',
                'views' => 12012312,
            ],
            [
                'sequence' => '2',
                'title' => 'Amazing cat',
                'lang' => 'en',
                'geo_country' => 'US',
                'geo_state' => 'Maryland',
                'published' => date_create()->modify('- 15 days')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'Asharq Al Awsat',
                'duplicates_count' => 0,
                'image_src' => '',
                'views' => 112,
                'section' => 'Lifestyle',
            ],
            [
                'sequence' => '3',
                'title' => 'More about cats',
                'lang' => 'ru',
                'geo_country' => 'US',
                'geo_state' => 'Louisiana',
                'published' => date_create()->modify('- 10 days')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'AAAE',
                'duplicates_count' => 0,
                'image_src' => null,
                'views' => 10001,
            ],
            [
                'sequence' => '4',
                'title' => 'Cat and dog market',
                'lang' => 'en',
                'geo_country' => 'RU',
                'published' => date_create()->modify('- 1 months')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'Aaj TV',
                'duplicates_count' => 0,
                'image_src' => 'http://lorempixel.com/120/100/',
                'views' => 123,
            ],
            [
                'sequence' => '5',
                'title' => 'Dogs are the best',
                'lang' => 'en',
                'published' => date_create()->modify('- 1 year')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'AACSB',
                'duplicates_count' => 20,
                'image_src' => 'http://lorempixel.com/120/100/',
                'views' => 1123312,
            ],
            [
                'sequence' => '6',
                'title' => 'Fish',
                'lang' => 'af',
                'geo_country' => 'US',
                'geo_state' => 'Maryland',
                'published' => date_create()->modify('- 25 days')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'Armenian Assembly of America',
                'duplicates_count' => 0,
                'image_src' => 'http://lorempixel.com/120/100/',
                'views' => 100237312,
            ],
            [
                'sequence' => '7',
                'title' => 'Cat and fish',
                'lang' => 'en',
                'geo_country' => 'US',
                'geo_state' => 'Arizona',
                'published' => date_create()->modify('- 3 days')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => '4A\'s',
                'duplicates_count' => 10,
                'image_src' => null,
                'views' => 1543312,
                'author_name' => 'Gracie Pfeffer',
                'publisher' => 'msnbc',
            ],
            [
                'sequence' => '8',
                'title' => 'Some',
                'main' => 'Cat',
                'lang' => 'af',
                'geo_country' => 'US',
                'geo_state' => 'Louisiana',
                'published' => date_create()->modify('- 15 minutes')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'Asian American Press',
                'duplicates_count' => 5,
                'image_src' => '',
                'views' => 10312,
            ],
            [
                'sequence' => '9',
                'title' => 'Some',
                'main' => 'Cat',
                'lang' => 'af',
                'geo_country' => 'US',
                'geo_state' => 'Louisiana',
                'published' => date_create()->modify('- 1 hours')->format('Y-m-d\TH:i:s\Z'),
                'source_title' => 'CNN',
                'duplicates_count' => 5,
                'image_src' => '',
                'views' => 10012,
            ],
        ];

        /** @var ArticleDocumentInterface[] $documents */
        $documents = [];
        $max = $this->checkEnvironment('stage') ? self::MAX : count($patches);
        foreach (range(0, $max) as $idx) {
            $document = $this->generator->generate(10 + $idx);

            if (isset($patches[$idx])) {
                $document = $this->applyPatch($document, $patches[$idx]);
            }
            $documents[] = $document;
        }

        $index->index($documents);

        //
        // Some documents we should persist into our database in order to add
        // comments for it.
        //
        /** @var EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $users = [
            $em->getReference(User::class, 1),
            $em->getReference(User::class, 2),
            $em->getReference(User::class, 3),
        ];
        $faker = $this->getFaker();

        if (! $this->checkEnvironment('test')) {
            foreach (range(0, $max, 5) as $idx) {
                $commentsCount = random_int(15, 35);

                $entity = $documents[$idx]->toDocumentEntity()
                    ->setCommentsCount($commentsCount + 1);
                $em->persist($entity);

                foreach (range(0, $commentsCount) as $commentIdx) {
                    $comment = new Comment(
                        $faker->randomElement($users),
                        $faker->realText(),
                        $faker->boolean() ? 'Comment ' . $commentIdx : ''
                    );

                    $comment
                        ->setCreatedAt(date_create()->modify('- ' . ($commentIdx + 1) . ' minutes'))
                        ->setNew($commentIdx < CommentManagerInterface::NEW_COMMENT_POOL_SIZE)
                        ->setDocument($entity);
                    $em->persist($comment);
                }
            }
        }

        $em->flush();
    }

    /**
     * @param DocumentInterface $document A IndexDocumentInterface instance.
     * @param array             $path     Array of patched properties with new values.
     *
     * @return DocumentInterface
     */
    private function applyPatch(DocumentInterface $document, array $path)
    {
        return $document->mapRawData(function (array $data) use ($path) {
            foreach ($path as $name => $value) {
                $data[$name] = $value;
            }

            return $data;
        });
    }
}
