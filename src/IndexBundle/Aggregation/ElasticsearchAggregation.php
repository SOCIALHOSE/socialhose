<?php

namespace IndexBundle\Aggregation;

use IndexBundle\Aggregation\Resolver\AggregationTypeResolverInterface;

/**
 * Class Aggregation
 * @package IndexBundle\Aggregation
 */
class ElasticsearchAggregation implements AggregationInterface
{

    /**
     * @var AggregationInterface[]
     */
    protected $aggregations = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var AggregationTypeInterface
     */
    protected $type;

    /**
     * @param AggregationTypeInterface $type A AggregationTypeInterface instance.
     * @param string                   $name Aggregation name.
     */
    public function __construct(AggregationTypeInterface $type, $name)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Overwrite existence aggregations
     *
     * @param AggregationInterface|AggregationInterface[] $aggregations An AggregationInterface instance
     *                                                                  or array of
     *                                                                  AggregationInterface instances.
     *
     * @return AggregationInterface
     */
    public function setAggregations($aggregations)
    {
        if ($aggregations instanceof AggregationInterface) {
            $aggregations = [ $aggregations ];
        }
        $this->aggregations = $aggregations;

        return $this;
    }

    /**
     * Add new aggregation
     *
     * @param AggregationInterface $aggregation A AggregationInterface instance.
     *
     * @return AggregationInterface
     */
    public function addAggregation(AggregationInterface $aggregation)
    {
        $this->aggregations[] = $aggregation;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return AggregationInterface[]
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Resolve aggregation to need format to query.
     *
     * @param AggregationTypeResolverInterface $resolver A AggregationTypeResolverInterface
     *                                                   instance.
     *
     * @return mixed
     */
    public function build(AggregationTypeResolverInterface $resolver)
    {
        $aggregation = [];
        $aggregation[$this->name] = $this->type->resolve($resolver);

        $subAggr = [];
        foreach ($this->aggregations as $value) {
            if ($value instanceof AggregationInterface) {
                $subAggr = array_merge($subAggr, $value->build($resolver));
            }
        }

        if (count($subAggr) > 0) {
            $aggregation[$this->name]['aggs'] = $subAggr;
        }

        return $aggregation;
    }
}
