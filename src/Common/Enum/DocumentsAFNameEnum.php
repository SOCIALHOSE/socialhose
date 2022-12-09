<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class DocumentsAFNameEnum
 * Available advanced filters names for documents.
 *
 * @package Common\Enum
 */
class DocumentsAFNameEnum extends AbstractEnum
{

    const ADDITIONAL_QUERY = 'keyword';
    const SOURCE = 'source';
    const ARTICLE_DATE = 'articleDate';
    const SOURCE_COUNTRY = 'sourceCountry';
    const SOURCE_STATE = 'sourceState';
    const SOURCE_CITY = 'sourceCity';
//    const SOURCE_SECTION = 'sourceSection';
    const ARTICLE_LANGUAGE = 'articleLanguage';
    const AUTHOR = 'author';
    const PUBLISHER = 'publisher';
    const REACH = 'reach';
    const SENTIMENT= 'sentiment';

}
