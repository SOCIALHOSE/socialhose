<?php

namespace ApiDocBundle\Parser;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Parser\ParserInterface;

/**
 * Class CustomOutputParser
 * Process custom output data.
 *
 * @package Nelmio\ApiDocBundle\Parser
 */
class CustomOutputParser implements ParserInterface
{

    /**
     * @var EntityMetadataParser
     */
    private $metadataParser;

    /**
     * @var PaginationParser
     */
    private $paginationParser;

    /**
     * CustomOutputParser constructor.
     *
     * @param EntityMetadataParser $metadataParser   A EntityMetadataParser
     *                                               instance.
     * @param PaginationParser     $paginationParser A PaginationParser instance.
     */
    public function __construct(
        EntityMetadataParser $metadataParser,
        PaginationParser $paginationParser
    ) {
        $this->metadataParser = $metadataParser;
        $this->paginationParser = $paginationParser;
    }

    /**
     * Return true/false whether this class supports parsing the given class.
     *
     * @param array $item Containing the following fields: class, groups.
     *                    Of which groups is optional.
     *
     * @return boolean
     */
    public function supports(array $item)
    {
        return isset($item['data']);
    }

    /**
     * Returns an array of class property metadata where each item is a key (the property name) and
     * an array of data with the following keys:
     *  - dataType          string
     *  - required          boolean
     *  - description       string
     *  - readonly          boolean
     *  - children          (optional) array of nested property names mapped to arrays
     *                      in the format described here
     *  - class             (optional) the fully-qualified class name of the item, if
     *                      it is represented by an object
     *
     * @param array $item The string type of input to parse.
     *
     * @return array
     */
    public function parse(array $item)
    {
        $result = array_map(function ($annotation) {
            switch (true) {
                //
                // Check that current property supported by EntityMetadataParser.
                //
                case $this->metadataParser->supports($annotation):
                    $shortName = \app\c\getShortName($annotation['class']);

                    $annotation = [
                        'dataType' => $shortName. ' entity',
                        'actualType' => DataTypes::MODEL,
                        'subType' => $annotation['class'],
                        'required' => (isset($annotation['nullable']))
                            ? $annotation['nullable']
                            : false,
                        'readonly' => true,
                        'children' => $this->metadataParser->parse($annotation),
                    ];
                    break;

                //
                // Check that current property supported by PaginatorParser.
                //
                case $this->paginationParser->supports($annotation):
                    $annotation = [
                        'dataType' => 'Paginated data.',
                        'actualType' => DataTypes::MODEL,
                        'required' => true,
                        'readonly' => true,
                        'children' => $this->paginationParser->parse($annotation),
                    ];
                    break;
            }

            return $annotation;
        }, $item['data']);

        return $result;
    }
}
