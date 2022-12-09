<?php

namespace IndexBundle\Model;

use CacheBundle\Entity\Document;

/**
 * Interface ArticleDocumentInterface
 *
 * @package IndexBundle\Model
 */
interface ArticleDocumentInterface extends DocumentInterface
{

    const PLATFORM_hose = 'hose';

    /**
     * Get document id.
     *
     * @return string
     */
    public function getId();

    /**
     * Get platform from which we get this document.
     *
     * @return string
     */
    public function getPlatform();

    /**
     * Create proper source document instance from this article document.
     *
     * @return array
     */
    public function toSourceDocumentData();

    /**
     * @return Document
     */
    public function toDocumentEntity();
}
