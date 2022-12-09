<?php


namespace AppBundle\Controller\Traits;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Trait FormFactoryAwareTrait
 *
 * @package AppBundle\Controller\Traits
 */
trait FormFactoryAwareTrait
{

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type.
     * @param mixed  $data    The initial data for the form.
     * @param array  $options Options for the form.
     *
     * @return FormInterface
     */
    protected function createForm($type, $data = null, array $options = array())
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * Creates and returns a form builder instance.
     *
     * @param mixed $data    The initial data for the form.
     * @param array $options Options for the form.
     *
     * @return FormBuilderInterface
     */
    protected function createFormBuilder($data = null, array $options = array())
    {
        return $this->formFactory->createBuilder(FormType::class, $data, $options);
    }
}
