<?php

namespace CacheBundle\Document\Extractor;

use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Interface DocumentContentExtractorInterface
 *
 * @package CacheBundle\Document\Extractor
 */
interface DocumentContentExtractorInterface
{

    /**
     * @param string                 $content   The document contents.
     * @param string                 $query     Search query.
     * @param ThemeOptionExtractEnum $extract   Extract type.
     * @param boolean                $highlight Should highlight matched keywords
     *                                          or not.
     *
     * @return ExtractionResult
     */
    public function extract(
        $content,
        $query,
        ThemeOptionExtractEnum $extract,
        $highlight = false
    );
}
