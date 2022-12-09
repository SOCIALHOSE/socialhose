<?php

namespace IndexBundle\Aggregation\Aggregations;

use IndexBundle\Aggregation\AbstractAggregationType;
use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Class TermsAggregation
 * @package IndexBundle\Aggregation\Aggregations
 */
class TermsAggregation extends AbstractAggregationType
{

    /**
     * @param integer $size Terms size.
     *
     * @return TermsAggregation
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Resolve given aggregation into proper index format.
     *
     * @param AggregationTypeResolverInterface $resolver A AggregationTypeResolverInterface
     *                                                   instance.
     *
     * @return mixed
     */
    public function resolve(AggregationTypeResolverInterface $resolver)
    {
        return $resolver->terms($this->fieldName, $this->size);
    }
}
