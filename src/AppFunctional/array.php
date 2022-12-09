<?php

namespace app\a;

use nspl\args;

/**
 * @param array|\Traversable $sequence Array of items.
 * @param string             $class    Required checked class fqcn.
 *
 * @return boolean
 */
// @codingStandardsIgnoreStart
function allInstanceOf($sequence, $class)
{
// @codingStandardsIgnoreEnd
    return \nspl\a\all($sequence, \nspl\f\rpartial(\app\op\isInstanceOf, $class));
}

/**
 * Groups a collection by index returned by callback.
 *
 * @param callable           $callback Function which returns indices.
 * @param array|\Traversable $sequence Grouped sequence.
 *
 * @return array
 */
// @codingStandardsIgnoreStart
function group(callable $callback, $sequence)
{
// @codingStandardsIgnoreEnd
    args\expects(args\traversable, $sequence);

    $groups = [];

    foreach ($sequence as $index => $element) {
        $groupKey = $callback($element, $index, $sequence);

        if (!isset($groups[$groupKey])) {
            $groups[$groupKey] = array();
        }

        $groups[$groupKey][$index] = $element;
    }

    return $groups;
}

/**
 * Looks through each element in the list, returning an array of all the elements
 * that pass a truthy test (callback).
 *
 * @param callable           $callback Callback used for selecting elements.
 * @param array|\Traversable $sequence Traversable collection of items.
 *
 * @return array
 */
// @codingStandardsIgnoreStart
function select(callable $callback, $sequence)
{
// @codingStandardsIgnoreEnd
    args\expects(args\traversable, $sequence);

    $aggregation = array();

    foreach ($sequence as $index => $element) {
        if ($callback($element, $index, $sequence)) {
            $aggregation[$index] = $element;
        }
    }

    return $aggregation;
}

/**
 * Binary search.
 * Specified collection should be already sorted in ascending order.
 *
 * @param \Traversable|array $sequence A collection.
 * @param mixed              $searched Searched value.
 * @param callable|string    $callback Number of elements pop from collection.
 *                                     By default try to get 'id' property.
 *
 * @return integer|false Searched item index or false if can't find.
 */
// @codingStandardsIgnoreStart
function binarySearch($sequence, $searched, $callback = null)
{
// @codingStandardsIgnoreEnd

    args\expects(args\traversable, $sequence);

    if (is_string($callback)) {
        $callback = \nspl\op\propertyGetter($callback);
    }

    $search = function ($start, $end) use ($sequence, $callback, $searched, &$search) {
        if ($start > $end) {
            return false;
        }

        $middle = $start + (int) floor(($end - $start) / 2);

        $value = $sequence[$middle];
        if ($callback) {
            $value = $callback($value);
        }

        if ($searched < $value) {
            return $search($start, $middle - 1);
        } elseif ($searched > $value) {
            return $search($middle + 1, $end);
        }

        return $middle;
    };

    // Make first call with initial bounds.

    $start = 0;
    $end = count($sequence) - 1;
    return $search($start, $end);
}
