<?php

namespace Common\Util;

use Common\Util\Converter\DateConverter;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Class DatabaseHelper
 * Check database status.
 *
 * @package Common\Util
 */
class DatabaseHelper
{

    /**
     * @var Registry
     */
    private $registry;

    /**
     * DatabaseHelper constructor.
     *
     * @param Registry $registry A Registry instance.
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Check that given entity with specified parameters are exists in our
     * database.
     *
     * @param string $name   Entity short name like AppBundle:Entity or FQCN.
     * @param array  $params Entity parameters.
     *
     * @return array
     */
    public function getEntities($name, array $params)
    {
        // Process parameters.
        $params = $this->parseParams($params);

        return $this->registry->getRepository($name)->findBy($params);
    }

    /**
     * Remove some entities from BD by parameters
     *
     * @param string $name   Entity short name like AppBundle:Entity or FQCN.
     * @param array  $params Entity parameters.
     *
     * @return void
     */
    public function deleteEntity($name, array $params)
    {
        // Process parameters.
        $params = $this->parseParams($params);

        $entities = $this->registry->getRepository($name)->findBy($params);
        $em = $this->registry->getEntityManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
        }
        $em->flush();
    }

    /**
     * Check that given entity with specified parameters are exists in our
     * database.
     *
     * @param string $name   Entity short name like AppBundle:Entity or FQCN.
     * @param array  $params Entity parameters.
     *
     * @return object|null
     */
    public function getEntity($name, array $params)
    {
        // Process parameters.
        $params = $this->parseParams($params);

        return $this->registry->getRepository($name)->findOneBy($params);
    }

    /**
     * Parse raw parameters.
     *
     * @param array $params Entity parameters.
     *
     * @return array
     */
    private function parseParams(array $params)
    {
        return array_map(function ($parameter) {
            $origin = trim($parameter);
            $buf = strtolower($origin);

            switch (true) {
                // Parameter is valid numerical value convert it to float or
                // integer.
                case is_numeric($buf):
                    if (strpos($buf, '.') !== false) {
                        return (float) $buf;
                    }

                    return (int) $buf;

                case DateConverter::can($buf):
                    return DateConverter::convert($buf);

                case in_array($origin, \DateTimeZone::listIdentifiers(), true):
                    return new \DateTimeZone($origin);

                // Parameter contains boolean value.
                case ($buf === 'true') || ($buf === 'false'):
                    return $buf === 'true';
            }

            return $parameter;
        }, $params);
    }
}
