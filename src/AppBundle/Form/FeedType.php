<?php

namespace AppBundle\Form;

use AppBundle\Form\SearchRequest\StoredQuerySearchRequestType;
use CacheBundle\Entity\Feed\ClipFeed;
use CacheBundle\Form\FeedInfoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Class FeedType
 * @package AppBundle\Form
 */
class FeedType extends AbstractType
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
            ->add('feed', FeedInfoType::class, [
                'constraints' => new Valid(),
            ])
            ->add('search', StoredQuerySearchRequestType::class)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm()->get('search');

                if (! isset($data['feed']['subType'])) {
                    return;
                }

                if ($data['feed']['subType'] === ClipFeed::getSubType()) {
                    $form
                        ->remove('query')
                        ->remove('filters');
                }
            });
    }
}
