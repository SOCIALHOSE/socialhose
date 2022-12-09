<?php

namespace IndexBundle\Normalizer\Query;

/**
 * Interface QueryNormalizerInterface
 *
 * Normalize raw query search string.
 *
 * @package IndexBundle\Normalizer\Query
 */
interface QueryNormalizerInterface
{

    /**
     * Normalize raw search query.
     *
     * @param string $query Raw search query.
     *
     * @return string
     */
    public function normalize($query);
}
