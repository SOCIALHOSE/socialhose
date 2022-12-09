<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use Common\Enum\PublisherTypeEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class PublisherFilterType
 * @package AppBundle\Form\Type\Filter
 */
class PublisherFilterType extends AbstractFilterType
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
     * @param array $options The options.
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('source', ChoiceType::class, [
                'choices' => PublisherTypeEnum::getAvailables(),
                'multiple' => true,
                'description' => 'Get document within specified publisher',
            ])
            ->add('domain', ChoiceType::class, [
                'choices' => ['Reddit' => 'reddit.com',
                    'Twitter' => 'twitter.com',
                    'Instagram' => 'instagram.com',
                    'Flickr' => 'flickr.com',
                    'Youtube' => 'youtube.com',
                    'vimeo' => 'vimeo.com'],
                'multiple' => true,
                'description' => 'Get document not within specified domain',
            ]);
    }


    /**
     * Transform input values into proper filters.
     *
     * @param mixed $value Value to be transformed.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\FilterInterface|null
     */
    protected function transform($value, FilterFactoryInterface $factory)
    {
        if (count($value['source']) > 0 && count($value['domain']) > 0) {
            $condition = $factory->orX([
                $factory->in(FieldNameEnum::SOURCE_PUBLISHER_TYPE,
                    $value['source']),

                $factory->orX($factory->in(FieldNameEnum::DOMAIN, $value['domain'])),
            ]);
            return $condition;
        } else {
            if (count($value['source']) > 0) {
                return $factory->in(
                    FieldNameEnum::SOURCE_PUBLISHER_TYPE,
                    $value['source']
                );
            } else {
                return $factory->in(
                    FieldNameEnum::DOMAIN,
                    $value['domain']
                );
            }

        }

        return null;
    }
}
