<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class StoredQueryStatusEnum
 * @package Common\Enum
 */
class StoredQueryStatusEnum extends AbstractEnum
{

    /**
     * Stored query just added and documents not fetched and indexed.
     */
    const INITIALIZE = 'initialize';

    /**
     * All documents fetched and synced with eexternal api.
     */
    const SYNCED = 'synced';

    /**
     * Fetch new documents from external api founded for stored query.
     */
    const UPDATING = 'updating';

    /**
     * This query marked as deleted.
     */
    const DELETED = 'deleted';
}
