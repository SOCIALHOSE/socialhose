<?php

namespace ApiBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Repository\NotificationRepository;

/**
 * Class NotificationSubscribeBatchType
 *
 * @package ApiBundle\Form
 */
class NotificationSubscribeBatchType extends EntitiesBatchType
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

        //
        // Use custom query builder.
        //
        $idsOptions = $builder->get('ids')->getOptions();
        $idsOptions['query_builder'] = function (NotificationRepository $repository) {
            return $repository->getQueryBuilderForSubscription();
        };

        $builder->remove('ids')->add('ids', EntityType::class, $idsOptions);
        $builder->add('subscribed', CheckboxType::class);
    }
}
