<?php

namespace UserBundle\Entity\Notification;

use ApiBundle\Entity\NormalizableEntityInterface;
use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use AppBundle\Entity\ActivateAwareEntityTrait;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use AppBundle\Entity\OwnerAwareEntityTrait;
use CacheBundle\Entity\Feed\AbstractFeed;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Entity\Subscription\AbstractSubscription;
use UserBundle\Entity\User;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Enum\ThemeTypeEnum;

/**
 * Class Notification
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity(
 *     repositoryClass="UserBundle\Repository\NotificationRepository"
 * )
 */
class Notification implements EntityInterface, NormalizableEntityInterface
{

    use
        BaseEntityTrait,
        ActivateAwareEntityTrait,
        OwnerAwareEntityTrait;

    /**
     * Notification name.
     *
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="UserBundle\Entity\Recipient\AbstractRecipient",
     *     mappedBy="notifications"
     * )
     */
    protected $recipients;

    /**
     * Notification subject.
     *
     * @var string
     *
     * @ORM\Column(nullable=true)
     */
    protected $subject = '';

    /**
     * If true, subject will be auto generated.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $automatedSubject = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $published = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $allowUnsubscribe = true;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $unsubscribeNotification = true;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="CacheBundle\Entity\Feed\AbstractFeed")
     * @ORM\JoinTable(name="cross_notifications_feeds")
     */
    protected $feeds;

    /**
     * @var NotificationTypeEnum
     *
     * @ORM\Column(type="notification_type")
     */
    protected $notificationType;

    /**
     * @var ThemeTypeEnum
     *
     * @ORM\Column(type="theme_type")
     */
    protected $themeType;

    /**
     * Main theme of notification.
     *
     * @var NotificationTheme
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\Notification\NotificationTheme")
     */
    protected $theme;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $enhancedThemeOptionsDiff = [];

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $plainThemeOptionsDiff = [];

    /**
     * Send this notification even if we don't get updates.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $sendWhenEmpty = false;

    /**
     * Time zone name.
     *
     * @var \DateTimeZone
     *
     * @ORM\Column(type="datetimezone")
     * @Assert\NotBlank
     */
    protected $timezone;

    /**
     * Notification schedule.
     *
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule",
     *     mappedBy="notification",
     *     cascade={ "persist", "remove"}
     * )
     */
    protected $schedules;

    /**
     * Notification render history.
     *
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="UserBundle\Entity\Notification\NotificationSendHistory",
     *     mappedBy="notification",
     *     cascade={ "persist", "remove" }
     * )
     */
    protected $history;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", nullable=true)
     */
    protected $sendUntil;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $sourcesCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastSentAt;

    /**
     * @var AbstractSubscription
     *
     * @ORM\ManyToOne(
     *     targetEntity="UserBundle\Entity\Subscription\AbstractSubscription",
     *     inversedBy="notifications"
     * )
     */
    protected $billingSubscription;

