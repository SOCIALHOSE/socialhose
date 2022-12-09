<?php

namespace CacheBundle\Feed\Formatter;

use AppBundle\Manager\Feed\FeedManagerInterface;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\Strategy\FeedFormatterStrategyInterface;
use Common\Enum\FieldNameEnum;
use Common\Enum\FormatNameEnum;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BasicFeedFormatter
 *
 * @package CacheBundle\Feed\Formatter
 */
class BasicFeedFormatter implements FeedFormatterInterface
{

    /**
     * @var FeedManagerInterface
     */
    private $feedManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * BasicFeedFormatter constructor.
     *
     * @param FeedManagerInterface $feedManager A FeedManagerInterface instance.
     * @param ContainerInterface   $container   A ContainerInterface instance.
     */
    public function __construct(
        FeedManagerInterface $feedManager,
        ContainerInterface $container
    ) {
        $this->feedManager = $feedManager;
        $this->container = $container;
    }

    /**
     * Format feed documents.
     *
     * @param AbstractFeed     $feed    A formatted feed entity instance.
     * @param FormatterOptions $options Used format options.
     *
     * @return FormattedData
     */
    public function formatFeed(AbstractFeed $feed, FormatterOptions $options)
    {
        $strategy = $this->createStrategy($options->getFormat());

        $filterFactory = $this->feedManager->getIndex()->getFilterFactory();

        $sourceFields = $strategy->requiredFields($options);
        $sourceFields[] = FieldNameEnum::SEQUENCE;
        $sourceFields[] = FieldNameEnum::COLLECTION_ID;
        $sourceFields[] = FieldNameEnum::COLLECTION_TYPE;

        $documents = $this->feedManager->getIndex()->createRequestBuilder()
            ->setFilters($filterFactory->andX([
                $filterFactory->eq(FieldNameEnum::COLLECTION_ID, $feed->getCollectionId()),
                $filterFactory->eq(FieldNameEnum::COLLECTION_TYPE, $feed->getCollectionType()),
            ]))
            ->setSources($sourceFields)
            ->setLimit($options->getNumberOfDocuments())
            ->setSorts([ FieldNameEnum::PUBLISHED => 'desc' ])
            ->build()
            ->execute()
            ->getDocuments();

        return new FormattedData(
            $strategy->serialize($feed, $documents, $options),
            $strategy->getMime()
        );
    }

    /**
     * Create strategy for specified format.
     *
     * @param FormatNameEnum $format Used format name.
     *
     * @return FeedFormatterStrategyInterface
     */
    private function createStrategy(FormatNameEnum $format)
    {
        $name = 'cache.feed_formatter_strategy.'. strtolower($format->getValue());

        if (! $this->container->has($name)) {
            throw new \InvalidArgumentException('Unknown format '. $format->getValue());
        }

        $strategy = $this->container->get($name);

        if (! $strategy instanceof FeedFormatterStrategyInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Feed formatter strategy should implements %s interface.',
                FeedFormatterStrategyInterface::class
            ));
        }

        return $strategy;
    }
}
