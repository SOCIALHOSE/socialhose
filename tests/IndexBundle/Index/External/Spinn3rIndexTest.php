<?php

namespace IndexBundle\Index\External;

use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Tests\AppTestCase;

/**
 * Class HoseIndexTest
 *
 * @package IndexBundle\Index\External
 */
class HoseIndexTest extends AppTestCase
{

    /**
     * @var HoseIndex
     */
    private $index;

    /**
     * @return void
     */
    public function testBeforeSearch()
    {
        $parameters = [
            'body' => [ 'query' => [ 'query_string' => [ 'query' => sprintf(
                'published:[%s TO *]',
                date_create('+ 36 days')->format('c')
            ), ], ], ],
        ];

        $parameters = $this->call($this->index, 'beforeSearch', [ $parameters ]);

        $this->assertArrayHasKey('index', $parameters);
        $this->assertEquals(
            implode(',', [ HoseIndex::HOT, HoseIndex::WARM ]),
            $parameters['index']
        );
    }

    /**
     * @dataProvider getQueryRangeEndProvider
     *
     * @param \DateTime|null $expected Expected result.
     * @param string         $query    ES query string.
     *
     * @return void
     */
    public function testGetQueryRangeEnd(\DateTime $expected = null, $query)
    {
        $this->assertEquals(
            $expected,
            $this->call($this->index, 'getQueryRangeEnd', [ $query ])
        );
    }

    /**
     * @return array
     */
    public function getQueryRangeEndProvider()
    {
        $date1 = date_create('+ 36 days 00:00:00');

        return [
            [
                $date1,
                sprintf('published:[%s TO *]', $date1->format('c')),
            ],
            [
                date_create('2017-11-27T00:00:00+07:00'),
                '{"body":{"query":{"query_string":{"query":"(main:("Clarion School" -"North Clarion") OR title:("Clarion School" -"North Clarion")) AND ((source_publisher_type:(MAINSTREAM_NEWS OR REVIEW OR CLASSIFIED OR UNKNOWN OR WEBLOG OR MICROBLOG OR UNKNOWN OR SOCIAL_MEDIA OR UNKNOWN OR VIDEO OR UNKNOWN OR FORUM OR MEMETRACKER OR UNKNOWN OR PHOTO OR UNKNOWN)) AND ((published:[2017-11-27T00:00:00+07:00 TO *]) AND (published:[* TO 2018-01-26T23:59:59+07:00])) AND (published:[* TO 2018-01-26T19:20:26+07:00]))"}},"sort":{"published":{"order":"desc"}}},"index":"content_*","type":"","size":100,"from":0}',
            ],
            [
                null,
                '',
            ],
            [
                null,
                '{"body":{"query":{"query_string":{"query":"(main:("Clarion School" -"North Clarion") OR title:("Clarion School" -"North Clarion")) AND ((source_publisher_type:(MAINSTREAM_NEWS OR REVIEW OR CLASSIFIED OR UNKNOWN OR WEBLOG OR MICROBLOG OR UNKNOWN OR SOCIAL_MEDIA OR UNKNOWN OR VIDEO OR UNKNOWN OR FORUM OR MEMETRACKER OR UNKNOWN OR PHOTO OR UNKNOWN)))"}},"sort":{"published":{"order":"desc"}}},"index":"content_*","type":"","size":100,"from":0}',
            ],
        ];
    }

    /**
     * @dataProvider determineIndexProvider
     *
     * @param array $expected  Expected hose indices.
     * @param array $arguments Tested method arguments.
     *
     * @return void
     */
    public function testDetermineIndex(array $expected, array $arguments)
    {
        $this->assertEquals(
            implode(',', $expected),
            $this->call($this->index, 'determineIndex', $arguments)
        );
    }

    /**
     * @return array
     */
    public function determineIndexProvider()
    {
        return [
            'without date' => [
                [ HoseIndex::HOT, HoseIndex::WARM, HoseIndex::COLD ],
                [],
            ],
            '72 days ahead' => [
                [ HoseIndex::HOT, HoseIndex::WARM, HoseIndex::COLD ],
                [ date_create('+ 72 days') ],
            ],
            '60 days and 1 hour ahead' => [
                [ HoseIndex::HOT, HoseIndex::WARM, HoseIndex::COLD ],
                [ date_create('+ 60 days 01:00:00') ],
            ],
            '60 days ahead' => [
                [ HoseIndex::HOT, HoseIndex::WARM ],
                [ date_create('+ 60 days 00:00:00') ],
            ],
            '36 days ahead' => [
                [ HoseIndex::HOT, HoseIndex::WARM ],
                [ date_create('+ 36 days') ],
            ],
            '30 days and 1 hour ahead' => [
                [ HoseIndex::HOT, HoseIndex::WARM ],
                [ date_create('+ 30 days 01:00:00') ],
            ],
            '30 days ahead' => [
                [ HoseIndex::HOT ],
                [ date_create('+ 30 days 00:00:00') ],
            ],
            '21 days ahead' => [
                [ HoseIndex::HOT ],
                [ date_create('+ 21 days') ],
            ],
            'current date' => [
                [ HoseIndex::HOT ],
                [ date_create() ],
            ],
        ];
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->index = new HoseIndex(
            new NullLogger(),
            new NullAdapter(),
            'host',
            'vendor',
            'auth'
        );
    }
}
