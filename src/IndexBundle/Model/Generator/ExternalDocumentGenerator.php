<?php

namespace IndexBundle\Model\Generator;

use Common\Enum\CountryEnum;
use Common\Enum\LanguageEnum;
use Common\Enum\PublisherTypeEnum;
use Common\Enum\StateEnum;
use IndexBundle\Index\Strategy\HoseIndexStrategy;
use IndexBundle\Model\ArticleDocument;

/**
 * Class ExternalDocumentGenerator
 * @package IndexBundle\Model\Generator
 */
class ExternalDocumentGenerator extends AbstractDocumentGenerator
{

    /**
     * Path to image placeholder service.
     */
    const REAL_IMG_URL = 'http://lorempixel.com/%d/%d/';

    /**
     * Available image heights
     *
     * @var array
     */
    private static $heights = [ 600, 800, 1024 ];

    /**
     * Available image widths
     *
     * @var array
     */
    private static $widths = [ 600, 800, 1024 ];

    /**
     * @var HoseIndexStrategy
     */
    private $strategy;

    /**
     * ExternalDocumentGenerator constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->strategy = new HoseIndexStrategy();
    }

    /**
     * Generate external document.
     *
     * @param integer $id Document id.
     *
     * @return ArticleDocument
     */
    public function generate($id = null)
    {
        $content = $this->faker->realText(400);
        $summary = substr($content, 0, 50) .'...';
        $dateFound = date_create()->format('c');

        $title = $this->faker->realText(20);
        $publisher = $this->faker->randomElement(self::$publisherNames);

        $sourceUrl = "http://{$this->faker->domainName}/";
        $documentUrl = "{$sourceUrl}{$this->faker->slug}.html";

        $publisherType = $this->faker->randomElement(PublisherTypeEnum::getAvailables());
        $country = $this->faker->randomElement(CountryEnum::getAvailables());
        $state = $this->faker->randomElement(StateEnum::getAvailables());
        $city = $this->faker->randomElement(self::$cityNames);

        $data = [
            // To insure that sequence will be unique number.
            'sequence' => $id === null ?: date_create()->getTimestamp() + $this->faker->randomNumber(8),
            'date_found' => $dateFound,
            'source_hashcode' => md5($sourceUrl . date_create()->getTimestamp()),
            'source_link' => $sourceUrl,
            'source_publisher_type' => $publisherType,
            'source_publisher_subtype' => $this->faker->domainName,
            'source_date_found' => $dateFound,
            'source_title' => $publisher,
            'source_description' => $this->faker->text(50),
            'source_location' => "{$country}, {$state}, {$city}",
            'permalink' => $documentUrl,
            'main' => $content,
            'main_length' => strlen($content),
            'summary_text' => $summary,
            'title' => $title,
            'publisher' => $publisher,
            'section' => $this->faker->randomElement(self::$sectionNames),
            'tags' => $this->faker->randomElements(self::$tags, random_int(2, 4)),
            'links' => $this->randomLinks(),
            'published' => $this->faker->dateTimeBetween('- 1 months')->format('c'),
            'author_name' => $this->faker->randomElement(self::$authorNames),
            'author_link' => $this->faker->url,
            'author_gender' => $this->faker->randomElement(['M', 'W']),
            'geo_country' => $country,
            'geo_state' => $state,
            'geo_city' => $city,
            'geo_point' => $this->faker->latitude .', '. $this->faker->longitude,
            'image_src' => $this->realImageUrl(),
            'sentiment' => $this->faker->randomElement(self::$sentimentTypes),
            'lang' => $this->faker->randomElement(LanguageEnum::getAvailables()),
            'duplicates_count' => $this->faker->numberBetween(0, 50),
            'views' => $this->faker->numberBetween(0, 11000000),
            'shares' => $this->faker->randomNumber(8),
        ];

        return new ArticleDocument($this->strategy, $data);
    }

    /**
     * @return array
     */
    private function randomLinks()
    {
        $count = random_int(1, 4);

        $links = [];
        for ($i = 0; $i < $count; ++$i) {
            $links[] = $this->faker->url;
        }

        return $links;
    }

    /**
     * @return string
     */
    private function realImageUrl()
    {
        if ($this->faker->boolean()) {
            return sprintf(
                self::REAL_IMG_URL,
                $this->faker->randomElement(self::$heights),
                $this->faker->randomElement(self::$widths)
            );
        }

        return '';
    }
}
