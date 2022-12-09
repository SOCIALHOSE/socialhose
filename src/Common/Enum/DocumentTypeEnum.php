<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class DocumentTypeEnum
 * @package Common\Enum
 */
class DocumentTypeEnum extends AbstractEnum
{

    const WEBLOG = 'WEBLOG';
    const SOCIAL_MEDIA = 'SOCIAL_MEDIA';
    const MAINSTREAM_NEWS = 'MAINSTREAM_NEWS';
    const MICROBLOG = 'MICROBLOG';
    const MEMETRACKER = 'MEMETRACKER';
    const REVIEW = 'REVIEW';
    const VIDEOS = 'VIDEOS';
    const PHOTOS = 'PHOTOS';
    const FORUM = 'FORUM';
}
