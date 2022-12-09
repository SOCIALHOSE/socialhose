<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use Common\Enum\FieldNameEnum;
use IndexBundle\Model\ArticleDocumentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RssFeedFormatterStrategy
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 *
 * @link https://validator.w3.org/feed/docs/rss2.html
 */
class RssFeedFormatterStrategy extends AbstractFeedFormatStrategy
{

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * AtomFeedFormatterStrategy constructor.
     *
     * @param DocumentContentExtractorInterface $extractor A
     *                                                     DocumentContentExtractorInterface
     *                                                     instance.
     * @param UrlGeneratorInterface             $generator A UrlGeneratorInterface
     *                                                     instance.
     */
    public function __construct(
        DocumentContentExtractorInterface $extractor,
        UrlGeneratorInterface $generator
    ) {
        parent::__construct($extractor);

        $this->generator = $generator;
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
        $fields = parent::requiredFields($options);
        $fields[] = FieldNameEnum::TITLE;
        $fields[] = FieldNameEnum::PERMALINK;
        $fields[] = FieldNameEnum::SOURCE_TITLE;
        $fields[] = FieldNameEnum::SOURCE_LINK;
        $fields[] = FieldNameEnum::PUBLISHED;

        if ($options->isShowImages()) {
            $fields[] = FieldNameEnum::IMAGE_SRC;
        }

        return $fields;
    }

    /**
     * Serialize feed.
     *
     * @param AbstractFeed               $feed      A serialized feed entity
     *                                              instance.
     * @param ArticleDocumentInterface[] $documents Array of fetched documents
     *                                              which should by serialized.
     * @param FormatterOptions           $options   Formatter options.
     *
     * @return mixed
     */
    public function serialize(
        AbstractFeed $feed,
        array $documents,
        FormatterOptions $options
    ) {
        $node = new \SimpleXMLElement('<rss version="2.0"></rss>');

        // Attach channel info.
        $channel = $node->addChild('channel');
        $channel->addChild('title', $feed->getName());
        $channel->addChild('link', $this->generator->generate('app_index_index', [], UrlGeneratorInterface::ABSOLUTE_URL));

        foreach ($documents as $document) {
            $data = $document->getNormalizedData();

            $item = $channel->addChild('item');
            $item
                ->addChild('title', $data['title'])
                ->addAttribute('url', $data['permalink']);
            if ($options->isShowImages()) {
                $item->addChild('image', $data['image']);
            }
            $item->addChild('description', $data['content']);
            $item
                ->addChild('source', $data['source']['title'])
                ->addAttribute('url', $data['source']['link']);
            $item->addChild('pubDate', $data['published']->format('d M Y H:i:s e'));
        }

        return $node->asXML();
    }

    /**
     * Get format mime type.
     *
     * @return string
     */
    public function getMime()
    {
        return 'text/xml';
    }
}
