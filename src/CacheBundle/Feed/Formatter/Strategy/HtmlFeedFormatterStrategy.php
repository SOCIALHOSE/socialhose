<?php

namespace CacheBundle\Feed\Formatter\Strategy;

use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FormatterOptions;
use Common\Enum\FieldNameEnum;
use IndexBundle\Model\ArticleDocumentInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * Class HtmlFeedFormatterStrategy
 *
 * @package CacheBundle\Feed\Formatter\Strategy
 */
class HtmlFeedFormatterStrategy extends AbstractFeedFormatStrategy
{

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * HtmlFeedFormatterStrategy constructor.
     *
     * @param DocumentContentExtractorInterface $extractor  A DocumentContentExtractorInterface
     *                                                      instance.
     * @param EngineInterface                   $templating A templating EngineInterface
     *                                                      instance.
     */
    public function __construct(
        DocumentContentExtractorInterface $extractor,
        EngineInterface $templating
    ) {
        parent::__construct($extractor);

        $this->templating = $templating;
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
        $fields[] = FieldNameEnum::PERMALINK;
        $fields[] = FieldNameEnum::SOURCE_TITLE;
        $fields[] = FieldNameEnum::SOURCE_LINK;
        $fields[] = FieldNameEnum::PUBLISHED;
        $fields[] = FieldNameEnum::AUTHOR_NAME;
        $fields[] = FieldNameEnum::TITLE;

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
        $data = \nspl\a\map(function (ArticleDocumentInterface $document) use ($options, $feed) {
            $data = $document->getNormalizedData();

            $data['content'] = $this->extract($data['content'], $options, $feed);

            return $data;
        }, $documents);

        return $this->templating->render('CacheBundle::feed.html.twig', [
            'feed' => $feed,
            'data' => $data,
        ]);
    }

    /**
     * Get format mime type.
     *
     * @return string
     */
    public function getMime()
    {
        return 'text/html';
    }
}
