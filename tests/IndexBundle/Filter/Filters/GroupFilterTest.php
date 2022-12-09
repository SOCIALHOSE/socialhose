<?php

namespace IndexBundle\Filter\Filters;

use IndexBundle\Filter\AbstractGroupFilter;
use Tests\AppTestCase;

/**
 * Class GroupFilterTest
 *
 * @package IndexBundle\Filter\Filters
 */
class GroupFilterTest extends AppTestCase
{

    /**
     * @return void
     */
    public function testCreate()
    {
        $this->getMockForAbstractClass(AbstractGroupFilter::class, [ [ new GteFilter('foo', 10), new AndFilter() ]]);
        $this->getMockForAbstractClass(AbstractGroupFilter::class, [ new GteFilter('foo', 10) ]);
        $this->getMockForAbstractClass(AbstractGroupFilter::class, [ new AndFilter() ]);
        $this->getMockForAbstractClass(AbstractGroupFilter::class);
        $this->getMockForAbstractClass(AbstractGroupFilter::class);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$filters' should be array of FilterInterface instances or single instance
     *
     * @return void
     */
    public function testCreateWithArrayOfInvalid()
    {
        $this->getMockForAbstractClass(AbstractGroupFilter::class, [ [ new GteFilter('foo', 10), 10 ] ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$filters' should be array of FilterInterface instances or single instance
     *
     * @return void
     */
    public function testCreateWithSingleInvalid()
    {
        $this->getMockForAbstractClass(AbstractGroupFilter::class, [ 1 ]);
    }

    /**
     * @return void
     */
    public function testSerialize()
    {
        $date = new \DateTime();

        $filter1 = new EqFilter('some', 10);
        $filter21 = new EqFilter('another', 'foo');
        $filter22 = new NotFilter(new GteFilter('bar', $date));
        $filter2 = $this->getMockForAbstractClass(AbstractGroupFilter::class, [ [ $filter21, $filter22 ] ]);
        $filter = $this->getMockForAbstractClass(AbstractGroupFilter::class, [ [ $filter1, $filter2 ] ]);

        /** @var AbstractGroupFilter $unserializedFilter */
        $unserializedFilter = unserialize(serialize($filter));

        $this->assertInstanceOf(AbstractGroupFilter::class, $unserializedFilter);
        $this->assertCount(2, $unserializedFilter->getFilters());

        /** @var EqFilter $unserializedInnerFilter1 */
        $unserializedInnerFilter1 = $unserializedFilter->getFilters()[0];
        /** @var AbstractGroupFilter $unserializedInnerFilter2 */
        $unserializedInnerFilter2 = $unserializedFilter->getFilters()[1];

        $this->assertInstanceOf(EqFilter::class, $unserializedInnerFilter1);
        $this->assertSame('some', $unserializedInnerFilter1->getFieldName());
        $this->assertSame(10, $unserializedInnerFilter1->getValue());

        $this->assertInstanceOf(AbstractGroupFilter::class, $unserializedInnerFilter2);
        $this->assertCount(2, $unserializedInnerFilter2->getFilters());

        /** @var EqFilter $unserializedInnerFilter21 */
        $unserializedInnerFilter21 = $unserializedInnerFilter2->getFilters()[0];
        /** @var NotFilter $unserializedInnerFilter22 */
        $unserializedInnerFilter22 = $unserializedInnerFilter2->getFilters()[1];

        $this->assertInstanceOf(EqFilter::class, $unserializedInnerFilter21);
        $this->assertSame('another', $unserializedInnerFilter21->getFieldName());
        $this->assertSame('foo', $unserializedInnerFilter21->getValue());

        $this->assertInstanceOf(NotFilter::class, $unserializedInnerFilter22);

        /** @var GteFilter $unserializedInnerFilter22Inner */
        $unserializedInnerFilter22Inner = $unserializedInnerFilter22->getFilter();

        $this->assertInstanceOf(GteFilter::class, $unserializedInnerFilter22Inner);
        $this->assertSame('bar', $unserializedInnerFilter22Inner->getFieldName());
        $this->assertEquals(
            $date->format('c'),
            $unserializedInnerFilter22Inner->getValue()->format('c')
        );
    }

    /**
     * @return void
     */
    public function testSort()
    {
        $date = new \DateTime();

        $filter1 = new EqFilter('some', 10);
        $filter21 = new EqFilter('another', 'foo');
        $filter22 = new NotFilter(new GteFilter('bar', $date));
        $filter2 = new AndFilter([ $filter22, $filter21 ]);
        $filter = new OrFilter([ $filter2, $filter1 ]);

        $filter->sort();

        $this->assertSame([ $filter1, $filter2 ], $filter->getFilters());
        $this->assertSame([ $filter21, $filter22 ], $filter2->getFilters());
    }

    /**
     * @return void
     */
    public function testCompareValueFilters()
    {
        $filter = $this->getMockForAbstractClass(AbstractGroupFilter::class);

        $this->assertGreaterThan(0, $this->call($filter, 'compareValueFilters', [
            new EqFilter('b', 10),
            new EqFilter('a', 10),
        ]));

        $this->assertLessThan(0, $this->call($filter, 'compareValueFilters', [
            new EqFilter('a', 9),
            new EqFilter('a', 10),
        ]));

        $this->assertEquals(0, $this->call($filter, 'compareValueFilters', [
            new GteFilter('a', 10),
            new LteFilter('a', 10),
        ]));
    }

    /**
     * @return void
     */
    public function testCompareInFilters()
    {
        $filter = $this->getMockForAbstractClass(AbstractGroupFilter::class);

        $this->assertGreaterThan(0, $this->call($filter, 'compareInFilters', [
            new InFilter('b', [ 10 ]),
            new InFilter('a', [ 10 ]),
        ]));

        $this->assertLessThan(0, $this->call($filter, 'compareInFilters', [
            new InFilter('a', [ 9 ]),
            new InFilter('a', [ 10 ]),
        ]));

        $this->assertEquals(0, $this->call($filter, 'compareInFilters', [
            new InFilter('a', [ 10 ]),
            new InFilter('a', [ 10 ]),
        ]));

        $this->assertLessThan(0, $this->call($filter, 'compareInFilters', [
            new InFilter('a', [ 10 ]),
            new InFilter('a', [ 10, 10 ]),
        ]));
    }

    /**
     * @return void
     */
    public function testCompareGroupFilters()
    {
        $filter = $this->getMockForAbstractClass(AbstractGroupFilter::class);

        $this->assertEquals(0, $this->call($filter, 'compareGroupFilters', [
            new AndFilter(),
            new AndFilter(),
        ]));

        $this->assertEquals(0, $this->call($filter, 'compareGroupFilters', [
            new AndFilter([ new AndFilter([ new EqFilter('a', 10) ]) ]),
            new AndFilter([ new AndFilter([ new GteFilter('a', 10) ]) ]),
        ]));

        $this->assertGreaterThan(0, $this->call($filter, 'compareGroupFilters', [
            new AndFilter([ new EqFilter('a', 10) ]),
            new AndFilter([ new EqFilter('a', 9) ]),
        ]));

        $this->assertLessThan(0, $this->call($filter, 'compareGroupFilters', [
            new AndFilter([ new EqFilter('a', 10) ]),
            new AndFilter([ new AndFilter() ]),
        ]));

        $this->assertLessThan(0, $this->call($filter, 'compareGroupFilters', [
            new AndFilter(),
            new AndFilter([ new AndFilter() ]),
        ]));
    }
}
