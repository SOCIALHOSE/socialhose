<?php

namespace AppBundle\Form\Type\Filter;

use CacheBundle\Entity\SourceList;
use CacheBundle\Entity\SourceToSourceList;
use CacheBundle\Form\Type\CurrentUserOwnedEntityType;
use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;

/**
 * Class SourceListFilterType
 * @package AppBundle\Form\Type\Filter
 */
class SourceListFilterType extends AbstractFilterType
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
        parent::buildForm($builder, $options);

        $builder
            ->add('include', CollectionType::class, [
                'entry_type' => CurrentUserOwnedEntityType::class,
                'entry_options' => [ 'class' => SourceList::class ],
                'allow_add' => true,
                'description' => 'Array of source list entities ids.',
            ])
            ->add('exclude', CollectionType::class, [
                'entry_type' => CurrentUserOwnedEntityType::class,
                'entry_options' => [ 'class' => SourceList::class ],
                'allow_add' => true,
                'description' => 'Array of source list entities ids.',
            ]);
    }

    /**
     * Custom validations.
     *
     * @param FormEvent $event A FormEvent instance.
     *
     * @return void
     */
    protected function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (count($data) === 0) {
            $event->getForm()->addError(
                new FormError('Provide at least one of \'include\' or \'exclude\' parameter.')
            );
        }
    }

    /**
     * Transform SourceList entities into array of source titles.
     *
     * @param array $sourceLists Array of SourceList entities.
     *
     * @return array
     */
    protected function getIds(array $sourceLists)
    {
        $listTitles = array_map(function (SourceList $list) {
            return array_map(function (SourceToSourceList $source) {
                return $source->getSource();
            }, $list->getSources()->toArray());
        }, $sourceLists);

        if (count($listTitles) === 0) {
            // To avoid passing empty arguments into 'array_merge' function.
            return [];
        }

        return call_user_func_array('array_merge', $listTitles);
    }

    /**
     * Transform input values into proper filters.
     *
     * @param mixed                  $value   Value to be transformed.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\FilterInterface|null
     */
    protected function transform($value, FilterFactoryInterface $factory)
    {
        $include = $this->getIds($value['include']);
        $exclude = $this->getIds($value['exclude']);

        $includeCount = count($include);
        $excludeCount = count($exclude);

        if (($includeCount === 0) && ($excludeCount === 0)) {
            // Don't make any transformations if client not provide any
            // parameters.
            return null;
        }

        $condition = $factory->andX();

        if ($includeCount > 0) {
            $condition->add($factory->in(FieldNameEnum::SOURCE_HASHCODE, $include));
        }

        if ($excludeCount > 0) {
            $condition->add($factory->not($factory->in(FieldNameEnum::SOURCE_HASHCODE, $exclude)));
        }

        return $condition;
    }
}
