<?php

namespace CacheBundle\Form;

use CacheBundle\Entity\Category;
use CacheBundle\Entity\Document;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Entity\Feed\QueryFeed;
use CacheBundle\Form\Type\CurrentUserOwnedEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class FeedInfoType
 *
 * @package CacheBundle\Form
 */
class FeedInfoType extends AbstractType implements DataMapperInterface
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
        $availableSubtypes = [
            ClipFeed::getSubType(),
            QueryFeed::getSubType(),
        ];

        $builder
            ->add('subType', ChoiceType::class, [
                'choices' => $availableSubtypes,
                'invalid_message' => sprintf(
                    'Unknown feed sub type. Available: %s',
                    implode(', ', $availableSubtypes)
                ),
            ])
            ->add('excludedDocuments', EntityType::class, [
                'class' => Document::class,
                'multiple' => true,
            ])
            ->add('name', null, [
                'constraints' => new NotBlank(),
            ])
            ->add('category', CurrentUserOwnedEntityType::class, [
                'class' => Category::class,
            ])
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
        $resolver->setDefaults([
            'data_class' => AbstractFeed::class,
            'empty_data' => null,
            'validation_groups' => [ 'Feed_Create' ],
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param AbstractFeed|null                          $data  Structured data.
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     *
     * @return void
     */
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        if ($data instanceof AbstractFeed) {
            $forms['name']->setData($data->getName());
            $forms['category']->setData($data->getCategory());
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param AbstractFeed|null                          $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        //
        // Create new proper feed instance if it's not provided to us.
        //
        if (! $data instanceof AbstractFeed) {
            try {
                $data = AbstractFeed::createBySubType($forms['subType']->getData());
            } catch (\Exception $exception) {
                // This should be handled by constraints.
            }
        }

        $data
            ->setName($forms['name']->getData())
            ->setCategory($forms['category']->getData());

        $excludedDocuments = $forms['excludedDocuments']->getData();
        foreach ($excludedDocuments as $document) {
            $data->addExcludedDocument($document);
        }
    }
}
