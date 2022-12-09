<?php

namespace AppBundle\Doctrine\ORM;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityRepository;

/**
 * Class BaseEntityRepository
 *
 * @package AppBundle\Doctrine\ORM
 */
class BaseEntityRepository extends EntityRepository
{

    /**
     * Persist entity.
     *
     * @param object  $entity A persisted entity. Should be have same class which
     *                        is used to create repository.
     * @param boolean $flush  Immediately flush entity change or not.
     *
     * @return void
     */
    public function persist($entity, $flush = true)
    {
        if (! ClassUtils::getRealClass(get_class($entity)) === $this->_entityName) {
            throw new \InvalidArgumentException(sprintf(
                '%s: \'$entity\' should be instance of \'%s\' but \'%s\' given',
                __CLASS__,
                $this->_entityName,
                \app\op\getPrintableType($entity)
            ));
        }

        $this->_em->persist($entity);

        if ($flush) {
            $this->_em->flush($entity);
        }
    }
}
