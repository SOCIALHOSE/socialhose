<?php

namespace IndexBundle\Filter\Filters;

use Common\Enum\PublisherTypeEnum;
use IndexBundle\Filter\AbstractValueFilter;
use Tests\AppTestCase;

/**
 * Class ValueFilterTest
 *
 * @package IndexBundle\Filter\Filters
 */
class ValueFilterTest extends AppTestCase
{

    /**
     * @return void
     */
    public function testCreate()
    {
        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', 10 ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals(10, $mock->getValue());

        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', 'string' ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals('string', $mock->getValue());

        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', 2.4 ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals(2.4, $mock->getValue());

        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', true ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals(true, $mock->getValue());

        $date = date_create();
        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', $date ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals($date, $mock->getValue());

        $date = date_create_immutable();
        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', $date ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals($date, $mock->getValue());

        $mock = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', PublisherTypeEnum::blogs() ]);
        $this->assertEquals('foo', $mock->getFieldName());
        $this->assertEquals(PublisherTypeEnum::BLOGS, $mock->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$field' should be string
     *
     * @return void
     */
    public function testCreateInvalidField()
    {
        $this->getMockForAbstractClass(AbstractValueFilter::class, [ 1, 10 ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage '$value' should be scalar, AbstractEnum or \DateTimeInterface instance
     *
     * @return void
     */
    public function testCreateInvalidValues()
    {
        $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', [ 1 ] ]);
    }

    /**
     * @return void
     */
    public function testSerialize()
    {
        $filter = $this->getMockForAbstractClass(AbstractValueFilter::class, [
            'foo',
            20,
        ]);

        /** @var AbstractValueFilter $unserializedFilter */
        $unserializedFilter = unserialize(serialize($filter));

        $this->assertInstanceOf(AbstractValueFilter::class, $unserializedFilter);
        $this->assertSame('foo', $unserializedFilter->getFieldName());
        $this->assertSame(20, $unserializedFilter->getValue());
    }

    /**
     * @return void
     */
    public function testSort()
    {
        $filter = $this->getMockForAbstractClass(AbstractValueFilter::class, [ 'foo', 10 ]);
        $filter->sort();
        $this->assertSame(10, $filter->getValue());
    }
}
