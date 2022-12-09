<?php

namespace UserBundle\Entity\Recipient;

use Tests\AppTestCase;
use UserBundle\Entity\User;

/**
 * Class PersonRecipientTest
 *
 * @package UserBundle\Entity
 */
class PersonRecipientTest extends AppTestCase
{

    /**
     * @var PersonRecipient
     */
    private $recipient;

    /**
     * @return void
     */
    public function testSetFirstNameWithoutAssignedUser()
    {
        $this->recipient->setFirstName('first name');

        $this->assertEquals('first name', $this->recipient->getFirstName());
    }

    /**
     * @return void
     */
    public function testSetFirstNameWithAssignedUser()
    {
        $user = new User();
        $this->recipient
            ->setAssociatedUser($user)
            ->setFirstName('first name');

        $this->assertEquals('first name', $this->recipient->getFirstName());
        $this->assertEquals('first name', $user->getFirstName());
    }

    /**
     * @return void
     */
    public function testSetLastNameWithoutAssignedUser()
    {
        $this->recipient->setLastName('last name');

        $this->assertEquals('last name', $this->recipient->getLastName());
    }

    /**
     * @return void
     */
    public function testSetLastNameWithAssignedUser()
    {
        $user = new User();
        $this->recipient
            ->setAssociatedUser($user)
            ->setLastName('last name');

        $this->assertEquals('last name', $this->recipient->getLastName());
        $this->assertEquals('last name', $user->getLastName());
    }

    /**
     * @return void
     */
    public function testSetEmailWithoutAssignedUser()
    {
        $this->recipient->setEmail('test@test.test');

        $this->assertEquals('test@test.test', $this->recipient->getEmail());
    }

    /**
     * @return void
     */
    public function testSetEmailWithAssignedUser()
    {
        $user = new User();
        $this->recipient
            ->setAssociatedUser($user)
            ->setEmail('test@test.test');

        $this->assertEquals('test@test.test', $this->recipient->getEmail());
        $this->assertEquals('test@test.test', $user->getEmail());
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->recipient = new PersonRecipient();
    }
}
