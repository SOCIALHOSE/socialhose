<?php

namespace app\a;

use IndexBundle\Index\Strategy\IndexStrategyInterface;
use IndexBundle\Model\ArticleDocument;
use Tests\AppTestCase;

/**
 * Class ArrayTest
 * @package AppFunctional
 */
class ArrayTest extends AppTestCase
{

    /**
     * @return void
     */
    public function testBinarySearchForCollectionWithOddItemsNumber()
    {
        $collection = [ 1, 2, 3, 4, 5 ];

        $this->assertSame(2, binarySearch($collection, 3));
        $this->assertSame(0, binarySearch($collection, 1));
        $this->assertSame(1, binarySearch($collection, 2));
        $this->assertSame(4, binarySearch($collection, 5));
    }

    /**
     * @return void
     */
    public function testBinarySearchForCollectionWithEvenItemsNumber()
    {
        $collection = [ 1, 2, 3, 4, 5, 6];

        $this->assertSame(2, binarySearch($collection, 3));
        $this->assertSame(0, binarySearch($collection, 1));
        $this->assertSame(1, binarySearch($collection, 2));
        $this->assertSame(5, binarySearch($collection, 6));
    }

    /**
     * @return void
     */
    public function testBinarySearchSearchUnknownItem()
    {
        $collection = [ 1, 2, 3, 4, 5, 6];

        $this->assertFalse(binarySearch($collection, 0));
        $this->assertFalse(binarySearch($collection, 7));
        $this->assertFalse(binarySearch($collection, -10));
        $this->assertFalse(binarySearch($collection, 20));
    }

    /**
     * @return void
     */
    public function testBinarySearchInObject()
    {
        $collection = [
            (object) [ 'id' => 1 ],
            (object) [ 'id' => 2 ],
            (object) [ 'id' => 3 ],
            (object) [ 'id' => 4 ],
            (object) [ 'id' => 5 ],
        ];

        $this->assertSame(2, binarySearch($collection, 3, 'id'));
        $this->assertSame(0, binarySearch($collection, 1, 'id'));
        $this->assertSame(1, binarySearch($collection, 2, 'id'));
        $this->assertSame(4, binarySearch($collection, 5, 'id'));
    }

    /**
     * @return void
     */
    public function testBinarySearchInObjectWithGetter()
    {
        $collection = [
            new TestFixture(1),
            new TestFixture(2),
            new TestFixture(3),
            new TestFixture(4),
            new TestFixture(5),
        ];

        $this->assertSame(2, binarySearch($collection, 3, \nspl\op\methodCaller('getId')));
        $this->assertSame(0, binarySearch($collection, 1, \nspl\op\methodCaller('getId')));
        $this->assertSame(1, binarySearch($collection, 2, \nspl\op\methodCaller('getId')));
        $this->assertSame(4, binarySearch($collection, 5, \nspl\op\methodCaller('getId')));
    }

    /**
     * @return void
     */
    public function testBinarySearchInDocument()
    {
        /** @var IndexStrategyInterface $strategy */
        $strategy = $this->getMockForInterface(IndexStrategyInterface::class);

        $collection = [
            new ArticleDocument($strategy, ['sequence' => 1]),
            new ArticleDocument($strategy, ['sequence' => 2]),
            new ArticleDocument($strategy, ['sequence' => 3]),
            new ArticleDocument($strategy, ['sequence' => 4]),
            new ArticleDocument($strategy, ['sequence' => 5]),
        ];

        $this->assertSame(2, binarySearch($collection, 3, 'sequence'));
        $this->assertSame(0, binarySearch($collection, 1, 'sequence'));
        $this->assertSame(1, binarySearch($collection, 2, 'sequence'));
        $this->assertSame(4, binarySearch($collection, 5, 'sequence'));
    }
}

/**
 * Class TestFixture
 * @package AppFunctional
 */
class TestFixture
{
    /**
     * @var integer
     */
    private $id;

    /**
     * TestFixture constructor.
     *
     * @param integer $id Identifier.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
