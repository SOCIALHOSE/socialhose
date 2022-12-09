<?php

namespace UserBundle\Form;

use AppBundle\Form\Type\EnumType;
use CacheBundle\Form\Type\CurrentUserOwnedEntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Enum\ThemeTypeEnum;
use UserBundle\Form\Extension\Core\DataMapper\NotificationDataMapper;
use UserBundle\Form\Type\NotificationDiffType;
use UserBundle\Form\Type\ScheduleType;
use UserBundle\Form\Type\SimpleTimeZoneType;
use UserBundle\Form\Type\SourcesType;

/**
 * Class NotificationType
 * @package UserBundle\Form
 */
class NotificationType extends AbstractType
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
            ->add('name')
            ->add('notificationType', EnumType::class, [
                'enum_class' => NotificationTypeEnum::class,
            ])
            ->add('recipients', CurrentUserOwnedEntityType::class, [
                'class' => AbstractRecipient::class,
                'multiple' => true,
                'expanded' => true,
                    'by_reference' => 'false',
                'user_property' => 'owner',
            ])
            ->add('themeType', EnumType::class, [
                'enum_class' => ThemeTypeEnum::class,
            ])
            ->add('theme', EntityType::class, [
                'class' => NotificationTheme::class,
            ])
            ->add('subject', null, [ 'required' => false ])
            ->add('automatedSubject', FormType\CheckboxType::class)
            ->add('published', FormType\CheckboxType::class)
            ->add('allowUnsubscribe', FormType\CheckboxType::class)
            ->add('unsubscribeNotification', FormType\CheckboxType::class)
            ->add('sources', SourcesType::class)
            ->add('sendWhenEmpty', FormType\CheckboxType::class)
            ->add('timezone', SimpleTimeZoneType::class)
            ->add('automatic', FormType\CollectionType::class, [
                'entry_type' => ScheduleType::class,
                'description' => 'Array of daily, weekly or monthly scheduling objects.',
                'allow_add' => true,
                'allow_delete' => true,
                'empty_data' => [],
            ])
            ->add('sendUntil', FormType\DateType::class, [ 'widget' => 'single_text' ])
            ->add('plainDiff', NotificationDiffType::class)
            ->add('enhancedDiff', NotificationDiffType::class)
            ->setDataMapper(new NotificationDataMapper())
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (isset($data['automatedSubject']) && ($data['automatedSubject'] === false)) {
                    $options = $form->get('subject')->getConfig()->getOptions();
                    $options['constraints'] = new NotBlank([ 'message' => 'Subject should not be blank' ]);

                    $form
                        ->remove('subject')
                        ->add('subject', null, $options);
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
        $resolver->setDefault('data_class', Notification::class);
    }
}
