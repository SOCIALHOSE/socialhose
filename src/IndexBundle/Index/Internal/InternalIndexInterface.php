<?php

namespace IndexBundle\Index\Internal;

use IndexBundle\Index\IndexInterface;
use IndexBundle\Model\DocumentInterface;
use IndexBundle\SearchRequest\SearchRequestInterface;

/**
 * Interface InternalIndexInterface
 *
 * Internal index interface used for caching and storing documents.
 *
 * @package IndexBundle\Index\Internal
 */
interface InternalIndexInterface extends IndexInterface
{

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
     * Update specified document.
     *
     * Make partial update so in data must be placed only changed properties.
     *
     * @param string|integer $id   Updated document id.
     * @param array          $data Array of changed data where key is property
     *                             name and value is new property value.
     *
     * @return void
     */
    public function update($id, array $data);

    /**
     * Update array of documents.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param array $config Array of arrays where key is updated document id and
     *                      value is array of updated fields same as $data in
     *                      `update` method.
     *
     * @return void
     */
    public function updateBulk(array $config);

    /**
     * Update array of documents with filtering.
     *
     * Make partial update so for each document id we should place only changed
     * property.
     *
     * @param SearchRequestInterface $request A SearchRequestInterface instance.
     * @param string                 $script  Updating script.
     * @param array                  $params  Script parameters.
     *
     * @return void
     */
    public function updateByQuery(SearchRequestInterface $request, $script, array $params = []);

    /**
     * Purge index.
     *
     * @return void
     */
    public function purge();

    /**
     * Remove document by specified id or array of ids.
     *
     * @param string|string[] $id Document id or array of document ids.
     *
     * @return void
     */
    public function remove($id);
}
