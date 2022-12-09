<?php

namespace IndexBundle\Normalizer\Query;

use Tests\AppTestCase;

/**
 * Class QueryNormalizerTest
 * @package AppBundle\Search\Request\Normalizer
 */
class QueryNormalizerTest extends AppTestCase
{

    /**
     * @var QueryNormalizer
     */
    private $normalizer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->normalizer = new QueryNormalizer();
    }

    /**
     * @dataProvider queriesProvider
     *
     * @param string  $query1  First search query.
     * @param string  $query2  Second search query.
     * @param boolean $expects Match or don't.
     *
     * @return void
     */
    public function testGenerateUniqueKey($query1, $query2, $expects)
    {
        $query1 = $this->normalizer->normalize($query1);
        $query2 = $this->normalizer->normalize($query2);

        $message = "Key's must be ". ($expects ? 'same' : 'differ')
            .', but first query is '. $query1 .' and second is '. $query2;
        $this->assertEquals($query1 === $query2, $expects, $message);
    }

    /**
     * @return array
     */
    public function queriesProvider()
    {
        return [
            'cat dog === dog cat' => [
                'cat dog',
                'dog cat',
                true,
            ],
            'cat~ dog~0.8 === dog~0.8 cat~' => [
                'cat~ dog~0.8',
                'dog~0.8 cat~',
                true,
            ],
            'cat~ dog~0.7 !== dog~0.8 cat~' => [
                'cat~ dog~0.7',
                'dog~0.8 cat~',
                false,
            ],
            'ca?t dog === dog ca?t' => [
                'ca?t dog',
                'dog ca?t',
                true,
            ],
            'c?at dog !== dog ca?t' => [
                'c?at dog',
                'dog ca?t',
                false,
            ],
            'cat^4 dog === dog cat^4' => [
                'cat^4 dog',
                'dog cat^4',
                true,
            ],
            'cat^3 dog !== dog cat^4' => [
                'cat^3 dog',
                'dog cat^4',
                false,
            ],
            '* dog === dog *' => [
                '* dog',
                'dog *',
                true,
            ],
            '+cat dog === dog +cat' => [
                '+cat dog',
                'dog +cat',
                true,
            ],
            'NOT cat AND dog === dog AND NOT cat' => [
                'NOT cat AND dog',
                'dog AND NOT cat',
                true,
            ],
            'NOT (cat AND (dog OR fish)) === NOT ((fish OR dog) AND cat)' => [
                'NOT (cat AND (dog OR fish))',
                'NOT ((fish OR dog) AND cat)',
                true,
            ],
            ' AND or NOT !== NOT or AND' => [
                ' AND or NOT',
                'NOT or AND',
                false,
            ],
            'OR AND NOT === NOT OR AND' => [
                'OR AND NOT',
                'NOT OR AND',
                true,
            ],
            'NOT cat !== cat NOT' => [
                'NOT cat',
                'cat NOT',
                false,
            ],
            'cat AND dog !== cat and dog' => [
                'cat AND dog',
                'cat and dog',
                false,
            ],
            '"cat fly" AND dog === dog AND "cat fly"' => [
                '"cat fly" AND dog',
                'dog AND "cat fly"',
                true,
            ],
            '"cat fly"^3 AND dog === dog AND "cat fly"^3' => [
                '"cat fly"^3 AND dog',
                'dog AND "cat fly"^3',
                true,
            ],
            '"cat fly"^4 AND dog !== dog AND "cat fly"^3' => [
                '"cat fly"^4 AND dog',
                'dog AND "cat fly"^3',
                false,
            ],
            '"cat fly"~0.4 AND dog === dog AND "cat fly"~0.4' => [
                '"cat fly"~0.4 AND dog',
                'dog AND "cat fly"~0.4',
                true,
            ],
            '"cat fly"~0.3 AND dog !== dog AND "cat fly"~0.4' => [
                '"cat fly"~0.3 AND dog',
                'dog AND "cat fly"~0.4',
                false,
            ],
            '"cat fly"+ AND dog !== dog AND "cat fly"' => [
                '"cat fly"+ AND dog',
                'dog AND "cat fly"',
                false,
            ],
            '"cat fly"+ AND dog === dog AND "cat fly"+' => [
                '"cat fly"+ AND dog',
                'dog AND "cat fly"+',
                true,
            ],
            'cat AND dog !== cat dog' => [
                'cat AND dog',
                'cat dog',
                false,
            ],
            'cat OR dog === cat dog' => [
                'cat OR dog',
                'cat dog',
                true,
            ],
            '(cat) dog === cat dog' => [
                '(cat) dog',
                'cat dog',
                true,
            ],
            '(cat AND dog) === cat AND dog' => [
                '(cat AND dog)',
                'cat AND dog',
                true,
            ],
            '(cat dog) === dog cat' => [
                '(cat dog)',
                'dog cat',
                true,
            ],
            '(((cat OR dog))) === cat dog' => [
                '(((cat OR dog)))',
                'cat dog',
                true,
            ],
            '(cat AND dog) OR fish === cat AND dog OR fish' => [
                '(cat AND dog) OR fish',
                'cat AND dog OR fish',
                true,
            ],
            'cat AND (dog OR fish) !== cat AND dog OR fish' => [
                'cat AND (dog OR fish)',
                'cat AND dog OR fish',
                false,
            ],
            '(cat AND dog) (fish AND bird) === (cAt AND dog) (fish AND BIRd)' => [
                '(cat AND dog) (fish AND bird)',
                '(cAt AND dog) (fish AND BIRd)',
                true,
            ],
            '(cat AND dog) AND bird === cAt AND BIRD AND dOg' => [
                '(cat AND dog) AND bird',
                'cAt AND BIRD AND dOg',
                true,
            ],
            '(cat OR dog) bird === cAt bird doG' => [
                '(cat OR dog) bird',
                'cAt bird doG',
                true,
            ],
            '(dog OR dog) AND cat === cat AND doG' => [
                '(dog OR dog) AND cat',
                'cat AND doG',
                true,
            ],
            '(dog AND dog) AND cat === cat AND doG' => [
                '(dog AND dog) AND cat',
                'cat AND doG',
                true,
            ],
            '(dog AND dog) AND cat AND (fish OR fish) === fish AND cat AND doG' => [
                '(dog AND dog) AND cat AND (fish OR fish)',
                'fish AND cat AND doG',
                true,
            ],
            '(dog AND dog) OR (cat AND cat) === dog OR (cat)' => [
                '(dog AND dog) OR (cat AND cat)',
                'dog OR (cat)',
                true,
            ],
            '"NOT ("dog cat"^3 AND (Alice OR "fish"~0.3)) OR NOT ("dog cat fish"~ AND NOT (+"Alice" OR dog~0.3 OR man^3))' => [
                'NOT ("dog cat"^3 AND (Alice OR "fish"~0.3)) OR NOT ("dog cat fish"~ AND NOT (+"Alice" OR dog~0.3 OR man^3))',
                'NOT (NOT (man^3 OR +"Alice" OR dog~0.3) AND "dog cat fish"~) OR NOT ("dog cat"^3 AND ("fish"~0.3 OR Alice))',
                true,
            ],
            '+Ethiopia +Sudan -”South Africa” === -”South Africa” +Ethiopia +Sudan' => [
                '+Ethiopia +Sudan -”South Africa”',
                '-”South Africa” +Ethiopia +Sudan',
                true,
            ],
        ];
    }
}
