<?php

namespace UserBundle\Entity\Traits;

use Tests\AppTestCase;
use UserBundle\Enum\AppLimitEnum;

/**
 * Class LimitAwareTraitTest
 *
 * @package UserBundle\Entity\Traits
 */
class LimitAwareTraitTest extends AppTestCase
{

    /**
     * @var LimitAwareTrait
     */
    private $trait;

    /**
     * @return void
     */
    public function testLimitValue()
    {
        /** @var AppLimitEnum $value */
        foreach (AppLimitEnum::getValues() as $value) {
            $this->trait->setLimitValue($value, 1);
            $this->assertEquals(1, $this->trait->getLimitValue($value));
        }
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->trait = $this->getMockForTrait(LimitAwareTrait::class);
    }
}
