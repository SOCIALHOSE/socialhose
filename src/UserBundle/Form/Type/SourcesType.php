<?php

namespace UserBundle\Form\Type;

use AppBundle\Form\Transformer\OnlyReverseTransformerTrait;
use CacheBundle\Entity\Feed\AbstractFeed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UserBundle\Entity\User;

/**
 * Class SourcesType
 * @package UserBundle\Form\Type
 */
class SourcesType extends AbstractType implements DataTransformerInterface
{

    use OnlyReverseTransformerTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * NotificationSourceType constructor.
     *
     * @param EntityManagerInterface $em      A EntityManagerInterface instance.
     * @param TokenStorageInterface  $storage A TokenStorageInterface instance.
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $storage
    ) {
        $this->em = $em;
        $this->storage = $storage;
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
        $builder->addModelTransformer($this);
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
            'entry_type' => NotificationSourceType::class,
            'allow_add' => true,
            'by_reference' => true,
            'constraints' => new Callback([ $this, 'validate' ]),
        ]);
    }

    /**
     * Validate sources.
     *
     * @param array                     $sources Array of transformed sources.
     * @param ExecutionContextInterface $context A ExecutionContextInterface
     *                                           instance.
     *
     * @return void
     */
    public function validate(array $sources, ExecutionContextInterface $context)
    {
        // todo uncomment and rewrite when analytic is added
//        if (($sources['feeds'] === null) || ($sources['charts'] === null)) {
        if ($sources['feeds'] === null) {
            $context
                ->buildViolation('Some of sources has invalid id.')
                ->addViolation();
        }
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string|null The name of the parent type if any, null otherwise.
     */
    public function getParent()
    {
        return CollectionType::class;
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
        // Split feeds and charts.
        // todo uncomment when analytic is added
        //
//            list($feedsIds, $chartsIds) = \nspl\a\partition(function (array $row) {
//                return $row['type'] === AbstractFeed::class;
//            }, $data);

        //
        // Fetch proper entities from database.
        //
        $feedsIds = \nspl\a\map(\nspl\op\itemGetter('id'), $data);
        $feeds = $this->getEntities(AbstractFeed::class, $feedsIds);

        //
        // We return hash here to simplify further processing.
        //
        return [
            'feeds' => (count($feeds) === count($feedsIds)) ? $feeds : null,
            'charts' => null,
        ];
    }

    /**
     * Check that all specified id is exists.
     *
     * @param string $class Entity fqcn.
     * @param array  $ids   Array of entities ids.
     *
     * @return array
     */
    private function getEntities($class, array $ids)
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository($class);
        $expr = $this->em->getExpressionBuilder();

        $condition = $expr->andX($expr->in('Source.id', ':ids'));
        $parameters = [ 'ids' => $ids ];

        //
        // Filter by user only if we have it.
        //
        $user = \app\op\invokeIf($this->storage->getToken(), 'getUser');
        if ($user instanceof User) {
            $condition->add($expr->eq('Source.user', ':user'));
            $parameters['user'] = $user->getId();
        }

        //
        // We should get only ids and names of sources 'cause it will be used
        // for generating response to client.
        //
        return $repository->createQueryBuilder('Source')
            ->select('partial Source.{id, name}')
            ->where($condition)
            ->setParameters($parameters)
            ->getQuery()
            ->getResult();
    }
}
