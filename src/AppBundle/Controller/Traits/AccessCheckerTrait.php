<?php


namespace AppBundle\Controller\Traits;

use ApiBundle\Security\AccessChecker\AccessCheckerInterface;

/**
 * Trait AccessCheckerTrait
 *
 * @package AppBundle\Controller\Traits
 */
trait AccessCheckerTrait
{

    /**
     * @var AccessCheckerInterface
     */
    protected $accessChecker;

    /**
     * @param string          $action Action name.
     * @param object|object[] $entity A Entity instance or array of entity instances.
     *
     * @return string[] Array of restriction reasons.
     */
    protected function checkAccess($action, $entity)
    {
        if ($entity instanceof \Traversable) {
            $entity = iterator_to_array($entity);
        } elseif (is_object($entity)) {
            $entity = [ $entity ];
        }

        if (! is_array($entity)) {
            throw new \InvalidArgumentException('Expects single object or array of objects.');
        }

        $grantChecker = \nspl\f\partial([ $this->accessChecker, 'isGranted' ], $action);

        return \nspl\a\flatten(\nspl\a\map($grantChecker, $entity));
    }
}
