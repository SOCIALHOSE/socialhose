<?php

namespace UserBundle\Form\Type;

use PaymentBundle\Model\CreditCard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class CreditCardType
 *
 * @package UserBundle\Form\Type
 */
class CreditCardType extends AbstractType implements DataMapperInterface
{

    private static $availableMonth = [
        '01',
        '02',
        '03',
        '04',
        '05',
        '06',
        '07',
        '08',
        '09',
        '10',
        '11',
        '12',
    ];

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
        $currentYear = (int) date('Y');

        $builder
            ->add('creditCardNumber', null, [
                'constraints' => [
                    new Constraint\Luhn(),
                    new Constraint\CardScheme([ 'schemes' => [ 'VISA', 'MASTERCARD', 'AMEX' ] ]),
                ],
            ])
            ->add('CVV', null, [
                'constraints' => [
                    new Constraint\Length([
                        'min' => 3,
                        'max' => 4,
                        'minMessage' => 'Card Verification Code is too short. It should have 3 or 4 characters.',
                        'maxMessage' => 'Card Verification Code is too long. It should have 3 or 4 characters.',
                    ]),
                    new Constraint\Type([ 'type' => 'numeric' ]),
                ],
            ])
            ->add('expireMonth', ChoiceType::class, [
                'choices' => self::$availableMonth,
                'constraints' => new Constraint\NotBlank(),
            ])
            ->add('expireYear', ChoiceType::class, [
                'choices' => range($currentYear, $currentYear + 10),
                'constraints' => new Constraint\NotBlank(),
            ])
            ->add('address', CreditCardAddressType::class)
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
            'constraints' => new Constraint\Callback([ $this, 'validate' ]),
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param CreditCard|null           $data  Structured data.
     * @param FormInterface[]|\Iterator $forms A list of {@link FormInterface}
     *                                         instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        if ($data !== null) {
            $forms = iterator_to_array($forms);
            $forms['creditCardNumber']->setData($data->getNumber());
            $forms['CVV']->setData($data->getCvv());
            $forms['expireMonth']->setData($data->getExpiresAt()->format('m'));
            $forms['expireYear']->setData($data->getExpiresAt()->format('Y'));
            $forms['address']->setData($data->getAddress());
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Iterator $forms A list of {@link FormInterface}
     *                                         instances.
     * @param CreditCard|null           $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $expiresAt = \DateTime::createFromFormat(
            'Y-m-d',
            $forms['expireYear']->getData(). '-'. $forms['expireMonth']->getData() .'-01'
        )->setTime(0, 0);

        $data = new CreditCard(
            'First',
            'Second',
            $forms['creditCardNumber']->getData(),
            $forms['CVV']->getData(),
            $expiresAt,
            $forms['address']->getData()
        );
    }

    /**
     * @param CreditCard|mixed          $data    Validated payment data.
     * @param ExecutionContextInterface $context A ExecutionContextInterface
     *                                           instance.
     *
     * @return void
     */
    public function validate($data, ExecutionContextInterface $context)
    {
        if (($data instanceof CreditCard)
            && ($data->getExpiresAt() < date_create('first day of this month 00:00:00'))) {
            $context->buildViolation('Card has already expired')
                ->atPath('expireMonth')
                ->addViolation();
        }
    }
}
