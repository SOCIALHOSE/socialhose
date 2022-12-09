<?php

namespace UserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use PaymentBundle\Enum\PaymentGatewayEnum;
use PaymentBundle\Model\PaymentData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Entity\User;
use UserBundle\Form\Type\CreditCardType;

/**
 * Class PaymentDataType
 * @package UserBundle\Form
 */
class PaymentDataType extends AbstractType implements DataMapperInterface
{

    /**
     * @var User[]
     */
    private $userCache = [];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * PaymentDataType constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
            ->add('paymentGateway', ChoiceType::class, [
                'choices' => PaymentGatewayEnum::getAvailables(),
                'empty_data' => PaymentGatewayEnum::PAYPAL,
            ])
            ->add('code', null, [
                'constraints' => [
                    new Constraint\NotBlank(),
                    new Constraint\Callback([ $this, 'validateCode' ]),
                ],
            ])
            ->add('card', CreditCardType::class)
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
            'data_class' => PaymentData::class,
            'empty_data' => new PaymentData(PaymentGatewayEnum::paypal()),
        ]);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param PaymentData|null          $data  Structured data.
     * @param FormInterface[]|\Iterator $forms A list of {@link FormInterface}
     *                                         instances.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function mapDataToForms($data, $forms)
    {
        // Do nothing.
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Iterator $forms A list of {@link FormInterface}
     *                                         instances.
     * @param PaymentData|null          $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);
        $user = $this->getUserByCode($forms['code']->getData());

        try {
            $data = new PaymentData(
                new PaymentGatewayEnum($forms['paymentGateway']->getData()),
                $user,
                $forms['card']->getData()
            );
        } catch (\InvalidArgumentException $e) {
            //
            // This may occurred 'cause form don't validate values before mapping
            // it data to source object.
            //
            throw new \RuntimeException('Can\'t create payment data', 0, $e);
        }
    }

    /**
     * @param mixed                     $code    Validated code.
     * @param ExecutionContextInterface $context A ExecutionContextInterface
     *                                           instance.
     *
     * @return void
     */
    public function validateCode($code, ExecutionContextInterface $context)
    {
        $user = $this->getUserByCode($code);

        if ($user === null) {
            $context->buildViolation('Invalid code. Can\'t find user with specified code')
                ->addViolation();
        }
    }

    /**
     * @param mixed $code User confirmation code.
     *
     * @return User
     */
    private function getUserByCode($code)
    {
        if (! array_key_exists($code, $this->userCache)) {
            $this->userCache[$code] = $this->em->getRepository(User::class)
                ->findOneBy(['confirmationToken' => $code]);
        }

        return $this->userCache[$code];
    }
}
