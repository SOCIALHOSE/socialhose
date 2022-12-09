<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\SiteSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ConfigParametersType
 * @package AdminBundle\Form
 */
class ConfigParametersSectionType extends AbstractType
{

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder.
     * @param array                $options The options.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $parameters = $builder->getData();

        $checker = \nspl\f\rpartial('\app\op\isInstanceOf', SiteSettings::class);
        if (! \nspl\a\all($parameters, $checker)) {
            throw new \LogicException(
                'Each \'parameters\' item should be instance of '
                . SiteSettings::class
            );
        }

        $parameters = \app\a\group(\nspl\op\methodCaller('getSection'), $parameters);

        foreach ($parameters as $section => $sectionParams) {
            $builder->add($section, ConfigParametersType::class, [
                'data' => $sectionParams,
            ]);
        }
    }
}
