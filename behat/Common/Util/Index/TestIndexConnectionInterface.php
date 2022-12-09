<?php

namespace Common\Util\Index;

use IndexBundle\Index\External\ExternalIndexInterface;
use IndexBundle\Index\IndexInterface;
use IndexBundle\Index\Internal\InternalIndexInterface;
use IndexBundle\Index\Source\SourceIndexInterface;
use IndexBundle\Model\DocumentInterface;

/**
 * Interface TestIndexConnectionInterface
 *
 * @package Common\Util\Index
 */
interface TestIndexConnectionInterface extends InternalIndexInterface
{

    /**
     * Setup index with mappings.
     *
     * @return void
     */
    public function setup();

    /**
     * Create new document for this index.
     *
     * @return DocumentInterface
     */
    public function createDocument();

    /**
     * Create new index.
     *
     * @param array $mapping  Index mapping.
     * @param array $settings Index settings.
     *
     * @return void
     */
    public function createIndex(array $mapping, array $settings = []);

    /**
     * Index given document or array of documents.
     *
     * @param DocumentInterface|DocumentInterface[] $data DocumentInterface instance
     *                                                    or array of instances.
     *
     * @return void
     */
    public function index($data);

    /**
     * Purge index.
     *
     * @return void
     */
    public function purge();

    /**
     * @return IndexInterface|InternalIndexInterface|ExternalIndexInterface|SourceIndexInterface
     */
    public function getIndex();
}
