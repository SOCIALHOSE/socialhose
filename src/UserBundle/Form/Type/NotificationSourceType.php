<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Transformer\OnlyReverseTransformerTrait;
use CacheBundle\Entity\Feed\AbstractFeed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class NotificationSourceType
 * @package UserBundle\Form\Type
 */
class NotificationSourceType extends AbstractType implements DataTransformerInterface
{

    use OnlyReverseTransformerTrait;

    /**
     * Map between available type and actual entity class.
     *
     * @var array
     */
    private static $types = [
        'feed' => AbstractFeed::class,
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
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => array_keys(self::$types),
                'constraints' => new NotBlank(),
            ])
            ->add('id', null, [
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan([ 'value' => 0 ]),
                ],
            ])
            ->addModelTransformer($this);
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::submit()} is called to transform
     * the requests tainted data into an acceptable format for your data
     * processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $data The value in the transformed representation.
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($data)
    {
        //
        // Unfortunately we can't use here 'getPartialReference' or
        // 'getReference' methods for creating partial entity or proxy.
        //
        // * getPartialReference method is trying to instantiate specified
        // entity but for feeds we use base abstract class.
        //
        // getReference perform query to database for AbstractFeed entity.
        // https://github.com/doctrine/doctrine2/blob/v2.5.6/lib/Doctrine/ORM/EntityManager.php#L493
        //
        // So we just replace 'type' by entity class.
        //
        if (! isset($data['type'], self::$types[$data['type']])) {
            return null;
        }

        $data['type'] = self::$types[$data['type']];

        return $data;
    }
}
