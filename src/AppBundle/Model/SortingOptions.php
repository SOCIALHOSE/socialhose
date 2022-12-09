<?php

namespace AppBundle\Model;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class SortingOptions
 *
 * @package AppBundle\Model
 */
class SortingOptions implements \JsonSerializable
{

    /**
     * Sorted field name.
     *
     * @var string
     */
    private $fieldName;

    /**
     * Sort direction.
     *
     * @var string
     */
    private $sortDirection;

    /**
     * SortingOptions constructor.
     *
     * @param string $fieldName        Sorted field name.
     * @param string $sortDirection    A SortDirectionEnum instance.
     * @param string $defaultFieldName Default field name.
     */
    public function __construct(
        $fieldName,
        $sortDirection,
        $defaultFieldName
    ) {
        $this->fieldName = trim($fieldName);
        if ($this->fieldName === '') {
            $this->fieldName = trim($defaultFieldName);
        }

        $sortDirection = strtolower(trim($sortDirection));
        if (($sortDirection !== 'asc') && ($sortDirection !== 'desc')) {
            throw new \InvalidArgumentException('\'$sortDirection\' should be \'asc\' or \'desc\'');
        }

        $this->sortDirection = $sortDirection;
    }

    /**
     * Create instance from request.
     *
     * @param Request $request          A Request instance.
     * @param string  $defaultFieldName Default field name.
     *
     * @return SortingOptions
     */
    public static function fromRequest(Request $request, $defaultFieldName)
    {
        $sortField = $request->query->get('sortField');
        $sortDirection = $request->query->get('sortDirection', 'asc');

        return new static($sortField, $sortDirection, $defaultFieldName);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return mixed data which can be serialized by json_encode, which is a value
     * of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return [
            'field' => $this->fieldName,
            'direction' => $this->sortDirection,
        ];
    }
}
