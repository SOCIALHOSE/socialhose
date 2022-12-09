<?php

namespace Tests\UserBundle\Manager\Notification;

use ApiBundle\Serializer\Metadata\Metadata;
use UserBundle\Entity\Recipient\AbstractRecipient;

/**
 * Class NotificationManagerTest
 * @package UserBundle\Manager\Notification
 */
class RecipientFixture extends AbstractRecipient
{

    /**
     * RecipientFixture constructor.
     *
     * @param string|integer $id Entity id.
     */
    public function __construct($id)
    {
        parent::__construct();
        $this->id = $id;
    }

    /**
     * Return fqcn of form used for creating this entity.
     *
     * @return string
     */
    public function getCreateFormClass()
    {
        return '';
    }

    /**
     * Return fqcn of form used for updating this entity.
     *
     * @return string
     */
    public function getUpdateFormClass()
    {
        return '';
    }

    /**
     * Return metadata for current entity.
     *
     * @return \ApiBundle\Serializer\Metadata\Metadata
     */
    public function getMetadata()
    {
        return new Metadata(static::class);
    }
}
