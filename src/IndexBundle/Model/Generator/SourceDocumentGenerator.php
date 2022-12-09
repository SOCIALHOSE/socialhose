<?php

namespace IndexBundle\Model\Generator;

use Common\Enum\CountryEnum;
use Common\Enum\LanguageEnum;
use Common\Enum\PublisherTypeEnum;
use Common\Enum\StateEnum;
use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Index\Strategy\SourceIndexStrategy;
use IndexBundle\Model\SourceDocument;

/**
 * Class SourceDocumentGenerator
 * @package IndexBundle\Model\Generator
 */
class SourceDocumentGenerator extends AbstractDocumentGenerator
{

    /**
     * @var IndexStrategyInterface
     */
    private $strategy;

    /**
     * SourceDocumentGenerator constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->strategy = new SourceIndexStrategy();
    }

    /**
     * Generate external document.
     *
     * @return SourceDocument
     */
    public function generate()
    {
        return new SourceDocument($this->strategy, [
            'title' => $this->faker->randomElement(self::$publisherNames),
            'url' => $this->faker->url,
            'country' => $this->faker->randomElement(CountryEnum::getAvailables()),
            'state' => $this->faker->randomElement(StateEnum::getAvailables()),
            'city' => $this->faker->randomElement(self::$cityNames),
            'section' => $this->faker->randomElement(self::$sectionNames),
            'lang' => $this->faker->randomElement(LanguageEnum::getAvailables()),
            'deleted' => $this->faker->boolean(90),
            'type' => $this->faker->randomElement(PublisherTypeEnum::getAvailables()),
            'listIds' => [],
        ]);
    }
}
