<?php

namespace ApiBundle\Security\Inspector;

use UserBundle\Entity\User;

/**
 * Class InspectorInterface
 * Base class for all inspectors.
 *
 * @package ApiBundle\Security\Inspector
 */
abstract class AbstractInspector implements InspectorInterface
{

    /**
     * @var array
     */
    protected $reasons = [];

    /**
     * Checks that given user can make given action with specified entity.
     *
     * @param User   $user   A User entity instance.
     * @param object $entity A Entity instance or array of instances.
     * @param string $action Action name.
     *
     * @return string[] Array of restriction reasons.
     */
    public function inspect(User $user, $entity, $action)
    {
        $classes = (array) static::supportedClass();

        $checker = \nspl\f\partial('\app\op\isInstanceOf', $entity);
        if (! \nspl\a\any($classes, $checker)) {
            throw new \InvalidArgumentException('Can inspect only '. implode(', ', $classes));
        }

        // Clear reasons.
        $this->reasons = [];
        switch ($action) {
            case self::CREATE:
                $this->canCreate($user, $entity);
                break;

            case self::READ:
                $this->canRead($user, $entity);
                break;

            case self::UPDATE:
                $this->canUpdate($user, $entity);
                break;

            case self::DELETE:
                $this->canDelete($user, $entity);
                break;
        }

        return $this->reasons;
    }

    /**
     * @param string $reason Restriction reason.
     *
     * @return AbstractInspector
     */
    protected function addReason($reason)
    {
        $this->reasons[] = $reason;

        return $this;
    }

    /**
     * Add reason only if condition is true.
     *
     * @param string  $reason    Restriction reason.
     * @param boolean $condition Some boolean condition.
     *
     * @return AbstractInspector
     */
    protected function addReasonIf($reason, $condition)
    {
        if ($condition) {
            $this->reasons[] = $reason;
        }

        return $this;
    }

    /**
     * Check that user can create specified entity.
     *
     * @param User   $user   A user who try to create entity.
     * @param object $entity A Entity instance.
     *
     * @return void
     */
    abstract protected function canCreate(User $user, $entity);

    /**
     * Check that user can read specified entity.
     *
     * @param User   $user   A user who try to read entity.
     * @param object $entity A Entity instance.
     *
     * @return void
     */
    abstract protected function canRead(User $user, $entity);

    /**
     * Check that user can update specified entity.
     *
     * @param User   $user   A user who try to update entity.
     * @param object $entity A Entity instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    abstract protected function canUpdate(User $user, $entity);

    /**
     * Check that user can delete specified entity.
     *
     * @param User   $user   A user who try to delete entity.
     * @param object $entity A Entity instance.
     *
     * @return void
     */
    abstract protected function canDelete(User $user, $entity);
}
