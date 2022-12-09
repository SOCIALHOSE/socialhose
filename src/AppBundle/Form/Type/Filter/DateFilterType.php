<?php

namespace AppBundle\Form\Type\Filter;

use Common\Enum\FieldNameEnum;
use IndexBundle\Filter\Factory\FilterFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class DateFilterType
 * @package AppBundle\Form\Type\Filter
 */
class DateFilterType extends AbstractFilterType
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
            ->add('type', ChoiceType::class, [
                'choices' => [ 'last', 'between' ],
                'description' => 'Date filter type.',
            ])
            ->add('days', IntegerType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'This value should be integer greater than 0.',
                    ]),
                    new NotBlank(),
                ],
                'invalid_message' => 'This value should be integer greater than 0.',
                'description' => 'How many days ago document found. Used only for \'last\' type.',
            ])
            ->add('start', DateType::class, [
                'widget' => 'single_text',
                'input' => 'string',
                'description' => 'Start of searched period, format: \'YYYY-MM-DD\'. Used only for \'between\' type.',
                'constraints' => new NotBlank(),
            ])
            ->add('end', DateType::class, [
                'widget' => 'single_text',
                'input' => 'string',
                'description' => 'End of searched period, format: \'YYYY-MM-DD\'. Used only for \'between\' type.',
                'constraints' => new NotBlank(),
            ]);
    }

    /**
     * Change form based on selected type.
     *
     * @param FormEvent $event A FormEvent instance.
     *
     * @return void
     */
    protected function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        switch (true) {
            //
            // Stop processing because type not set.
            // Remove all fields to avoid unnecessary validation errors.
            //
            case ! isset($data['type']) || (($data['type'] !== 'last')
                    && ($data['type'] !== 'between')):
                $form
                    ->remove('days')
                    ->remove('start')
                    ->remove('end');
                break;

            //
            // For last 'type' we should remove 'start' and 'end' field because
            // its unnecessary.
            //
            case $data['type'] === 'last':
                $form
                    ->remove('start')
                    ->remove('end');
                break;

            //
            // Make additional validation for 'between' type.
            //
            // Try to convert 'start' and 'end' values into datetime instances
            // and check that 'start' not greater than 'end' and if it so add
            // proper error message to 'start' field.
            //
            default:
                if (isset($data['start'], $data['end'])) {
                    $start = date_create_from_format('Y-m-d', $data['start'])->setTime(0, 0);
                    $end = date_create_from_format('Y-m-d', $data['end'])->setTime(0, 0);

                    if ((($start !== false) && ($end !== false)) && ($start > $end)) {
                        $form->addError(new FormError(
                            '\'start\' value should be less than \'end\'.'
                        ));
                    }
                }

                $form->remove('days');
        }
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
        //
        // Don't make any transformations if 'type' is not set.
        //
        if (! is_array($value) || ! isset($value['type'])) {
            return null;
        }

        //
        // Because transformation occurred before validation we should check all
        // values and if some of them is invalid we just return null.
        //
        // All validation error messages will be added in form validation
        // listener so we should'nt worry about it.
        //

        switch ($value['type']) {
            case 'last':
                return $this->transformLast($value, $factory);

            case 'between':
                return $this->transformBetween($value, $factory);
        }

        throw new \LogicException(sprintf(
            'Unhandled date filter type \'%s\'',
            $value['type']
        ));
    }

    /**
     * @param array                  $value   Date filter value.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\Filters\AndFilter|null
     */
    private function transformLast(array $value, FilterFactoryInterface $factory)
    {
        if (! isset($value['days']) || ($value['days'] < 0)) {
            return null;
        }

        return $factory->andX([
            $factory->gte(FieldNameEnum::PUBLISHED, date_create(sprintf(
                '- %d days 00:00:00',
                $value['days']
            ))),
            $factory->lte(FieldNameEnum::PUBLISHED, date_create('23:59:59')),
        ]);
    }

    /**
     * @param array                  $value   Date filter value.
     * @param FilterFactoryInterface $factory A FilterFactoryInterface instance.
     *
     * @return \IndexBundle\Filter\Filters\AndFilter|null
     */
    private function transformBetween(array $value, FilterFactoryInterface $factory)
    {
        if (! isset($value['start'], $value['end'])) {
            return null;
        }

        // Try to create datetime instances from 'start' and 'end' fields and
        // if we got error we should stop further transformations and return
        // null.
        $start = date_create_from_format('Y-m-d', $value['start'])->setTime(0, 0);
        $end = date_create_from_format('Y-m-d', $value['end'])->setTime(23, 59, 59);

        if (($start === false) || ($end === false)) {
            return null;
        }

        return $factory->andX([
            $factory->gte(FieldNameEnum::PUBLISHED, $start),
            $factory->lte(FieldNameEnum::PUBLISHED, $end),
        ]);
    }
}
