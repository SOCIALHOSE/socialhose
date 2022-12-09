<?php

namespace UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use UserBundle\Entity\Notification\Schedule\AbstractNotificationSchedule;
use UserBundle\Entity\Notification\Schedule\DailyNotificationSchedule;
use UserBundle\Entity\Notification\Schedule\MonthlyNotificationSchedule;
use UserBundle\Entity\Notification\Schedule\WeeklyNotificationSchedule;

/**
 * Class ScheduleType
 * @package UserBundle\Form\Type
 */
class ScheduleType extends AbstractType implements DataMapperInterface
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
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'daily',
                    'weekly',
                    'monthly',
                ],
                'description' => 'Notification schedule type.',
            ])

            // DailyNotificationSchedule
            ->add('time', ChoiceType::class, [
                'choices' => DailyNotificationSchedule::getAvailableTime(),
                'description' => 'Daily schedule time.',
            ])
            ->add('days', ChoiceType::class, [
                'choices' => DailyNotificationSchedule::getAvailableDays(),
                'description' => 'Daily schedule days.',
            ])

            // WeeklyNotificationSchedule
            ->add('period', ChoiceType::class, [
                'choices' => WeeklyNotificationSchedule::getAvailablePeriod(),
                'description' => 'Weekly schedule period.',
            ])

            // Common for WeeklyNotificationSchedule and MonthlyNotificationSchedule
            ->add('day', ChoiceType::class, [
                'choices' => [], // Filled on submitting, when we known schedule
                                 // type.
                'description' => 'Weekly and monthly schedule day. For weekly: day name. For monthly: numbers from 1 to 31 and word last.',
            ])
            ->add('hour', ChoiceType::class, [
                'choices' => range(0, 23),
                'description' => 'Weekly and monthly schedule hour.',
            ])
            ->add('minute', ChoiceType::class, [
                'choices' => range(0, 55, 5),
                'description' => 'Weekly and monthly schedule minute.',
            ])
            ->setDataMapper($this)
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();

                if (isset($data['type'])) {
                    //
                    // We should update form only if we got valid type.
                    //
                    switch ($data['type']) {
                        case 'daily':
                            $form
                                ->remove('period')
                                ->remove('day')
                                ->remove('hour')
                                ->remove('minute');
                            break;

                        case 'weekly':
                            $options = $form->get('day')->getConfig()->getOptions();

                            $form
                                ->remove('time')
                                ->remove('days')
                                ->remove('day');

                            $options['choices'] = array_combine(
                                WeeklyNotificationSchedule::getAvailableDay(),
                                WeeklyNotificationSchedule::getAvailableDay()
                            );
                            $form->add('day', ChoiceType::class, $options);
                            break;

                        case 'monthly':
                            $options = $form->get('day')->getConfig()->getOptions();

                            $form
                                ->remove('time')
                                ->remove('days')
                                ->remove('period')
                                ->remove('day');

                            $options['choices'] = array_combine(
                                MonthlyNotificationSchedule::getAvailableDay(),
                                MonthlyNotificationSchedule::getAvailableDay()
                            );
                            $form->add('day', ChoiceType::class, $options);
                            break;
                    }
                }
            });
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param AbstractNotificationSchedule|null          $data  Structured data.
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     *
     * @return void
     */
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        switch (true) {
            case ($data instanceof DailyNotificationSchedule):
                $forms['time']->setData($data->getTime());
                $forms['days']->setData($data->getDays());
                break;

            case ($data instanceof WeeklyNotificationSchedule):
                $forms['period']->setData($data->getPeriod());
                $forms['day']->setData($data->getDay());
                $forms['hour']->setData($data->getHour());
                $forms['minute']->setData($data->getMinute());
                break;

            case ($data instanceof MonthlyNotificationSchedule):
                $forms['day']->setData($data->getDay());
                $forms['hour']->setData($data->getHour());
                $forms['minute']->setData($data->getMinute());
                break;
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\RecursiveIteratorIterator $forms A list of
     *                                                          {@link FormInterface}
     *                                                          instances.
     * @param AbstractNotificationSchedule|null          $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        switch ($forms['type']->getData()) {
            case 'daily':
                $data = DailyNotificationSchedule::create()
                    ->setTime($forms['time']->getData())
                    ->setDays($forms['days']->getData());
                break;

            case 'weekly':
                $data = WeeklyNotificationSchedule::create()
                    ->setPeriod($forms['period']->getData())
                    ->setDay($forms['day']->getData())
                    ->setHour($forms['hour']->getData())
                    ->setMinute($forms['minute']->getData());
                break;

            case 'monthly':
                $data = MonthlyNotificationSchedule::create()
                    ->setDay($forms['day']->getData())
                    ->setHour($forms['hour']->getData())
                    ->setMinute($forms['minute']->getData());
                break;
        }
    }
}
