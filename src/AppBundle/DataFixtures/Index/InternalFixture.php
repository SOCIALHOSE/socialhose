<?php
namespace AppBundle\DataFixtures\Internal;

use AppBundle\DataFixtures\AbstractInternalIndexFixture;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Model\Generator\InternalDocumentGenerator;

/**
 * Class InternalFixture
 * @package AppBundle\DataFixtures\Internal
 */
class InternalFixture extends AbstractInternalIndexFixture
{
    /**
     * @param IndexInterface $index A IndexInterface instance.
     *
     * @return void
     */
    public function load(IndexInterface $index)
    {
        if (! $index instanceof InternalIndexInterface) {
            throw new \LogicException(sprintf(
                'External fixtures should be loaded into \'%s\' but \'%s\' given',
                InternalIndexInterface::class,
                get_class($index)
            ));
        }

        if (! $this->checkEnvironment([ 'dev', 'test' ])) {
            return;
        }

        $documentManager = new InternalDocumentGenerator();

        $documents = [];
        for ($i = 0; $i < 100; ++$i) {
            $document = $documentManager->generate();
            $document['sequence'] = $i;
            $document['title'] = 'About cat '.$i;
            $documents[] = $document;
        }

        $index->index($documents);
    }


    /**
     * Return index type for this fixture.
     *
     * @return string
     */
    public function getIndex()
    {
        return self::INDEX_INTERNAL;
    }
}
