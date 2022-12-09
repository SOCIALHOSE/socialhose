<?php

namespace AdminBundle\Form;

use AdminBundle\Entity\SiteSettings;
use AppBundle\Configuration\ConfigurationDefinitionMap;
use AppBundle\Configuration\ConfigurationParameterInterface;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Class ConfigParametersType
 * @package AdminBundle\Form
 */
class ConfigParametersType extends AbstractType implements DataMapperInterface
{

    /**
     * @var ConfigurationDefinitionMap
     */
    private $definitionMap;

    /**
     * ConfigParametersType constructor.
     *
     * @param ConfigurationDefinitionMap $definitionMap A ConfigurationDefinitionMap
     *                                                  instance.
     */
    public function __construct(ConfigurationDefinitionMap $definitionMap)
    {
        $this->definitionMap = $definitionMap;
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
        $parameters = $builder->getData();

        /** @var SiteSettings $parameter */
        foreach ($parameters as $parameter) {
            $definition = $this->definitionMap->getDefinition($parameter->getName());
            $name = str_replace('.', ':', $parameter->getName());
            $type = $definition['type'];
            $type = $type === 'integer' ? 'numeric' : $type;

            $options = [
                'label' => $parameter->getTitle(),
                'constraints' => new Type([ 'type' => $type ]),
            ];

            if ($definition['formType'] === CKEditorType::class) {
                $options['config_name'] = 'default';
            }

            if ($definition['formType'] === ChoiceType::class) {
                $options['choices'] = $definition['choices'];
            }

            $builder->add($name, $definition['formType'], $options);
        }

        $builder->setDataMapper($this);
    }

    /**
     * Maps properties of some data to a list of forms.
     *
     * @param ConfigurationParameterInterface[]|null $data  Structured data.
     * @param FormInterface[]|\Iterator              $forms A list of {@link FormInterface}
     *                                                      instances.
     *
     * @return void
     */
    public function mapDataToForms($data, $forms)
    {
        $forms = iterator_to_array($forms);

        foreach ($data as $parameter) {
            $forms[str_replace('.', ':', $parameter->getName())]->setData($this->definitionMap->denormalize(
                $parameter->getName(),
                $parameter->getValue()
            ));
        }
    }

    /**
     * Maps the data of a list of forms into the properties of some data.
     *
     * @param FormInterface[]|\Iterator              $forms A list of {@link FormInterface}
     *                                                      instances.
     * @param ConfigurationParameterInterface[]|null $data  Structured data.
     *
     * @return void
     */
    public function mapFormsToData($forms, &$data)
    {
        $forms = iterator_to_array($forms);

        /** @var FormInterface $form */
        foreach ($forms as $form) {
            $name = str_replace(':', '.', $form->getName());
            $value = $form->getData();
            settype($value, $this->definitionMap->getDefinition($name)['type']);

            $data[$name]->setValue($value);
        }
    }
}
