<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use Common\Enum\FieldNameEnum;
use IndexBundle\Model\ArticleDocumentInterface;

/**
 * Class TsvFeedFormatterStrategy
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 *
 * @link http://www.iana.org/assignments/media-types/text/tab-separated-values
 */
class TsvFeedFormatterStrategy extends AbstractFeedFormatStrategy
{

    /**
     * TSV column titles.
     *
     * @var string[]
     */
    private static $columns = [
        'Link',
        'Headline Text',
        'Source Name',
        'Source URL',
        'Harvest Time',
        'Extractor Author',
        'Content',
    ];

    /**
     * Return list of required document fields.
     *
     * @param FormatterOptions $options Formatter options.
     *
     * @return string[]
     */
    public function requiredFields(FormatterOptions $options)
    {
        $fields = parent::requiredFields($options);
        $fields[] = FieldNameEnum::PERMALINK;
        $fields[] = FieldNameEnum::SOURCE_TITLE;
        $fields[] = FieldNameEnum::SOURCE_LINK;
        $fields[] = FieldNameEnum::PUBLISHED;
        $fields[] = FieldNameEnum::AUTHOR_NAME;

        return $fields;
    }

    /**
     * Serialize feed.
     *
     * @param AbstractFeed                     $feed      A serialized feed entity
     *                                                    instance.
     * @param ArticleDocumentInterface[]|array $documents Array of fetched documents
     *                                                    which should by serialized.
     * @param FormatterOptions                 $options   Formatter options.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function serialize(
        AbstractFeed $feed,
        array $documents,
        FormatterOptions $options
    ) {
        $body = implode("\t", self::$columns) . PHP_EOL;

        $processor = \nspl\f\compose(
            \nspl\f\partial('implode', "\t"),
            function (ArticleDocumentInterface $document) use ($options, $feed) {
                $data = $document->getNormalizedData();

                $date = $data['published'];
                if (! $date instanceof \DateTimeInterface) {
                    $date = date_create();
                }

                return [
                    $data['permalink'],
                    $data['title'],
                    $data['source']['title'],
                    $data['source']['link'],
                    $date->format('Y-m-d H:i:s'),
                    $data['author']['name'],
                    $this->extract($data['content'], $options, $feed),
                ];
            }
        );
        $lines = \nspl\a\map($processor, $documents);

        return $body . implode(PHP_EOL, $lines);
    }

    /**
     * Get format mime type.
     *
     * @return string
     */
    public function getMime()
    {
        //
        // TSV has own mime type: 'text/tab-separated-values' but response with
        // this mime got strange encoding so we use 'text/plain' instead.
        //
        return 'text/plain';
    }
}
