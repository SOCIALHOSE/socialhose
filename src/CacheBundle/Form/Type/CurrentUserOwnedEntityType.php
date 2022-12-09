<?php

namespace CacheBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\User;

/**
 * Class CurrentUserCategoryType
 *
 * Extend default EntityType with proper query builder which is returns categories
 * which is owned by current user.
 *
 * @package CacheBundle\Form\Type
 */
class CurrentUserOwnedEntityType extends EntityType
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * CurrentUserOwnedEntityType constructor.
     *
     * @param ManagerRegistry       $managerRegistry A ManagerRegistry instance.
     * @param TokenStorageInterface $storage         A TokenStorageInterface
     *                                               instance.
     */
    public function __construct(
        ManagerRegistry $managerRegistry,
        TokenStorageInterface $storage
    ) {
        parent::__construct($managerRegistry);
        $this->storage = $storage;
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
            ->setRequired('user_property')
            ->setAllowedTypes('user_property', 'string')
            ->setDefaults([
                'user_property' => 'user',
                'query_builder' => function (Options $options) {
                    $userProperty = $options['user_property'];

                    return function (EntityRepository $repository) use ($userProperty) {
                        // Get current user.
                        $user = \app\op\invokeIf($this->storage->getToken(), 'getUser');

                        if ($user instanceof User) {
                            $user = $user->getId();
                        }

                        $qb = $repository->createQueryBuilder('Entity');

                        if ($user instanceof User) {
                            $qb
                                ->where("Entity.{$userProperty} = :user")
                                ->setParameter('user', $user);
                        }

                        return $qb;
                    };
                },
            ]);
        parent::configureOptions($resolver);
    }
}
