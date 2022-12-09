<?php

namespace ApiBundle\Security\AccessChecker;

use ApiBundle\Security\Inspector\Factory\InspectorFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AccessChecker
 * @package ApiBundle\Security\AccessChecker
 */
class AccessChecker implements AccessCheckerInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $storage;

    /**
     * @var InspectorFactoryInterface
     */
    private $factory;

    /**
     * AccessChecker constructor.
     *
     * @param TokenStorageInterface     $storage A TokenStorageInterface instance.
     * @param InspectorFactoryInterface $factory A InspectorFactoryInterface
     *                                           instance.
     */
    public function __construct(
        TokenStorageInterface $storage,
        InspectorFactoryInterface $factory
    ) {
        $this->storage = $storage;
        $this->factory = $factory;
    }

    /**
     * Checks that current user can make given action with specified entity.
     *
     * @param string $action Action name.
     * @param object $entity A Entity instance.
     *
     * @return string[] Array of restriction reasons.
     */
    public function isGranted($action, $entity)
    {
        $inspector = $this->factory->create($entity);
        $user = $this->storage->getToken()->getUser();

        return $inspector->inspect($user, $entity, $action);
    }
}
