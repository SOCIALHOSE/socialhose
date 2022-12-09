<?php

namespace UserBundle\Enum;

use AppBundle\Enum\AbstractEnum;

/**
 * Class ThemeTypeEnum
 * @package UserBundle\Enum
 *
 * @method static FontFamilyEnum arial()
 * @method static FontFamilyEnum calibri()
 * @method static FontFamilyEnum centuryGothic()
 * @method static FontFamilyEnum courierNew()
 * @method static FontFamilyEnum georgia()
 * @method static FontFamilyEnum lucidaSansUnicode()
 * @method static FontFamilyEnum myriadProRegular()
 * @method static FontFamilyEnum tahoma()
 * @method static FontFamilyEnum timesNewRoman()
 * @method static FontFamilyEnum trebuchet()
 * @method static FontFamilyEnum verdana()
 */
class FontFamilyEnum extends AbstractEnum
{

    const ARIAL = 'Arial';
    const CALIBRI = 'Calibri';
    const CENTURY_GOTHIC = 'Century Gothic';
    const COURIER_NEW = 'Courier New';
    const GEORGIA = 'Georgia';
    const LUCIDA_SANS_UNICODE = 'Lucida Sans Unicode';
    const MYRIAD_PRO_REGULAR = 'Myriad Pro Regular';
    const TAHOMA = 'Tahoma';
    const TIMES_NEW_ROMAN = 'Times New Roman';
    const TREBUCHET = 'Trebuchet';
    const VERDANA = 'Verdana';

    private static $nameToFamilyMap = [
        FontFamilyEnum::ARIAL => 'Arial,helvetica,sans-serif',
        FontFamilyEnum::CALIBRI => 'Calibri,Helvetica,sans-serif',
        FontFamilyEnum::CENTURY_GOTHIC => '\'Century Gothic\',CenturyGothic,AppleGothic,sans-serif',
        FontFamilyEnum::COURIER_NEW => '\'Courier new\',courier,monospace',
        FontFamilyEnum::GEORGIA => 'Georgia,serif',
        FontFamilyEnum::LUCIDA_SANS_UNICODE => '\'Lucida Sans Unicode\',sans-serif',
        FontFamilyEnum::MYRIAD_PRO_REGULAR => 'MyriadPro-Regular,\'Lucida Sans Unicode\',\'Lucida Grande\',sans-serif',
        FontFamilyEnum::TAHOMA => '\'Tahoma Verdana\',Segoe,sans-serif',
        FontFamilyEnum::TIMES_NEW_ROMAN => 'TimesNewRoman,serif',
        FontFamilyEnum::TREBUCHET => 'Trebuchet,Trebuchet MS,sans-serif',
        FontFamilyEnum::VERDANA => 'Verdana,geneva,sans-serif',
    ];

    /**
     * Get css font family option.
     *
     * @return string
     */
    public function getCss()
    {
        return self::$nameToFamilyMap[$this->value];
    }
}
