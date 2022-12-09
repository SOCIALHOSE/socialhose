<?php

namespace AppBundle\Form\Type\Extension;

use AppBundle\Utils\TransKey\ConstTransKeyGenerator;
use AppBundle\Utils\TransKey\RecursiveTransKeyGenerator;
use AppBundle\Utils\TransKey\TransKeyGeneratorInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LocalizationTypeExtension
 * @package AppBundle\Form\Type\Extension
 */
class LocalizationTypeExtension extends AbstractTypeExtension
{

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('key', new RecursiveTransKeyGenerator())
            ->addAllowedTypes('key', [ 'string', TransKeyGeneratorInterface::class ])
            ->setNormalizer('key', function (Options $options, $key) {
                //
                // We should insure that key is always be an instance of trans key
                // generator.
                //
                if (is_string($key)) {
                    $key = new ConstTransKeyGenerator($key);
                }

                return $key;
            });
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended.
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
