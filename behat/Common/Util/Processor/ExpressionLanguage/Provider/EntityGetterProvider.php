<?php

namespace Common\Util\Processor\ExpressionLanguage\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

/**
 * Class EntityGetterProvider
 * Register function like 'getUser' or 'getStoredQuery' for fetching single
 * entity from database.
 *
 * @package Common\Util\Processor\ExpressionLanguage\Provider
 *
 */
class EntityGetterProvider implements ExpressionFunctionProviderInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * EntityGetterProvider constructor.
     *
     * @param EntityManagerInterface $em A EntityManagerInterface instance.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return ExpressionFunction[] An array of Function instances.
     */
    public function getFunctions()
    {
        /**
         * Dummy compiler.
         * We use this expression function only in runtime and not compile its.
         */
        $compiler = function () {
        };

        $functions = [];
        /** @var ClassMetadataInfo[] $metadataList */
        $metadataList = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($metadataList as $metadata) {
            $name = $metadata->getName();
            $shortName = substr($name, strrpos($name, '\\') + 1);
            $fnName = 'get'. $shortName;

            if (strpos($name, 'Entity\\'. $shortName) === false) {
                // Process only entities inside 'Entity' directory.
                continue;
            }

            /**
             * @param mixed $arguments Arguments specified by expression language
             * @param array $criteria  Search criteria.
             *
             * @return null|object Found entity or null.
             */
            $evaluator = function ($arguments, array $criteria) use ($name) {
                return $this->em->getRepository($name)->findOneBy($criteria);
            };
            $functions[] = new ExpressionFunction($fnName, $compiler, $evaluator);
        }

        return $functions;
    }
}
