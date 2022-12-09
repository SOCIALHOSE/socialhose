<?php

namespace IndexBundle\Util\Initializer;

use IndexBundle\Index\Source\SourceIndexInterface;

/**
 * Class SourceIndexInitializer
 * @package IndexBundle\Util\Initializer
 */
class SourceIndexInitializer extends AbstractIndexInitializer
{

    /**
     * Initialize index mapping.
     *
     * @return void
     */
    public function initializeIndex()
    {
        if ($this->index instanceof SourceIndexInterface) {
            $this->index->createIndex([
                'title' => [
                    'type' => 'text',
                    'fields' => ['raw' => ['type' => 'keyword']],
                ],
                'url' => [
                    'type' => 'text',
                    'fields' => ['raw' => ['type' => 'keyword']],
                ],
                'country' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'city' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'state' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'section' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'lang' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'deleted' => ['type' => 'boolean'],
                'type' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'source_publisher_type' => [
                    'type' => 'keyword',
                    'norms' => false,
                ],
                'listIds' => ['type' => 'integer'],
            ], [
                'number_of_shards' => 4,
                'index.store.type' => 'mmapfs',
            ]);
        } else {
            throw new \LogicException('Can\'t initialize source index for '. get_class($this->index));
        }
    }
}
