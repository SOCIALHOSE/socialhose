<?php

namespace ApiDocBundle\Parser;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Parser\ParserInterface;

/**
 * Class ArrayParser
 * Process array of data.
 *
 * @package Nelmio\ApiDocBundle\Parser
 */
class ArrayParser implements ParserInterface
{

    /**
     * @var EntityMetadataParser
     */
    protected $parser;

    /**
     * PaginationParser constructor.
     *
     * @param EntityMetadataParser $parser A EntityMetadataParser instance.
     */
    public function __construct(EntityMetadataParser $parser)
    {
        $this->parser = $parser;
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
        if (! isset($item['class'], $item['groups'])
            || strpos($item['class'], 'Array') === false) {
            return false;
        }

        return $this->parser->supports([
            'class' => $this->getInnerClass($item['class']),
            'groups' => $item['groups'],
        ]);
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
        $innerClass = $this->getInnerClass($item['class']);

        return [
            '' => [
                'description' => 'Requested entities.',
                'dataType' => 'Collection of '. \app\c\getShortName($innerClass),
                'actualType' => DataTypes::COLLECTION,
                'subType' => $innerClass,
                'required' => true,
                'readonly' => true,
                'children' => $this->parser->parse([
                    'class' => $innerClass,
                    'groups' => $item['groups'],
                ]),
            ],
        ];
    }

    /**
     * @param string $class Class field from $item.
     *
     * @return string
     */
    private function getInnerClass($class)
    {
        $openPos = strpos($class, '<');
        $closePos = strrpos($class, '>');

        if (($openPos === false) || ($closePos === false)) {
            return '';
        }

        return substr($class, $openPos + 1, $closePos - $openPos - 1);
    }
}
