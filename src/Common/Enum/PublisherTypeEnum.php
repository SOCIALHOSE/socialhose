<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class PublisherTypeEnum
 * Enum of internal publisher types.
 *
 * @package Common\Enum
 *
 * @method static PublisherTypeEnum unknown()
 * @method static PublisherTypeEnum blogs()
 * @method static PublisherTypeEnum socials()
 * @method static PublisherTypeEnum news()
 * @method static PublisherTypeEnum videos()
 * @method static PublisherTypeEnum forums()
 * @method static PublisherTypeEnum photo()
 */
class PublisherTypeEnum extends AbstractEnum
{

    const UNKNOWN = 'unknown';
    const BLOGS = 'blogs';
    const SOCIAL = 'socials';
    const NEWS = 'news';
    const VIDEO = 'videos';
    const FORUMS = 'forums';
    const PHOTO = 'photo';
}
