<?php

namespace AdminBundle\Form;

/**
 * Class Search
 * @package AdminBundle\Form
 */
class Search
{

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @return array
     */
    public function getHandledQuery()
    {
        $handledQuery = explode(' ', $this->query);
        if (count($handledQuery) === 1) {
            if (!$handledQuery[0]) {
                $handledQuery = [];
            }
        }

        return $handledQuery;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query Search query.
     *
     * @return Search
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }
}
