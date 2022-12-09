<?php

namespace UserBundle\Entity\Recipient;

use ApiBundle\Serializer\Metadata\Metadata;
use ApiBundle\Serializer\Metadata\PropertyMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Enum\RecipientTypeEnum;
use UserBundle\Form\GroupRecipientType;

/**
 * Class GroupRecipient
 *
 * @package UserBundle\Entity\Recipient
 *
 * @ORM\Entity(repositoryClass="UserBundle\Repository\GroupRecipientRepository")
 */
class GroupRecipient extends AbstractRecipient
{

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $recipientsNumber = 0;

    /**
     * @var Collection
     *
     * We use 'EXTRA_LAZY' in order to avoid fetching all recipients when we need
     * only compute numbers of recipient.
     *
     * @ORM\ManyToMany(
     *     targetEntity="UserBundle\Entity\Recipient\PersonRecipient",
     *     inversedBy="groups",
     *     fetch="EXTRA_LAZY"
     * )
     * @ORM\JoinTable(name="cross_groups_persons")
     */
    protected $recipients;

    /**
     * AbstractRecipient constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->recipients = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description Group description.
     *
     * @return GroupRecipient
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set personsCount
     *
     * @param integer $recipientsNumber Count of person in group.
     *
     * @return GroupRecipient
     */
    public function setRecipientsNumber($recipientsNumber)
    {
        $this->recipientsNumber = $recipientsNumber;

        return $this;
    }

    /**
     * Get personsCount
     *
     * @return integer
     */
    public function getRecipientsNumber()
    {
        return $this->recipientsNumber;
    }

    /**
     * Add person
     *
     * @param PersonRecipient $person A PersonRecipient entity instance.
     *
     * @return GroupRecipient
     */
    public function addRecipient(PersonRecipient $person)
    {
        $this->recipients[] = $person;
        $this->recipientsNumber++;

        return $this;
    }

    /**
     * Remove person
     *
     * @param PersonRecipient $person A PersonRecipient entity instance.
     *
     * @return GroupRecipient
     */
    public function removeRecipient(PersonRecipient $person)
    {
        $this->recipients->removeElement($person);
        $this->recipientsNumber--;

        return $this;
    }

    /**
     * Get persons
     *
     * @return Collection
     */
    public function getRecipients()
    {
        return $this->recipients;
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
            PropertyMetadata::createString('name', [ 'recipient', 'notification', 'notification_list', 'recipient_autocompletion' ]),
            PropertyMetadata::createString('email', [ 'notification', 'notification_list', 'recipient_autocompletion' ])
                ->setField(function () {
                    return '';
                }),
            PropertyMetadata::createString('description', [ 'recipient' ]),
            PropertyMetadata::groupProperties('subscriptions', $subscriptions, [ 'recipient' ]),
            PropertyMetadata::createArray('recipients', [ 'recipient' ])
                ->setField(function () {
                    return $this->getRecipients()->map(function (PersonRecipient $recipient) {
                        return $recipient->getId();
                    })->toArray();
                }),
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
        return RecipientTypeEnum::GROUP;
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return GroupRecipientType::class;
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return GroupRecipientType::class;
    }
}
