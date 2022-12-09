<?php

namespace IndexBundle\Util\Initializer;

use IndexBundle\Index\External\HoseIndex;
use IndexBundle\Index\Internal\InternalIndexInterface;

/**
 * Class ExternalIndexInitializer
 * @package IndexBundle\Util\Initializer
 */
class ExternalIndexInitializer extends AbstractIndexInitializer
{

    /**
     * Initialize index mapping.
     *
     * @return void
     */
    public function initializeIndex()
    {
        if ((! $this->index instanceof HoseIndex) && ($this->index instanceof InternalIndexInterface)) {
            $path = __DIR__ . '/../../../../hose_external_schema.json';
            $config = json_decode(file_get_contents($path), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException(
                    'External index setup: ' . json_last_error_msg()
                );
            }

            $this->index->createIndex(
                $config['mappings']['simple_document']['properties'],
                $config['settings']
            );
        } else {
            throw new \LogicException(sprintf(
                'Can\'t initialize external index for \'%s\'',
                get_class($this->index)
            ));
        }
    }
}
