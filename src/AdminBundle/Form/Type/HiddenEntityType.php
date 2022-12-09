<?php

namespace AdminBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class HiddenEntityType
 * @package AdminBundle\Form\Type
 */
class HiddenEntityType extends AbstractType
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * HiddenEntityType constructor.
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
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idParameter = $options['id_parameter'];
        $class = $options['class'];

        $transformer = function ($entity) use ($class, $idParameter) {
            // Transform.
            if (! $entity instanceof $class) {
                throw new TransformationFailedException('Entity should be instance of '. $class);
            }
            return PropertyAccess::createPropertyAccessor()
                ->getValue($entity, $idParameter);
        };

        $reverseTransformer = function ($id) use ($class) {
            if (trim($id) === '') {
                return null;
            }

            $entity = $this->em->find($class, $id);

            if (null === $entity) {
                throw new TransformationFailedException(sprintf(
                    'An entity with ID "%s" does not exist!',
                    $id
                ));
            }

            return $entity;
        };

        $builder->addModelTransformer(new CallbackTransformer($transformer, $reverseTransformer));
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
        $resolver
            ->setRequired('id_parameter')
            ->setRequired('class')
            ->setDefault('id_parameter', 'id');
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
