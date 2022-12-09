<?php

namespace Common\Util\Processor\ExpressionLanguage;

use Common\Util\Processor\ExpressionLanguage\Provider\EntityGetterProvider;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class TestExpressionLanguage
 * @package Common\Util\Processor\ExpressionLanguage
 */
class TestExpressionLanguage extends ExpressionLanguage
{

    /**
     * TestExpressionLanguage constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     */
    public function __construct(ContainerInterface $container)
    {
        /** @var Registry $doctrine */
        $doctrine = $container->get('doctrine');

        $dummy = function () {
            // Dummy function for compiler argument of ExpressionFunction.
        };

        parent::__construct(null, [
            new EntityGetterProvider($doctrine->getManager()),
        ]);

        // Add 'now' function which return current date.
        $this->addFunction(new ExpressionFunction('now', $dummy, function () {
            return date_create();
        }));
    }
}
