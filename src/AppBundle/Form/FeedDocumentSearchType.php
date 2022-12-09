<?php

namespace AppBundle\Form;

use AppBundle\AdvancedFilters\AdvancedFiltersConfig;
use AppBundle\Form\Type\AdvancedFiltersType;
use Common\Enum\AFSourceEnum;
use IndexBundle\SearchRequest\SearchRequestBuilderInterface;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FeedDocumentSearchType
 * @package AppBundle\Form
 */
class FeedDocumentSearchType extends AbstractConnectionAwareType implements
    DataMapperInterface
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
        $builder
            // Page field, default value - 1
            ->add('page', null, [
                'description' => 'Requested page number. Default value is 1.',
                'empty_data' => 1,
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
            ->setPage($forms['page']->getData())
            ->setFilters($forms['advancedFilters']->getData());
    }
}
