<?php

namespace UserBundle\Form\Type;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

/**
 * Class ColorTypeTest
 *
 * @package UserBundle\Form\Type
 */
class ColorTypeTest extends TestCase
{

    /**
     * @var ColorType
     */
    private $type;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->type = new ColorType();
    }

    /**
     * @dataProvider validProvider
     *
     * @param mixed $color Validated color.
     *
     * @return void
     */
    public function testValidateSuccess($color)
    {
        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->setMethods([ 'buildViolation' ])
            ->getMock();

        $context->expects($this->never())
            ->method('buildViolation')
            ->willReturnCallback(function () {
                $mock = $this->getMockBuilder(ConstraintViolationBuilder::class)
                    ->disableOriginalConstructor()
                    ->setMethods([ 'addViolation' ])
                    ->getMock();

                $mock->expects($this->never())
                    ->method('addViolation');

                return $mock;
            });

        $this->type->validate($color, $context);
    }

    /**
     * @dataProvider notValidProvider
     *
     * @param mixed $color Validated color.
     *
     * @return void
     */
    public function testValidateFail($color)
    {

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->setMethods([ 'buildViolation' ])
            ->getMock();

        $context->expects($this->once())
            ->method('buildViolation')
            ->willReturnCallback(function () {
                $mock = $this->getMockBuilder(ConstraintViolationBuilder::class)
                    ->disableOriginalConstructor()
                    ->setMethods([ 'addViolation' ])
                    ->getMock();

                $mock->expects($this->once())
                    ->method('addViolation');

                return $mock;
            });

        $this->type->validate($color, $context);
    }

    /**
     * @return array
     */
    public function validProvider()
    {
        return [
            [ 'rgba(100, 100, 100, .0)' ],
            [ 'rgba(125, 100, 100, 1.0)' ],
            [ 'rgba(255, 255, 255, 1)' ],
            [ 'rgba(1, 12, 123, .0000034)' ],
            [ 'rgba(43, 234, 23, .2)' ],
            [ 'rgba(0, 0, 0, .1)' ],
        ];
    }

    /**
     * @return array
     */
    public function notValidProvider()
    {
        return [
            [ 'rgba(100, 100, 100, .0' ],
            [ 'rgba(125, 256, 100, 1.0)' ],
            [ 'rgba(255, 255, 255)' ],
            [ 'rgba(1, 12, 123, -1)' ],
            [ 'rgba(43, 234, -23, .2)' ],
            [ 'rgba(0, 0, 0, 1.001)' ],
            [ 'rgba(0, 20, 0, 100%)' ],
        ];
    }
}
