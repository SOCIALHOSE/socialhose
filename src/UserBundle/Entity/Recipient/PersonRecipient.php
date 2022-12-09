<?php

namespace UserBundle\Entity\Recipient;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\User;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Enum\RecipientTypeEnum;
use UserBundle\Form\PersonRecipientType;

/**
 * Class PersonRecipient
 *
 * @package UserBundle\Entity\Recipient
 *
 * @ORM\Entity(repositoryClass="UserBundle\Repository\PersonRecipientRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PersonRecipient extends AbstractRecipient
{

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected $email;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(
     *     targetEntity="UserBundle\Entity\Recipient\GroupRecipient",
     *     mappedBy="recipients"
     * )
     */
    protected $groups;

    /**
     * @var User
     *
     * @ORM\OneToOne(
     *     targetEntity="UserBundle\Entity\User",
     *     mappedBy="recipient",
     *     cascade={ "ALL" }
     * )
     */
    protected $associatedUser;

    /**
     * AbstractRecipient constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->groups = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->firstName .' '. $this->lastName;
    }

    /**
     * Create recipient from user.
     *
     * @param User $user A User entity instance.
     *
     * @return PersonRecipient
     */
    public static function createFromUser(User $user)
    {
        return static::create()
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setEmail($user->getEmail());
    }

    /**
     * Set firstName
     *
     * @param string $firstName Person first name.
     *
     * @return PersonRecipient
     */
    public function setFirstName($firstName)
    {
        $this->firstName = trim($firstName);
        $this->name = $this->firstName .' '. $this->lastName;

        if (($this->associatedUser !== null) && ($this->associatedUser->getFirstName() !== $firstName)) {
            $this->associatedUser->setFirstName($firstName);
        }

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName Person last name.
     *
     * @return PersonRecipient
     */
    public function setLastName($lastName)
    {
        $this->lastName = trim($lastName);
        $this->name = $this->firstName .' '. $this->lastName;

        if (($this->associatedUser !== null) && ($this->associatedUser->getLastName() !== $lastName)) {
            $this->associatedUser->setLastName($lastName);
        }

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name Group name.
     *
     * @return AbstractRecipient
     */
    public function setName($name)
    {
        list($firstName, $lastName) = explode(' ', $name, 2);

        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);

        $this->name = $this->firstName .' '. $this->lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email Person email.
     *
     * @return PersonRecipient
     */
    public function setEmail($email)
    {
        $this->email = $email;

        if (($this->associatedUser !== null) && ($this->associatedUser->getEmail() !== $email)) {
            $this->associatedUser->setEmail($email);
        }

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Add group
     *
     * @param GroupRecipient $group A GroupRecipient entity instance.
     *
     * @return PersonRecipient
     */
    public function addGroup(GroupRecipient $group)
    {
        $this->groups[] = $group;
        $group->addRecipient($this);

        return $this;
    }

    /**
     * Remove group
     *
     * @param GroupRecipient $group A GroupRecipient entity instance.
     *
     * @return PersonRecipient
     */
    public function removeGroup(GroupRecipient $group)
    {
        $this->groups->removeElement($group);
        $group->removeRecipient($this);

        return $this;
    }

    /**
     * Get groups
     *
     * @return Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return User $associatedUser
     */
    public function getAssociatedUser()
    {
        return $this->associatedUser;
    }

    /**
     * @param User $associatedUser Associated user.
     *
     * @return PersonRecipient
     */
    public function setAssociatedUser(User $associatedUser = null)
    {
        if ($associatedUser === null) {
            $this->associatedUser->setRecipient(null);
        } else {
            $associatedUser->setRecipient($this);
        }

        $this->associatedUser = $associatedUser;

        return $this;
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        $subscriptions = array_map(function ($type) {
            return PropertyMetadata::createInteger($type, [ 'recipient' ])
                ->setField(function () use ($type) {
                    return $this->subscribedCount[$type];
                });
        }, NotificationTypeEnum::getAvailables());
        $subscriptions[] = PropertyMetadata::createArray('ids', [ 'recipient' ])
            ->setField(function () {
                return $this->getNotifications()->map(function (Notification $notification) {
                    return $notification->getId();
                })->toArray();
            });

        return new Metadata(static::class, [
            PropertyMetadata::createInteger('id', [ 'id' ]),
            PropertyMetadata::createString('firstName', [ 'recipient' ]),
            PropertyMetadata::createString('lastName', [ 'recipient' ]),
            PropertyMetadata::createString('name', [ 'notification', 'notification_list', 'recipient_autocompletion' ]),
            PropertyMetadata::createString('email', [ 'recipient', 'notification', 'notification_list', 'recipient_autocompletion' ]),
            PropertyMetadata::createDate('creationDate', [ 'recipient' ])
                ->setField('createdAt'),
            PropertyMetadata::groupProperties('subscriptions', $subscriptions, [ 'recipient' ]),
            PropertyMetadata::createCollection('groups', GroupRecipient::class, [ 'recipient' ]),
            PropertyMetadata::createBoolean('active', [ 'recipient' ]),
            PropertyMetadata::createBoolean('enrolled', [ 'sublist' ]),
        ]);
    }

    /**
     * Get entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return RecipientTypeEnum::PERSON;
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return PersonRecipientType::class;
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return PersonRecipientType::class;
    }

    /**
     * @ORM\PreRemove
     *
     * @param LifecycleEventArgs $event A LifecycleEventArgs instance.
     *
     * @return void
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        $em = $event->getObjectManager();

        foreach ($this->groups as $group) {
            $group->removeRecipient($this);
            $em->persist($group);
        }
    }
}
