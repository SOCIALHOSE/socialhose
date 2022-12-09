<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use Common\Enum\FieldNameEnum;
use IndexBundle\Model\ArticleDocumentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AtomFeedFormatterStrategy
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 *
 * @link https://validator.w3.org/feed/docs/atom.html
 */
class AtomFeedFormatterStrategy extends AbstractFeedFormatStrategy
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
        $fields[] = FieldNameEnum::SECTION;

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
        $node = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><feed xmlns="http://www.w3.org/2005/Atom"></feed>');

        // Attach feed info.
        $node->addChild('id', $this->generator->generate('app_index_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $node->addChild('title', $feed->getName());
        $node->addChild('updated', date_create()->format('c'));

        $link = $node->addChild('link');
        $link->addAttribute('rel', 'self');
        $link->addAttribute('href', $this->generator->generate('app_index_exportfeed', [
            'format' => $options->getFormat()->getValue(),
            'id' => $feed->getId(),
        ]));

        $textType = $options->isAsPlain() ? 'text' : 'html';

        foreach ($documents as $document) {
            $data = $document->getNormalizedData();

            $item = $node->addChild('entry');
            $item
                ->addChild('title', $data['title'])
                ->addAttribute('type', $textType);

            $link = $item->addChild('link');
            $link->addAttribute('rel', 'alternate');
            $link->addAttribute('href', $data['permalink']);

            if ($options->isShowImages()) {
                $item->addChild('image', $data['image']);
            }

            $author = $item->addChild('author');
            $author->addChild('name', $data['source']['title']);
            $author->addChild('uri', $data['source']['link']);

            $item->addChild('pubDate', $data['published']->format('d M Y H:i:s e'));
            $item
                ->addChild('summary', $this->extract($data['content'], $options, $feed))
                ->addAttribute('type', $textType);
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
