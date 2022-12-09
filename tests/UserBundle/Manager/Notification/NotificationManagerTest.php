<?php

namespace UserBundle\Manager\Notification;

use AppBundle\Configuration\ConfigurationImmutableInterface;
use CacheBundle\Document\Extractor\DocumentContentExtractorInterface;
use CacheBundle\Feed\Fetcher\Factory\FeedFetcherFactoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Tests\AppTestCase;
use Tests\UserBundle\Manager\Notification\RecipientFixture;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Recipient\AbstractRecipient;

/**
 * Class NotificationManagerTest
 * @package UserBundle\Manager\Notification
 */
class NotificationManagerTest extends AppTestCase
{

    /**
     * @var Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conn;

    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $em;

    /**
     * @var FeedFetcherFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $feedFetcherFactory;

    /**
     * @var ConfigurationImmutableInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configuration;

    /**
     * @var DocumentContentExtractorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extractor;

    /**
     * @var NotificationManager
     */
    private $manager;

    /**
     * @return void
     */
    public function testNormalizeNotificationsSingle()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)->getMock();

        $this->assertEquals([ $notification ], $this->call($this->manager, 'normalizeNotifications', [ $notification ]));
    }

    /**
     * @return void
     */
    public function testNormalizeNotificationsMany()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification1 */
        $notification1 = $this->getMockBuilder(Notification::class)->getMock();
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification2 */
        $notification2 = $this->getMockBuilder(Notification::class)->getMock();

        $notifications = [ $notification1, $notification2 ];

        $this->assertEquals($notifications, $this->call($this->manager, 'normalizeNotifications', [ $notifications ]));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Expects single
     *
     * @return void
     */
    public function testNormalizeNotificationsManyFail()
    {
        $notifications = [ 'invalid', 123 ];

        $this->assertEquals($notifications, $this->call($this->manager, 'normalizeNotifications', [ $notifications ]));
    }

    /**
     * @return void
     */
    public function testActivatedToggleSingleTrue()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setActive' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('setActive')
            ->with($this->equalTo(true));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->activatedToggle($notification);
    }

    /**
     * @return void
     */
    public function testActivatedToggleSingleFalse()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setActive' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('setActive')
            ->with($this->equalTo(false));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->activatedToggle($notification, false);
    }

    /**
     * @return void
     */
    public function testActivatedToggleMany()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification1 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setActive' ])
            ->getMock();
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification2 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setActive' ])
            ->getMock();

        $notification1
            ->expects($this->once())
            ->method('setActive')
            ->with($this->equalTo(false));

        $notification2
            ->expects($this->once())
            ->method('setActive')
            ->with($this->equalTo(false));

        $this->em
            ->expects($this->at(0))
            ->method('persist')
            ->with($this->equalTo($notification1));

        $this->em
            ->expects($this->at(1))
            ->method('persist')
            ->with($this->equalTo($notification2));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->activatedToggle([ $notification1, $notification2 ], false);
    }

    /**
     * @return void
     */
    public function testPublishedToggleSingleTrue()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setPublished' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('setPublished')
            ->with($this->equalTo(true));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->publishedToggle($notification);
    }

    /**
     * @return void
     */
    public function testPublishedToggleSingleFalse()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setPublished' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('setPublished')
            ->with($this->equalTo(false));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->publishedToggle($notification, false);
    }

    /**
     * @return void
     */
    public function testPublishedToggleManyFalse()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification1 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setPublished' ])
            ->getMock();
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification2 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'setPublished' ])
            ->getMock();

        $notification1
            ->expects($this->once())
            ->method('setPublished')
            ->with($this->equalTo(false));

        $notification2
            ->expects($this->once())
            ->method('setPublished')
            ->with($this->equalTo(false));

        $this->em
            ->expects($this->at(0))
            ->method('persist')
            ->with($this->equalTo($notification1));

        $this->em
            ->expects($this->at(1))
            ->method('persist')
            ->with($this->equalTo($notification2));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->publishedToggle([ $notification1, $notification2 ], false);
    }

    /**
     * @return void
     */
    public function testSubscriptionToggleTrue()
    {
        $recipient = $this->getMockForAbstractClass(AbstractRecipient::class);

        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getRecipients', 'addRecipient' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('getRecipients')
            ->willReturn([]);

        $notification
            ->expects($this->once())
            ->method('addRecipient')
            ->with($this->equalTo($recipient));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->subscriptionToggle($recipient, $notification);
    }

    /**
     * @return void
     */
    public function testSubscriptionToggleTrueWithExists()
    {
        $recipient1 = new RecipientFixture(1);
        $recipient2 = new RecipientFixture(2);

        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification1 */
        $notification1 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getRecipients', 'addRecipient' ])
            ->getMock();

        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification2 */
        $notification2 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getRecipients', 'addRecipient' ])
            ->getMock();

        $notification1
            ->expects($this->once())
            ->method('getRecipients')
            ->willReturn([ $recipient1, $recipient2 ]);

        $notification1
            ->expects($this->never())
            ->method('addRecipient');

        $notification2
            ->expects($this->once())
            ->method('getRecipients')
            ->willReturn([ $recipient2 ]);

        $notification2
            ->expects($this->once())
            ->method('addRecipient')
            ->with($this->equalTo($recipient1));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification2));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->subscriptionToggle($recipient1, [ $notification1, $notification2 ]);
    }

    /**
     * @return void
     */
    public function testSubscriptionToggleFalse()
    {
        $recipient = $this->getMockForAbstractClass(AbstractRecipient::class);

        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getRecipients', 'removeRecipient' ])
            ->getMock();

        $notification
            ->expects($this->never())
            ->method('getRecipients');

        $notification
            ->expects($this->once())
            ->method('removeRecipient')
            ->with($this->equalTo($recipient));

        $this->em
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->manager->subscriptionToggle($recipient, $notification, false);
    }

    /**
     * @return void
     */
    public function testRemoveSingle()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification */
        $notification = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getId' ])
            ->getMock();

        $notification
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $this->em
            ->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($notification));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->conn
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('WHERE notification_id in (1)'));

        $this->manager->remove($notification);
    }

    /**
     * @return void
     */
    public function testRemoveMany()
    {
        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification1 */
        $notification1 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getId' ])
            ->getMock();

        /** @var Notification|\PHPUnit_Framework_MockObject_MockObject $notification2 */
        $notification2 = $this->getMockBuilder(Notification::class)
            ->setMethods([ 'getId' ])
            ->getMock();

        $notification1
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $notification2
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->em
            ->expects($this->at(0))
            ->method('remove')
            ->with($this->equalTo($notification1));

        $this->em
            ->expects($this->at(1))
            ->method('remove')
            ->with($this->equalTo($notification2));

        $this->em
            ->expects($this->once())
            ->method('flush');

        $this->conn
            ->expects($this->once())
            ->method('executeQuery')
            ->with($this->stringContains('WHERE notification_id in (1,2)'));

        $this->manager->remove([ $notification1, $notification2 ]);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->conn = $this->getMockForInterface(Connection::class);
        $this->em = $this->getMockForInterface(EntityManagerInterface::class);
        $this->feedFetcherFactory = $this->getMockForInterface(FeedFetcherFactoryInterface::class);
        $this->configuration = $this->getMockForInterface(ConfigurationImmutableInterface::class);
        $this->extractor = $this->getMockForInterface(DocumentContentExtractorInterface::class);

        $this->em
            ->expects($this->any())
            ->method('getConnection')
            ->willReturn($this->conn);

        $this->manager = new NotificationManager(
            $this->em,
            $this->feedFetcherFactory,
            $this->configuration,
            $this->extractor
        );
    }
}
