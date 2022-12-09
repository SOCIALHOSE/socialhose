<?php

namespace IndexBundle\Model\Generator;

use Faker\Factory;

/**
 * Class AbstractDocumentGenerator
 * @package IndexBundle\Model\Generator
 */
abstract class AbstractDocumentGenerator
{

    /**
     * @var string[]
     */
    protected static $sharedIdentifierTypes = [
        'IdentifierOne',
        'IdentifierTwo',
        'IdentifierThree',
    ];

    /**
     * @var string[]
     */
    protected static $sentimentTypes = [
        'POSITIVE',
        'NEGATIVE',
        'NEUTRAL',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $authorNames = [
        'Mireya Ortiz MD',
        'Paxton OHara',
        'Paxton Bins DDS',
        'Gracie Pfeffer',
        'Elta Mraz PhD',
        'Preston Lang',
        'Mr. Geoffrey Bauch V',
        'Hilton Lowe',
        'Prof. Harmon Beahan',
        'Beatrice Daugherty',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $authorGender = [
        'MALE',
        'FEMALE',
        'UNKNOWN',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $publisherNames = [
        'CNN',
        'MSNBC',
        'Techcrunch',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $cityNames = [
        'Lake Leanneberg',
        'Krajcikport',
        'Majorport',
        'North Everardo',
        'Krajcikport',
        'Abigaylechester',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $stateNames = [
        'Oklahoma',
        'Florida',
        'North Dakota',
        'Maine',
        'Colorado',
        'Idaho',
    ];

    /**
     * Just for better aggregation tests
     *
     * @var string[]
     */
    protected static $sectionNames = [
        'Sports',
        'Lifestyle',
        'Technology',
    ];

    /**
     * @var string[]
     */
    protected static $types = [
        'POST',
        'COMMENT',
    ];

    /**
     * @var string[]
     */
    protected static $tags = [
        'news',
        '2016',
        'finance',
        'history',
    ];

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * FakerDocumentGenerator constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }
}
