<?php

namespace AppBundle\Form\Type\AdvancedFilter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdvancedFilterType
 *
 * Convert filter parameters passed from frontend into internal representation.
 *
 * @package AppBundle\Form\Type\AdvancedFilte
 *
 * @see \AppBundle\Form\Type\AdvancedFilter\AdvancedFilterParameters
 */
class AdvancedFilterType extends AbstractType
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $options['choices'];

        $transformer = function (FormEvent $event) use ($choices) {
            $values = $event->getData();
            if ($values instanceof AdvancedFilterParameters) {
                $values = $event->getForm()->getExtraData();
            }

            $include = [];
            $exclude = [];

            switch (true) {
                case is_string($values):
                    $data = AdvancedFilterParameters::queryFilterParameters($values);
                    break;

                case is_array($values):
                    if (count($choices) > 0) {
                        // Range type.
                        $include = key($values);
                    } else {
                        // Simple type.
                        foreach ($values as $value => $type) {
                            switch ($type) {
                                case 1:
                                    $include[] = $value;
                                    break;

                                case -1:
                                    $exclude[] = $value;
                                    break;

                                default:
                                    throw new \RuntimeException('Invalid value type, should be 1 or -1');
                            }
                        }
                    }
                    $data = new AdvancedFilterParameters($include, $exclude);
                    break;

                default:
                    $event->getForm()->addError(new FormError('Invalid value, expects object or string'));
                    return;
            }

            $event->setData($data);
        };

        $builder->addEventListener(FormEvents::SUBMIT, $transformer);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AdvancedFilterParameters::class,
            'empty_data' => new AdvancedFilterParameters([], []),
            'allow_extra_fields' => true,
            'choices' => [],
            'compound' => true,
        ]);
    }
}
