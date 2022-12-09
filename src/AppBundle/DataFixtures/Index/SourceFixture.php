<?php

namespace AppBundle\DataFixtures\Source;

use AppBundle\DataFixtures\AbstractSourceIndexFixture;
use AppBundle\Manager\Source\SourceManagerInterface;
use CacheBundle\Entity\SourceList;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;

/**
 * Class SourceFixture
 * @package AppBundle\DataFixtures\Source
 */
class SourceFixture extends AbstractSourceIndexFixture
{

    /**
     * Maximum created sources.
     */
    const MAX_COUNT = 100;

    /**
     * @param IndexInterface $index A IndexInterface instance.
     *
     * @return void
     */
    public function load(IndexInterface $index)
    {
        if (! $index instanceof SourceIndexInterface) {
            throw new \LogicException(sprintf(
                'External fixtures should be loaded into \'%s\' but \'%s\' given',
                SourceIndexInterface::class,
                get_class($index)
            ));
        }

        if ($this->checkEnvironment('prod')) {
            return;
        }

        // Wait to insure that all external documents was indexed.
        sleep(5);

        /** @var SourceManagerInterface $manager */
        $manager = $this->container->get('app.source_manager');
        $manager->pullFromExternal();

        // Wait to insure that all sources was indexed.
        sleep(5);

        /** @var \Doctrine\ORM\EntityManagerInterface $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $lists = $em->getRepository(SourceList::class)->findAll();
        $response = $index->createRequestBuilder()->setLimit(1000)->build()->execute();

        $min = (int) floor($response->getTotalCount() / 6);
        $max = (int) floor($response->getTotalCount() / 2);

        foreach ($lists as $list) {
            $ids = $this->uniqueRandomIds($response->getDocuments(), mt_rand($min, $max));
            $manager->addSourcesToList($ids, $list->getId());

            // Wait to insure that all changes will be accepted and applied.
            sleep(1);
        }
    }

    /**
     * Fetch unique random ids from sources.
     *
     * @param array   $sources Source array.
     * @param integer $count   How much elements get.
     *
     * @return array
     */
    private function uniqueRandomIds(array $sources, $count)
    {
        $alreadyFetched = [];
        $fetchedCount = 0;
        $sourceCount = count($sources);
        $result = [];

        while ($fetchedCount < $count) {
            $idx = mt_rand(0, $sourceCount - 1);
            if (in_array($idx, $alreadyFetched, true)) {
                continue;
            }

            $alreadyFetched[] = $idx;
            $fetchedCount++;

            $result[] = $sources[$idx]['id'];
        }

        return $result;
    }
}
