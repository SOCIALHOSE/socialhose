<?php

namespace CacheBundle\Form;

use AppBundle\Form\SearchRequest\AbstractSearchRequestType;
use AppBundle\Form\Type\FiltersType;
use CacheBundle\DTO\AnalyticDTO;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Form\Type\CurrentUserOwnedEntityType;
use Doctrine\Common\Collections\Collection;
use IndexBundle\Index\IndexInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class AnalyticType
 *
 * Transform http request into AnalyticDTO object.
 *
 * @package CacheBundle\Form
 */
class AnalyticType extends AbstractType implements DataMapperInterface
{

    /**
     * @var IndexInterface
     */
    private $index;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $rawFilters;

    /**
     * AnalyticType constructor.
     *
     * @param IndexInterface        $index        A IndexInterface instance.
     * @param TokenStorageInterface $tokenStorage A TokenStorageInterface instance.
     */
    public function __construct(
        IndexInterface $index,
        TokenStorageInterface $tokenStorage
    ) {
        $this->index = $index;
        $this->tokenStorage = $tokenStorage;
    }

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
            ->add('feeds', CurrentUserOwnedEntityType::class, [
                'class' => AbstractFeed::class,
                'multiple' => true,
                'description' => 'Array of current user feeds ids.',
                'constraints' => new NotBlank(),
            ])
            ->add('filters', FiltersType::class, [
                'filter_factory' => $this->index->getFilterFactory(),
                'description' => 'Search filters.',
                'empty_data' => [],
                'filters' => AbstractSearchRequestType::$filters,
                'required' => false,
            ])
            ->setDataMapper($this)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                $this->rawFilters = [];
                if (isset($data['filters'])) {
                    $this->rawFilters = $data['filters'];
                }
            });
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
            'data_class' => AnalyticDTO::class,
            'empty_data' => null,
        ]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string The prefix of the template block name.
     */
    public function getBlockPrefix()
    {
        return '';
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param AnalyticDTO|null                           $data  Structured data.
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing because it's not necessary method.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param AnalyticDTO|null                           $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $feeds = $forms['feeds']->getData();
        if ($feeds instanceof Collection) {
            $feeds = $feeds->toArray();
        }

        $data = new AnalyticDTO(
            $feeds,
            \app\op\invokeIf($this->tokenStorage->getToken(), 'getUser'),
            $forms['filters']->getData(),
            $this->rawFilters
        );
    }
}
