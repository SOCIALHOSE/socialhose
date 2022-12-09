<?php

namespace AppBundle\Form\Type;

use AppBundle\Enum\AbstractEnum;
use AppBundle\Form\Transformer\OnlyReverseTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnumType
 *
 * @package AppBundle\Form\Type
 */
class EnumType extends AbstractType
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
        $class = $options['enum_class'];

        $builder->addModelTransformer(new OnlyReverseTransformer(function ($value) use ($class) {
            if ($value === null) {
                return null;
            }

            return new $class($value);
        }));
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
        $resolver
            ->setRequired('enum_class')
            ->setAllowedTypes('enum_class', 'string')
            ->setDefault('choices', function (Options $options) {
                $class = $options['enum_class'];

                if (! class_exists($class)) {
                    throw new \InvalidArgumentException('Can\'t find class: '. $class);
                }

                $reflection = new \ReflectionClass($class);

                if ($reflection->isSubclassOf(AbstractEnum::class)) {
                    return $class::getAvailables();
                }

                return [];
            });
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
