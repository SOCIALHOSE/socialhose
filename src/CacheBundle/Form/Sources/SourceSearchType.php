<?php

namespace CacheBundle\Form\Sources;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use AppBundle\Form\AbstractConnectionAwareType;
use AppBundle\Form\Transformer\OnlyReverseTransformer;
use AppBundle\Form\Type\AdvancedFiltersType;
use AppBundle\Form\Type\Filter as QueryFilter;
use AppBundle\Form\Type\FiltersType;
use CacheBundle\Form\Sources\Type\SortType;
use Common\Enum\AFSourceEnum;
use Common\Enum\FieldNameEnum;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class SourceSearchType
 * @package CacheBundle\Form\Sources
 */
class SourceSearchType extends AbstractConnectionAwareType implements DataMapperInterface
{

    public static $fields = [
        'name' => FieldNameEnum::SOURCE_TITLE,
        'mediaType' => FieldNameEnum::SOURCE_PUBLISHER_TYPE,
        'country' => FieldNameEnum::COUNTRY,
    ];

    private static $filters = [
        'publisher' => [
            'type' => QueryFilter\PublisherFilterType::class,
            'description' => 'Filter by publisher type.',
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

        /**
         * Custom data mapping
         *
         * @param FormEvent $event A FormEvent instance.
         *
         * @return void
         */
        $postSubmit = function (FormEvent $event) {
            /** @var SearchRequestBuilderInterface $builder */
            $builder = $event->getData();
            $builder->setSorts($event->getForm()->get('sort')->getData());
            $event->setData($builder);
        };

        $builder
            ->add('query', null, [
                'description' => 'Search query, maybe empty. Search by source title and url.',
                'empty_data' => '',
                'constraints' => new Length([
                    'max' => 40,
                    'maxMessage' => 'Search query is too long. Should be 40 characters long or less.',
                ]),
            ])
            ->add('page', null, [
                'description' => 'Requested page number, should start from 1. Default value 1.',
                'empty_data' => 1,
            ])
            ->add('limit', null, [
                'description' => 'Max sources per page. Default 20.',
                'empty_data' => 20,
            ])
            ->add('filters', FiltersType::class, [
                'filter_factory' => $this->index->getFilterFactory(),
                'description' => 'Search filters.',
                'empty_data' => [],
                'filters' => self::$filters,
            ])
            ->add('sort', SortType::class, [
                'fields' => self::$fields,
                'default_field' => 'name',
                'default_direction' => 'asc',
                'mapped' => false,
            ])
            ->add('advancedFilters', AdvancedFiltersType::class, [
                'description' => 'Advanced filters.',
                'config' => AdvancedFiltersConfig::getConfig(AFSourceEnum::SOURCE),
                'empty_data' => [],
                'connection' => $this->index,
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $postSubmit)
            ->setEmptyData($this->index->createRequestBuilder())
            ->setDataMapper($this);

        $builder
            ->get('sort')
            ->addModelTransformer(new OnlyReverseTransformer(function (array $data) {
                if (count($data) === 0) {
                    // Default sort order.
                    $data = [ FieldNameEnum::SOURCE_TITLE => 'asc' ];
                }

                return $data;
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
        parent::configureOptions($resolver);
        $resolver->setDefault('key', 'searchSource');
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

        $data
            ->setQuery($forms['query']->getData())
            ->setPage($forms['page']->getData())
            ->setLimit($forms['limit']->getData())
            ->setFilters(array_merge(
                $forms['filters']->getData(),
                $forms['advancedFilters']->getData()
            ));
    }
}
