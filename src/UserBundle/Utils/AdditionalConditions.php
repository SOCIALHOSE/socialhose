<?php

namespace UserBundle\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Parameter;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdditionalConditions
 *
 * @package UserBundle\Utils
 */
class AdditionalConditions
{

    /**
     * @var array
     */
    private $included;

    /**
     * @var array
     */
    private $excluded;

    /**
     * AdditionalConditions constructor.
     *
     * @param array $included Array of entities ids which must be included.
     * @param array $excluded Array of entities ids which must be excluded.
     */
    public function __construct(array $included, array $excluded)
    {
        $this->included = $included;
        $this->excluded = $excluded;
    }

    /**
     * @param Request $request A HTTP Request instance.
     *
     * @return static
     */
    public static function fromRequest(Request $request)
    {
        $included = array_filter(\nspl\a\map('trim', explode(',', trim($request->query->get('include')))));
        $excluded = array_filter(\nspl\a\map('trim', explode(',', trim($request->query->get('exclude')))));

        // @codingStandardsIgnoreStart
        return new static($included, $excluded);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @param ArrayCollection $parameters Array of conditions parameters.
     *
     * @return array|ArrayCollection
     */
    public function addToParameters(ArrayCollection $parameters)
    {
        if (count($this->included) > 0) {
            $parameters[] = new Parameter('included', $this->included);
        }

        if (count($this->excluded) > 0) {
            $parameters[] = new Parameter('excluded', $this->excluded);
        }

        return $parameters;
    }

    /**
     * @param Expr\Base $condition A expression condition.
     * @param string    $alias     Used entity alias.
     *
     * @return Expr\Base
     */
    public function addToConditions(Expr\Base $condition, $alias)
    {
        $expr = new Expr();

        if (count($this->included) > 0) {
            //
            // If we got additional included person recipients we should
            // transform condition and fetch all person enrolled in specified
            // group or in provided list.
            //
            $condition = $expr->orX(
                $condition,
                $expr->in($alias. '.id', ':included')
            );
        }

        if (count($this->excluded) > 0) {
            //
            // If we should exclude some person recipients we should
            // transform condition and fetch all person enrolled in specified
            // group (or in additionally provided 'include' list) but not
            // exists in 'exclude' list.
            //
            $condition = $expr->andX(
                $condition,
                $expr->notIn($alias. '.id', ':excluded')
            );
        }

        return $condition;
    }
}
