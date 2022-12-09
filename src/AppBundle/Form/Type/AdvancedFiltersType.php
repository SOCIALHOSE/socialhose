<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Transformer\OnlyReverseTransformer;
use AppBundle\Form\Type\AdvancedFilter\AdvancedFilterParameters;
use AppBundle\Form\Type\AdvancedFilter\AdvancedFilterType;
use AppBundle\Form\Type\Traits\CleanFormTrait;
use Common\Enum\AFTypeEnum;
use IndexBundle\Index\IndexInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AdvancedFiltersType
 * @package AppBundle\Form\Type
 */
class AdvancedFiltersType extends AbstractType
{

    use CleanFormTrait;

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
        $index = $options['connection'];
        if (! $index instanceof IndexInterface) {
            throw new \RuntimeException(sprintf(
                '\'connection\' options should be instance of %s',
                IndexInterface::class
            ));
        }
        $config = array_filter($options['config']);

        //
        // Add all available filters to form.
        //
        foreach ($config as $name => $params) {
            $parameters = [];

            if ($params['type'] === AFTypeEnum::RANGE) {
                $parameters['choices'] = array_keys($params['ranges']);
            } elseif ($params['type'] === AFTypeEnum::QUERY) {
                $parameters['compound'] = false;
            }

            $parameters['description'] = $params['description'];
            $parameters['constraints'] = new NotBlank();

            $builder->add($name, AdvancedFilterType::class, $parameters);
        }

        /**
         * Transform filter names and values to concrete filters.
         *
         * @param array $filters Array of advanced filters values.
         *
         * @return \IndexBundle\Filter\FilterInterface[]
         */
        $transformationFn = function (array $filters) use ($index, $config) {
            $resolver = $index->getAFResolver();
            $resolvedFilters = [];

            /** @var AdvancedFilterParameters $params */
            foreach ($filters as $name => $params) {
                try {
                    $resolvedFilters[] = $resolver->generateFilter($config, $name, $params);
                } catch (\Exception $exception) {
                    throw new TransformationFailedException($exception->getMessage(), $exception->getCode(), $exception);
                }
            }

            return $resolvedFilters;
        };

        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, [ $this, 'clean' ])
            ->addModelTransformer(new OnlyReverseTransformer($transformationFn));
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
            ->setRequired('connection')
            ->setRequired('config')
            ->setAllowedTypes('connection', IndexInterface::class)
            ->setAllowedTypes('config', 'array')
            ->setDefaults([
                'allow_extra_fields' => true, // We handle this situation in pre
                                              // submit listener and make more
                                              // detailed error message.
                'connection' => null,
            ]);
    }
}
