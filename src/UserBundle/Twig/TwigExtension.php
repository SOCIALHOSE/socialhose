<?php

namespace UserBundle\Twig;

use Symfony\Component\Intl\Intl;

/**
 * Class TwigExtension
 *
 * @package UserBundle\Twig
 */
class TwigExtension extends \Twig_Extension
{

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return \Twig_SimpleFilter[]
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('countryName', function ($code) {
                return Intl::getRegionBundle()->getCountryName($code);
            }),
        ];
    }
}
