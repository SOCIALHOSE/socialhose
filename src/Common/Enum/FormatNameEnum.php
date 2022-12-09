<?php

namespace Common\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class FormatNameEnum
 *
 * @package Common\Enum
 *
 * @method static FormatNameEnum rss()
 * @method static FormatNameEnum atom()
 * @method static FormatNameEnum tsv()
 * @method static FormatNameEnum html()
 */
class FormatNameEnum extends AbstractEnum
{

    const RSS = 'rss';
    const ATOM = 'atom';
    const TSV = 'tsv';
    const HTML = 'html';
}
