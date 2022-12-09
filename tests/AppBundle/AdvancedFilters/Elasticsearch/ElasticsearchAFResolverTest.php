<?php

namespace AppBundle\AdvancedFilters\Elasticsearch;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use AppBundle\AdvancedFilters\AFResolver;
use AppBundle\Form\Type\AdvancedFilter\AdvancedFilterParameters;
use Common\Enum\AFSourceEnum;
use Common\Enum\DocumentsAFNameEnum;
use Common\Enum\FieldNameEnum;
use Common\Enum\LanguageEnum;
use Faker\Factory;
use Faker\Generator;
use IndexBundle\Filter\Factory\FilterFactory;
use IndexBundle\Filter\Filters\AndFilter;
use IndexBundle\Filter\Filters\EqFilter;
use IndexBundle\Filter\Filters\GteFilter;
use IndexBundle\Filter\Filters\LteFilter;
use IndexBundle\Filter\Filters\OrFilter;
use IndexBundle\SearchRequest\SearchRequest;
use Tests\AppBundle\AdvancedFilters\TestAFAggregator;
use Tests\AppTestCase;

/**
 * Class ElasticsearchAFResolverTest
 * @package AppBundle\AdvancedFilters\Elasticsearch
 */
class ElasticsearchAFResolverTest extends AppTestCase
{

    /**
     * Fixtures for 'articleLanguage' advanced filter.
     *
     * @var array
     */
    private static $languages = [
        [
            'value' => LanguageEnum::BENGALI,
            'count' => 44,
        ],
        [
            'value' => LanguageEnum::VIETNAMESE,
            'count' => 40,
        ],
        [
            'value' => LanguageEnum::DUTCH,
            'count' => 35,
        ],
        [
            'value' => LanguageEnum::GERMAN,
            'count' => 20,
        ],
        [
            'value' => LanguageEnum::GREEK,
            'count' => 21,
        ],
        [
            'value' => LanguageEnum::FINNISH,
            'count' => 14,
        ],
        [
            'value' => LanguageEnum::RUSSIAN,
            'count' => 10,
        ],
        [
            'value' => LanguageEnum::ESTONIAN,
            'count' => 7,
        ],
        [
            'value' => LanguageEnum::AFRIKAANS,
            'count' => 5,
        ],
        [
            'value' => LanguageEnum::ARABIC,
            'count' => 1,
        ],
        [
            'value' => LanguageEnum::BULGARIAN,
            'count' => 1,
        ],
        [
            'value' => LanguageEnum::NORWEGIAN,
            'count' => 1,
        ],
    ];

    /**
     * Fixtures for 'articleDate' advanced filter.
     *
     * @var array
     */
    private static $dates = [
        [
            'value' => '1 Hour',
            'count' => 20,
        ],
        [
            'value' => '24 Hour',
            'count' => 45,
        ],
        [
            'value' => '7 Days',
            'count' => 124,
        ],
        [
            'values' => '31 Days',
            'count' => 2355,
        ],
        [
            'values' => '60 Days',
            'count' => 1254151,
        ],
    ];

    /**
     * @var AFResolver
     */
    private $resolver;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @return void
     */
    public function testGetAllAvailable()
    {
        /** @var SearchRequest $request */
        $request = $this->getMockBuilder(SearchRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $values = $this->resolver->getAvailables($request);

        self::assertArrayHasKey(DocumentsAFNameEnum::ARTICLE_LANGUAGE, $values);
        self::assertArrayHasKey(DocumentsAFNameEnum::ARTICLE_DATE, $values);

        self::assertMatch(
            $values[DocumentsAFNameEnum::ARTICLE_LANGUAGE],
            [
                'data' => '@array@',
            ]
        );
        self::assertMatch(
            $values[DocumentsAFNameEnum::ARTICLE_DATE],
            [
                'data' => '@array@',
            ]
        );
    }

    /**
     * @return void
     */
    public function testGenerateFilterForLanguage()
    {
        /** @var OrFilter $filter */
        $filter = $this->resolver->generateFilter(
            AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
            DocumentsAFNameEnum::ARTICLE_LANGUAGE,
            new AdvancedFilterParameters(LanguageEnum::DUTCH, [])
        );

        /** @var EqFilter $eqFilter */
        $eqFilter = $filter->getFilters()[0];

        self::assertInstanceOf(OrFilter::class, $filter);
        self::assertInstanceOf(EqFilter::class, $eqFilter);
        self::assertSame(FieldNameEnum::LANG, $eqFilter->getFieldName());
        self::assertSame(LanguageEnum::DUTCH, $eqFilter->getValue());
    }

    /**
     * @return void
     */
    public function testGenerateFilterForDateWithTwoBounds()
    {
        /** @var AndFilter $filter */
        $filter = $this->resolver->generateFilter(
            AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
            DocumentsAFNameEnum::ARTICLE_DATE,
            AdvancedFilterParameters::queryFilterParameters('24 Hour')
        );

        self::assertInstanceOf(AndFilter::class, $filter);

        $filters = $filter->getFilters();

        self::assertCount(2, $filters);
        self::assertInstanceOf(GteFilter::class, $filters[0]);
        self::assertInstanceOf(LteFilter::class, $filters[1]);

        /** @var GteFilter $gteFilter */
        $gteFilter = $filters[0];
        self::assertSame(FieldNameEnum::PUBLISHED, $gteFilter->getFieldName());
        self::assertSame('now-1d', $gteFilter->getValue());

        /** @var LteFilter $lteFilter */
        $lteFilter = $filters[1];
        self::assertSame(FieldNameEnum::PUBLISHED, $lteFilter->getFieldName());
        self::assertSame('now-2H', $lteFilter->getValue());
    }

    /**
     * @return void
     */
    public function testGenerateFilterForDateWithOneBound()
    {
        /** @var GteFilter $filter */
        $filter = $this->resolver->generateFilter(
            AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
            DocumentsAFNameEnum::ARTICLE_DATE,
            AdvancedFilterParameters::queryFilterParameters('1 Hour')
        );

        self::assertInstanceOf(GteFilter::class, $filter);
        self::assertSame(FieldNameEnum::PUBLISHED, $filter->getFieldName());
        self::assertSame('now-1H', $filter->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown filter 'unknown'.
     *
     * @return void
     */
    public function testGenerateFilterNameException()
    {
        $this->resolver->generateFilter(
            AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
            'unknown',
            new AdvancedFilterParameters('1 Hour', [])
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid value 'invalid'. Expects one of 1 Hour, 24 Hour, 7 Days, 31 Days, 60 Days.
     *
     * @return void
     */
    public function testGenerateFilterForDateInvalidValueException()
    {
        $this->resolver->generateFilter(
            AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
            DocumentsAFNameEnum::ARTICLE_DATE,
            new AdvancedFilterParameters('invalid', [])
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->faker = Factory::create();

        $aggregator = new TestAFAggregator([
            DocumentsAFNameEnum::ARTICLE_LANGUAGE => self::$languages,
            DocumentsAFNameEnum::ARTICLE_DATE     => self::$dates,
        ]);
        $factory = new FilterFactory();

        $this->resolver = new AFResolver($aggregator, $factory);
    }
}