    /**
     * @var boolean
     */
    public $subscribed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->feeds = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        $this->history = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->lastSentAt = clone $this->createdAt;
    }

    /**
     * Set name
     *
     * @param string $name Notification name.
     *
     * @return Notification
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set subject
     *
     * @param string $subject Notification email subject.
     *
     * @return Notification
     */
    public function setSubject($subject)
    {
        $this->subject = trim($subject);

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set automatedSubject
     *
     * @param boolean $automatedSubject Flag, subject will be auto generated if
     *                                  set.
     *
     * @return Notification
     */
    public function setAutomatedSubject($automatedSubject = true)
    {
        $this->automatedSubject = (bool) $automatedSubject;

        return $this;
    }

    /**
     * Get automatedSubject
     *
     * @return boolean
     */
    public function isAutomatedSubject()
    {
        return $this->automatedSubject;
    }

    /**
     * Set published
     *
     * @param boolean $published Flag, allow everybody to subscribe if set.
     *
     * @return Notification
     */
    public function setPublished($published = true)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * Set allowUnsubscribe
     *
     * @param boolean $allowUnsubscribe Allow to unsubscribe.
     *
     * @return Notification
     */
    public function setAllowUnsubscribe($allowUnsubscribe = true)
    {
        $this->allowUnsubscribe = $allowUnsubscribe;

        return $this;
    }

    /**
     * Get allowUnsubscribe
     *
     * @return boolean
     */
    public function isAllowUnsubscribe()
    {
        return $this->allowUnsubscribe;
    }

    /**
     * Set unsubscribeNotification
     *
     * @param boolean $unsubscribeNotification Notify owner if somebody is
     *                                         unsubscribed.
     *
     * @return Notification
     */
    public function setUnsubscribeNotification($unsubscribeNotification = true)
    {
        $this->unsubscribeNotification = $unsubscribeNotification;

        return $this;
    }

    /**
     * Get unsubscribeNotification
     *
     * @return boolean
     */
    public function isUnsubscribeNotification()
    {
        return $this->unsubscribeNotification;
    }

    /**
     * Set sendWhenEmpty
     *
     * @param boolean $sendWhenEmpty Flag, render notification even if we don't
     *                               get new documents.
     *
     * @return Notification
     */
    public function setSendWhenEmpty($sendWhenEmpty = true)
    {
        $this->sendWhenEmpty = $sendWhenEmpty;

        return $this;
    }

    /**
     * Get sendWhenEmpty
     *
     * @return boolean
     */
    public function isSendWhenEmpty()
    {
        return $this->sendWhenEmpty;
    }

    /**
     * Set timezone
     *
     * @param \DateTimeZone $timezone Notification timezone.
     *
     * @return Notification
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return \DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set sendUntil
     *
     * @param \DateTime $sendUntil Date until we render notification.
     *
     * @return Notification
     */
    public function setSendUntil(\DateTime $sendUntil = null)
    {
        $this->sendUntil = $sendUntil;

        return $this;
    }

    /**
     * Get sendUntil
     *
     * @return \DateTime|null
     */
    public function getSendUntil()
    {
        return $this->sendUntil;
    }

    /**
     * Add recipient
     *
     * @param AbstractRecipient $recipient Who will receive this notifications.
     *
     * @return Notification
     */
    public function addRecipient(AbstractRecipient $recipient)
    {
        $this->recipients[] = $recipient;
        $recipient->addNotification($this);

        return $this;
    }

    /**
     * Remove recipient
     *
     * @param AbstractRecipient $recipient Who will not receive notifications.
     *
     * @return Notification
     */
    public function removeRecipient(AbstractRecipient $recipient)
    {
        $this->recipients->removeElement($recipient);
        $recipient->removeNotification($this);

        return $this;
    }

    /**
     * Get recipients
     *
     * @return Collection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Set feeds.
     *
     * @param array $feeds Array of AbstractFeed entity instances.
     *
     * @return Notification
     */
    public function setFeeds(array $feeds = [])
    {
        $this->feeds = new ArrayCollection($feeds);
        $this->recomputeSourceCount();

        return $this;
    }

    /**
     * Add feed
     *
     * @param AbstractFeed $feed A AbstractFeed instance.
     *
     * @return Notification
     */
    public function addFeed(AbstractFeed $feed)
    {
        $this->feeds[] = $feed;
        $this->sourcesCount++;

        return $this;
    }

    /**
     * Remove feed
     *
     * @param AbstractFeed $feed A AbstractFeed instance.
     *
     * @return Notification
     */
    public function removeFeed(AbstractFeed $feed)
    {
        $this->feeds->removeElement($feed);
        $this->sourcesCount--;

        return $this;
    }

    /**
     * Get feeds
     *
     * @return Collection
     */
    public function getFeeds()
    {
        return $this->feeds;
    }

    /**
     * Add schedule
     *
     * @param AbstractNotificationSchedule $schedule A
     *                                               AbstractNotificationSchedule
     *                                               instance.
     *
     * @return Notification
     */
    public function addSchedule(AbstractNotificationSchedule $schedule)
    {
        $this->schedules[] = $schedule;
        $schedule->setNotification($this);

        return $this;
    }

    /**
     * Remove schedule
     *
     * @param AbstractNotificationSchedule $schedule A
     *                                               AbstractNotificationSchedule
     *                                               instance.
     *
     * @return Notification
     */
    public function removeSchedule(AbstractNotificationSchedule $schedule)
    {
        $this->schedules->removeElement($schedule);
        $schedule->setNotification(null);

        return $this;
    }

    /**
     * Set schedules
     *
     * @param AbstractNotificationSchedule[]|array $schedules Array of
     *                                                        AbstractNotificationSchedule
     *                                                        instance's.
     *
     * @return Notification
     */
    public function setSchedules(array $schedules)
    {
//        $valid = \Functional\every($schedules, function ($schedule) {
//            return $schedule instanceof AbstractNotificationSchedule;
//        });
        $valid = \nspl\a\all($schedules, \nspl\f\rpartial('\app\op\isInstanceOf', AbstractNotificationSchedule::class));

        if (! $valid) {
            throw new \InvalidArgumentException('Expects array of AbstractNotificationSchedule instance\'s');
        }

        $this->schedules = new ArrayCollection($schedules);
        /** @var AbstractNotificationSchedule $schedule */
        foreach ($this->schedules as $schedule) {
            $schedule->setNotification($this);
        }

        return $this;
    }

    /**
     * Get schedules
     *
     * @return Collection
     */
    public function getSchedules()
    {
        return $this->schedules;
    }

    /**
     * Add history
     *
     * @param NotificationSendHistory $history A NotificationSendHistory entity
     *                                         instance.
     *
     * @return Notification
     */
    public function addHistory(NotificationSendHistory $history)
    {
        $this->history[] = $history;
        $history->setNotification($this);

        return $this;
    }

    /**
     * Remove history
     *
     * @param NotificationSendHistory $history A NotificationSendHistory entity
     *                                         instance.
     *
     * @return Notification
     */
    public function removeHistory(NotificationSendHistory $history)
    {
        $this->history->removeElement($history);
        $history->setNotification(null);

        return $this;
    }

    /**
     * Get history
     *
     * @return Collection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set sourcesCount
     *
     * @param integer $sourcesCount How mush sources bind to this notification.
     *
     * @return Notification
     */
    public function setSourcesCount($sourcesCount)
    {
        $this->sourcesCount = $sourcesCount;

        return $this;
    }

    /**
     * Get sourcesCount
     *
     * @return integer
     */
    public function getSourcesCount()
    {
        return $this->sourcesCount;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set lastSentAt
     *
     * @param \DateTime|null $lastSentAt When this notification was last sent.
     *
     * @return static
     */
    public function setLastSentAt(\DateTime $lastSentAt = null)
    {
        $this->lastSentAt = $lastSentAt;

        return $this;
    }

    /**
     * Get lastSentAt
     *
     * @return \DateTime
     */
    public function getLastSentAt()
    {
        return $this->lastSentAt;
    }

    /**
     * Checks that this notification can be sent.
     *
     * @param \DateTime $sendDate When this notification is attempted to render.
     *
     * @return boolean
     */
    public function isCanBeSent(\DateTime $sendDate)
    {
        //
        // Notification can be render if:
        // * have at least one source, schedule and recipient.
        // * is active.
        // * if render until field is defined and render date is before it.
        //
        return ($this->sourcesCount > 0) && (count($this->schedules) > 0)
            && (count($this->recipients) > 0) && $this->active
            && (($this->sendUntil === null) || ($this->sendUntil >= $sendDate));
    }

    /**
     * Recompute source count.
     *
     * @return void
     */
    private function recomputeSourceCount()
    {
        $this->sourcesCount = count($this->feeds);
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createString('name', [ 'notification', 'notification_list' ]),
            PropertyMetadata::createCollection('recipients', AbstractRecipient::class, [ 'notification', 'notification_list' ]),
            PropertyMetadata::createEntity('owner', User::class, [ 'notification', 'notification_list' ]),
            PropertyMetadata::createString('subject', [ 'notification' ])
                ->setNullable(true),
            PropertyMetadata::createEnum('themeType', ThemeTypeEnum::class, [ 'notification', 'notification_list' ]),
            PropertyMetadata::createInteger('theme', [ 'notification' ])
                ->setField(function () {
                    return $this->theme->getId();
                }),
            PropertyMetadata::createBoolean('automatedSubject', [ 'notification' ]),
            PropertyMetadata::createBoolean('published', [ 'notification', 'notification_list' ]),
            PropertyMetadata::createBoolean('allowUnsubscribe', [ 'notification', 'notification_list' ]),
            PropertyMetadata::createBoolean('unsubscribeNotification', [ 'notification' ]),
            PropertyMetadata::createArray('sources', [ 'notification' ])
                ->setField(function () {
                    $feeds = $this->feeds->map(function (AbstractFeed $feed) {
                        return [
                            'type' => 'feed',
                            'id' => $feed->getId(),
                            'name' => $feed->getName(),
                            'class' => $feed->getSpecificType(),
                        ];
                    })->toArray();

                    return $feeds;
                }),

            PropertyMetadata::createBoolean('sendWhenEmpty', [ 'notification' ]),
            PropertyMetadata::createString('timezone', [ 'notification' ])
                ->setField(function () {
                    return $this->timezone->getName();
                }),
            PropertyMetadata::createCollection('automatic', AbstractNotificationSchedule::class, [ 'notification', 'notification_list' ])
                ->setField('schedules'),
            PropertyMetadata::createString('sendUntil', [ 'notification' ])
                ->setNullable(true)
                ->setField(function () {
                    return \app\op\invokeIf($this->sendUntil, 'format', [ 'Y-m-d' ]);
                }),
            PropertyMetadata::createBoolean('active', [ 'notification', 'notification_list' ]),
            PropertyMetadata::createInteger('sourcesCount', [ 'notification_list' ]),
            PropertyMetadata::createBoolean('subscribed', [ 'notification_list' ]),
            PropertyMetadata::createObject('plainDiff', [ 'notification' ])
                ->setField('plainThemeOptionsDiff'),
            PropertyMetadata::createObject('enhancedDiff', [ 'notification' ])
                ->setField('enhancedThemeOptionsDiff'),
        ]);
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'notification', 'schedule', 'id' ];
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return (string) $this->notificationType;
    }

    /**
     * Set notificationType
     *
     * @param NotificationTypeEnum $notificationType A NotificationTypeEnum instance.
     *
     * @return Notification
     */
    public function setNotificationType(NotificationTypeEnum $notificationType)
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * Get notificationType
     *
     * @return NotificationTypeEnum
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * Set themeType
     *
     * @param ThemeTypeEnum $themeType A ThemeTypeEnum instance.
     *
     * @return Notification
     */
    public function setThemeType(ThemeTypeEnum $themeType)
    {
        $this->themeType = $themeType;

        return $this;
    }

    /**
     * Get themeType
     *
     * @return ThemeTypeEnum
     */
    public function getThemeType()
    {
        return $this->themeType;
    }

    /**
     * Set enhancedThemeOptionsDiff
     *
     * @param array $enhancedThemeOptionsDiff Difference between theme and current
     *                                        notification options for enhanced
     *                                        layout.
     *
     * @return Notification
     */
    public function setEnhancedThemeOptionsDiff(array $enhancedThemeOptionsDiff)
    {
        $this->enhancedThemeOptionsDiff = $enhancedThemeOptionsDiff;

        return $this;
    }

    /**
     * Get enhancedThemeOptionsDiff
     *
     * @return array
     */
    public function getEnhancedThemeOptionsDiff()
    {
        return $this->enhancedThemeOptionsDiff;
    }

    /**
     * Set plainThemeOptionsDiff
     *
     * @param array $plainThemeOptionsDiff Difference between theme and current
     *                                     notification options for plain layout.
     *
     * @return Notification
     */
    public function setPlainThemeOptionsDiff(array $plainThemeOptionsDiff)
    {
        $this->plainThemeOptionsDiff = $plainThemeOptionsDiff;

        return $this;
    }

    /**
     * Get plainThemeOptionsDiff
     *
     * @return array
     */
    public function getPlainThemeOptionsDiff()
    {
        return $this->plainThemeOptionsDiff;
    }

    /**
     * Set theme
     *
     * @param NotificationTheme $theme A NotificationTheme entity instance.
     *
     * @return Notification
     */
    public function setTheme(NotificationTheme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return NotificationTheme
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Get actual theme options.
     *
     * It's merge between selected layout theme options and notification specific
     * changes.
     *
     * @return NotificationThemeOptions
     */
    public function getActualThemeOptions()
    {
        $isEnhanced = $this->themeType->is(ThemeTypeEnum::ENHANCED);

        $baseOptions = $isEnhanced ? clone $this->theme->getEnhanced() : clone $this->theme->getPlain();
        $diff = $isEnhanced ? $this->enhancedThemeOptionsDiff : $this->plainThemeOptionsDiff;

        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableMagicCall()
            ->getPropertyAccessor();
        foreach ($diff as $path => $value) {
            if ($value !== null) {
                $path = str_replace(':', '.', $path);
                $accessor->setValue($baseOptions, $path, $value);
            }
        }

        return $baseOptions;
    }

    /**
     * Set billingSubscription
     *
     * @param AbstractSubscription $billingSubscription A billing subscription
     *                                                  entity instance.
     *
     * @return Notification
     */
    public function setBillingSubscription(AbstractSubscription $billingSubscription = null)
    {
        $this->billingSubscription = $billingSubscription;

        return $this;
    }

    /**
     * Get billingSubscription
     *
     * @return AbstractSubscription
     */
    public function getBillingSubscription()
    {
        return $this->billingSubscription;
    }
}
