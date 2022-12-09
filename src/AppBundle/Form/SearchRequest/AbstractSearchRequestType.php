<?php

namespace AppBundle\Form\SearchRequest;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use AppBundle\Form\AbstractConnectionAwareType;
use AppBundle\Form\Type\AdvancedFiltersType;
use AppBundle\Form\Type\Filter as QueryFilter;
use AppBundle\Form\Type\FiltersType;
use Common\Enum\AFSourceEnum;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AbstractSearchRequestType
 * @package AppBundle\Form\SearchRequest
 */
abstract class AbstractSearchRequestType extends AbstractConnectionAwareType implements
    DataMapperInterface
{

    /**
     * Available search request filters for articles.
     *
     * @var array
     */
    public static $filters = [
        'headline' => [
            'type' => QueryFilter\HeadlineFilterType::class,
            'description' => 'Addition title filtering',
        ],
        'publisher' => [
            'type' => QueryFilter\PublisherFilterType::class,
            'description' => 'Filter by publisher type.',
        ],
        'source' => [
            'type' => QueryFilter\SourceFilterType::class,
            'description' => 'Filter by source.',
        ],
        'sourceList' => [
            'type' => QueryFilter\SourceListFilterType::class,
            'description' => 'Filter by source lists.',
        ],
        'language' => [
            'type' => QueryFilter\LanguageFilterType::class,
            'description' => 'Filter by language, use ISO 639-1 two-letters codes.',
        ],
        'country' => [
            'type' => QueryFilter\CountryFilterType::class,
            'description' => 'Filter by countries, ISO 3166-1 Alpha-2 two-letters codes.',
        ],
        'state' => [
            'type' => QueryFilter\StateFilterType::class,
            'description' => 'Filter by US states, ANSI standard INCITS 38:2009 two-letters codes.',
        ],
        'date' => [
            'type' => QueryFilter\DateFilterType::class,
            'description' => 'Filter by date, may be two types',
        ],
        'hasImage' => [
            'type' => QueryFilter\HasImageFilterType::class,
            'description' => 'Boolean flag, if true get document only with image.',
        ],
    ];

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
        $builder
            // Raw search query typed by user.
            ->add('query', null, [
                'constraints' => [
                    new NotBlank(),
                    new Length([ 'min' => 3 ]),
                ],
                'required' => true,
                'description' => 'Search query.',
            ])

            // Collection of available filters.
            ->add('filters', FiltersType::class, [
                'filter_factory' => $this->index->getFilterFactory(),
                'description' => 'Search filters.',
                'empty_data' => [],
                'filters' => self::$filters,
                'required' => false,
            ])

            // Collection of available advanced filters.
            ->add('advancedFilters', AdvancedFiltersType::class, [
                'description' => 'Advanced filters.',
                'config' => AdvancedFiltersConfig::getConfig(AFSourceEnum::FEED),
                'empty_data' => [],
                'connection' => $this->index,
                'required' => false,
            ])
            ->setEmptyData($this->createSearchRequestBuilder())
            ->setDataMapper($this);
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
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => SearchRequestBuilderInterface::class,
            'key' => 'createFeed',
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param mixed                                      $data  Structured data.
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing because it's senseless.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param mixed|SearchRequestBuilderInterface        $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        if (isset($forms['query'])) {
            $data->setQuery($forms['query']->getData());
        }

        $filters = $forms['advancedFilters']->getData() ?: [];
        if (isset($forms['filters'])) {
            $filters = array_merge($filters, $forms['filters']->getData());
        }
        $data->setFilters($filters);
    }
}
