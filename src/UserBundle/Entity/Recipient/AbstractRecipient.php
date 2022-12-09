<?php

namespace UserBundle\Entity\Recipient;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Entity\NormalizableEntityInterface;
use AppBundle\Entity\ActivateAwareEntityTrait;
use AppBundle\Entity\BaseEntityTrait;
use AppBundle\Entity\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;
use UserBundle\Enum\NotificationTypeEnum;

/**
 * Class AbstractRecipient
 *
 * @package UserBundle\Entity\Recipient
 *
 * @ORM\Table(name="recipients")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\RecipientRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *  "person"="PersonRecipient",
 *  "group"="GroupRecipient",
 * })
 */
abstract class AbstractRecipient implements
    EntityInterface,
    NormalizableEntityInterface,
    ManageableEntityInterface
{

    use
        BaseEntityTrait,
        ActivateAwareEntityTrait;

    /**
     * The user who created this notification.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", inversedBy="recipients")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * Array of subscriber notification count by types.
     *
     * @var integer[]
     *
     * @ORM\Column(type="array")
     */
    protected $subscribedCount = [];

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="UserBundle\Entity\Notification\Notification",
     *     inversedBy="recipients"
     * )
     * @ORM\JoinTable(name="cross_recipient_notifications")
     */
    protected $notifications;

    /**
     * @var boolean
     */
    public $enrolled;

    /**
     * AbstractRecipient constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->notifications = new ArrayCollection();

        foreach (NotificationTypeEnum::getAvailables() as $available) {
            $this->subscribedCount[$available] = 0;
        }
    }

    /**
     * Set owner
     *
     * @param User $owner The owner of this notification.
     *
     * @return static
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Checks that this entity is owned by specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return boolean
     */
    public function isOwnedBy(User $user)
    {
        return $this->owner->getId() === $user->getId();
    }

    /**
     * Set name
     *
     * @param string $name Group name.
     *
     * @return static
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
     * Set createdAt
     *
     * @param \DateTime $createdAt When this recipient is created.
     *
     * @return static
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
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
     * Set subscribedCount
     *
     * @param array $subscribedCount Array of subscribed notification counts by
     *                               type.
     *
     * @return static
     */
    public function setSubscribedCount(array $subscribedCount)
    {
        $this->subscribedCount = $subscribedCount;

        return $this;
    }

    /**
     * Get subscribedCount
     *
     * @return integer[]
     */
    public function getSubscribedCount()
    {
        return $this->subscribedCount;
    }

    /**
     * @param NotificationTypeEnum $type  A NotificationTypeEnum instance.
     * @param integer              $count New subscription count.
     *
     * @return static
     */
    public function setSubscribedCountByType(NotificationTypeEnum $type, $count)
    {
        $this->subscribedCount[(string) $type] = $count;

        return $this;
    }

    /**
     * @param NotificationTypeEnum $type A NotificationTypeEnum instance.
     *
     * @return integer
     */
    public function getSubscribedCountByType(NotificationTypeEnum $type)
    {
        return $this->subscribedCount[(string) $type];
    }

    /**
     * @param NotificationTypeEnum $type A NotificationTypeEnum instance.
     *
     * @return static
     */
    public function incSubscribedCountByType(NotificationTypeEnum $type)
    {
        $this->subscribedCount[(string) $type]++;

        return $this;
    }

    /**
     * @param NotificationTypeEnum $type A NotificationTypeEnum instance.
     *
     * @return static
     */
    public function decSubscribedCountByType(NotificationTypeEnum $type)
    {
        $this->subscribedCount[(string) $type]--;

        return $this;
    }

    /**
     * Add notification
     *
     * @param Notification $notification A Notification entity instance.
     *
     * @return static
     */
    public function addNotification(Notification $notification)
    {
        $this->notifications[] = $notification;
        $this->incSubscribedCountByType($notification->getNotificationType());

        return $this;
    }

    /**
     * Remove notification
     *
     * @param Notification $notification A Notification entity instance.
     *
     * @return static
     */
    public function removeNotification(Notification $notification)
    {
        $this->notifications->removeElement($notification);
        $this->decSubscribedCountByType($notification->getNotificationType());

        return $this;
    }

    /**
     * Get notifications
     *
     * @return Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Return default normalization groups.
     *
     * @return array
     */
    public function defaultGroups()
    {
        return [ 'id', 'recipient' ];
    }
}
