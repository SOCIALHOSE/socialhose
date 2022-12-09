<?php

namespace UserBundle\Form\Type;

use PaymentBundle\Model\CreditCardAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CreditCardAddressType
 *
 * @package UserBundle\Form\Type
 */
class CreditCardAddressType extends AbstractType implements DataMapperInterface
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
            ->add('country', CountryType::class, [
                'constraints' => new NotBlank(),
            ])
            ->add('city', null, [ 'constraints' => new NotBlank() ])
            ->add('street', null, [ 'constraints' => new NotBlank() ])
            ->add('postalCode', null, [ 'constraints' => new NotBlank() ])
            ->setDataMapper($this);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param CreditCardAddress|null    $data  Structured data.
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
            $forms['country']->setData($data->getCountry());
            $forms['city']->setData($data->getCity());
            $forms['street']->setData($data->getStreet());
            $forms['postalCode']->setData($data->getPostalCode());
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Iterator $forms A list of {@link FormInterface}
     *                                         instances.
     * @param CreditCardAddress|null    $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        $data = new CreditCardAddress(
            $forms['country']->getData(),
            $forms['city']->getData(),
            $forms['street']->getData(),
            $forms['postalCode']->getData()
        );
    }
}
