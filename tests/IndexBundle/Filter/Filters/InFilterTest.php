<?php

namespace IndexBundle\Filter\Filters;

use Common\Enum\PublisherTypeEnum;
use Tests\AppTestCase;

/**
 * Class InFilterTest
 *
 * @package IndexBundle\Filter\Filters
 */
class InFilterTest extends AppTestCase
{

    /**
     * @return void
     */
    public function testCreate()
    {
        $filter = new InFilter('foo', []);
        $this->assertEquals('foo', $filter->getFieldName());
        $this->assertEquals([], $filter->getValue());

        $dateMut = date_create();
        $dateImm = date_create_immutable(' + 1 day');

        $filter = new InFilter('bar', [
            12,
            true,
            2.3,
            'string',
            $dateMut,
            $dateImm,
            PublisherTypeEnum::unknown(),
        ]);
        $this->assertEquals('bar', $filter->getFieldName());
        $this->assertEquals([
            12,
            true,
            2.3,
            'string',
            $dateMut,
            $dateImm,
            PublisherTypeEnum::UNKNOWN,
        ], $filter->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$field' should be string
     *
     * @return void
     */
    public function testCreateInvalidField()
    {
        new InFilter(1, []);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$values' should be an array of scalar values, AbstractEnum or \DateTimeInterface instances

     *
     * @return void
     */
    public function testCreateInvalidValues()
    {
        new InFilter('foo', [ [ 1 ] ]);
    }

    /**
     * @return void
     */
    public function testSerialize()
    {
        $values = [ 10, 20, 100 ];

        $filter = new InFilter('foo', $values);

        /** @var InFilter $unserializedFilter */
        $unserializedFilter = unserialize(serialize($filter));

        $this->assertInstanceOf(InFilter::class, $unserializedFilter);
        $this->assertSame('foo', $unserializedFilter->getFieldName());
        $this->assertSame($values, $unserializedFilter->getValue());
    }

    /**
     * @return void
     */
    public function testSort()
    {
        $filter = new InFilter('foo', [ 11, -3, 30]);
        $filter->sort();
        $this->assertSame([-3, 11, 30], $filter->getValue());

        $date1 = new \DateTime('+ 1 day');
        $date2 = new \DateTime();
        $date3 = new \DateTime('- 1 month');

        $filter = new InFilter('some', [ $date1, $date2, $date3 ]);
        $filter->sort();
        $this->assertSame([ $date3, $date2, $date1 ], $filter->getValue());
    }
}
