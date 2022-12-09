<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class AppLimitEnum
 * @package UserBundle\Enum
 *
 * @method static AppLimitEnum searches()
 * @method static AppLimitEnum feeds()
 * @method static AppLimitEnum masterAccounts()
 * @method static AppLimitEnum subscriberAccounts()
 * @method static AppLimitEnum alerts()
 * @method static AppLimitEnum newsletters()
 * @method static AppLimitEnum webfeeds()
 */
class AppLimitEnum extends AbstractEnum
{

    const SEARCHES = 'searchesPerDay';
    const FEEDS = 'savedFeeds';
    const MASTER_ACCOUNTS = 'masterAccounts';
    const SUBSCRIBER_ACCOUNTS = 'subscriberAccounts';
    const ALERTS = 'alerts';
    const NEWSLETTERS = 'newsletters';
    const WEBFEEDS = 'webFeeds';
}
