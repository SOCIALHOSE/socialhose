<?php

namespace Tests\IndexBundle\Model;

use IndexBundle\Index\Strategy\IndexStrategyInterface;
use Tests\AppTestCase;

/**
 * Class TestModel
 * @package Tests\IndexBundle\Model
 */
class AbstractIndexDocumentTest extends AppTestCase
{

    /**
     * Array of available document properties.
     *
     * @var string[]
     */
    protected $available = [
        'first',
        'second',
        'third',
    ];

    /**
     * @var TestModel
     */
    private $model;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        /** @var IndexStrategyInterface|\PHPUnit_Framework_MockObject_MockObject $strategy */
        $strategy = $this->getMockForInterface(IndexStrategyInterface::class);

        $strategy
            ->method('normalizeDocumentData')
            ->willReturnCallback(function (array $data) {
                return $data;
            });

        $strategy
            ->method('getIndexableData')
            ->willReturnCallback(function (array $data) {
                return $data;
            });

        $strategy
            ->method('normalizeFieldName')
            ->willReturnCallback(function ($fieldName) {
                return $fieldName;
            });

        $strategy
            ->method('denormalizeFieldName')
            ->willReturnCallback(function ($fieldName) {
                return $fieldName;
            });

        $strategy
            ->method('normalizePublisherType')
            ->willReturnCallback(function ($type) {
                return $type;
            });

        $strategy
            ->method('denormalizePublisherType')
            ->willReturnCallback(function ($type) {
                return $type;
            });

        $this->model = new TestModel($strategy, [
            'first' => 1,
            'second' => 'two',
            'third' => [ 3, 2, 1 ],
        ]);
    }

    /**
     * @return void
     */
    public function testAccessAsArray()
    {
        $this->assertSame(1, $this->model['first']);
        $this->assertSame('two', $this->model['second']);
        $this->assertSame([ 3, 2, 1], $this->model['third']);

        $this->model['first'] = 2;
        $this->model['second'] = 'third';
        $this->model['third'] = [ 1, 2, 3 ];

        $this->assertSame(2, $this->model['first']);
        $this->assertSame('third', $this->model['second']);
        $this->assertSame([ 1, 2, 3], $this->model['third']);
    }

    /**
     * @return void
     */
    public function testIterate()
    {
        $expected = [
            'first' => 1,
            'second' => 'two',
            'third' => [ 3, 2, 1 ],
        ];

        reset($expected);
        foreach ($this->model as $name => $value) {
            $this->assertSame(key($expected), $name);
            $this->assertSame(current($expected), $value);

            next($expected);
        }

        $this->model['first'] = 2;
        $this->model['second'] = 'third';
        $this->model['third'] = [ 1, 2, 3 ];

        $expected = [
            'first' => 2,
            'second' => 'third',
            'third' => [ 1, 2, 3 ],
        ];

        reset($expected);
        foreach ($this->model as $name => $value) {
            $this->assertSame(key($expected), $name);
            $this->assertSame(current($expected), $value);

            next($expected);
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown property 'fourth'
     *
     * @return void
     */
    public function testAccessAsArrayInvalidProperty()
    {
        $this->model['fourth'];
    }

    /**
     * @return void
     */
    public function testAccessAsProperty()
    {
        $this->assertSame(1, $this->model->first);
        $this->assertSame('two', $this->model->second);
        $this->assertSame([ 3, 2, 1], $this->model->third);

        $this->model->first = 2;
        $this->model->second = 'third';
        $this->model->third = [ 1, 2, 3 ];

        $this->assertSame(2, $this->model->first);
        $this->assertSame('third', $this->model->second);
        $this->assertSame([ 1, 2, 3], $this->model->third);
    }
}
