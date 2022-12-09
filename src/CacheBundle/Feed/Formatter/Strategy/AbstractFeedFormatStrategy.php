<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use Common\Enum\FieldNameEnum;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class AbstractFeedFormatStrategy
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 */
abstract class AbstractFeedFormatStrategy implements FeedFormatterStrategyInterface
{

    /**
     * @var DocumentContentExtractorInterface
     */
    private $extractor;

    /**
     * AbstractFeedFormatStrategy constructor.
     *
     * @param DocumentContentExtractorInterface $extractor A DocumentContentExtractorInterface
     *                                                     instance.
     */
    public function __construct(DocumentContentExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * Return list of required document fields.
     *
     * @param FormatterOptions $options Formatter options.
     *
     * @return string[]
     */
    public function requiredFields(FormatterOptions $options)
    {
        $fields = [];

        if (! $options->getExtract()->is(ThemeOptionExtractEnum::NO)) {
            $fields[] = FieldNameEnum::MAIN;
        }

        return $fields;
    }

    /**
     * @param string           $content Document content.
     * @param FormatterOptions $options FormatterOptions.
     * @param AbstractFeed     $feed    A serialized feed entity instance.
     *
     * @return string
     */
    protected function extract($content, FormatterOptions $options, AbstractFeed $feed)
    {
        $extract = $options->getExtract();

        //
        // We should get normalized search query only if it requested.
        //
        $query = '';
        if (! $extract->is(ThemeOptionExtractEnum::no())) {
            if ($feed instanceof QueryFeed) {
                $query = $feed->getQuery()->getNormalized();
            }
        }

        $result = $this->extractor->extract($content, $query, $extract);

        return $result->getText() . ($result->getLength() > 0 ? '...' : '');
    }
}
