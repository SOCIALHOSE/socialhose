<?php

namespace Common\Util\Processor;

use Common\Util\Processor\ExpressionLanguage\TestExpressionLanguage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * Class DataProcessor
 * Process data before sending to server.
 *
 * @package Common\Util\Processor
 */
class DataProcessor
{

    /**
     * @var ExpressionLanguage
     */
    private $language;

    /**
     * DataProcessor constructor.
     *
     * @param ContainerInterface $container A ContainerInterface instance.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->language = new TestExpressionLanguage($container);
        $this->registerFunctions();
    }

    /**
     * @param mixed $data Process data send to server and replace patterns.
     *
     * @return mixed
     */
    public function process($data)
    {
        if (is_array($data)) {
            // Recursively process arrays.
            return array_map(function ($data) {
                return $this->process($data);
            }, $data);
        } elseif (is_string($data)) {
            // Process string data.
            $replacer = function ($param) {
                // Sanitize params.
                $param = str_replace('\\"', '\'', trim(current($param), '#'));

                return $this->language->evaluate($param);
            };

            return preg_replace_callback("/#.+?#/", $replacer, $data);
        }

        // Not change other variable types.
        return $data;
    }

    /**
     * @param mixed $arguments Arguments specified by expression language.
     * @param mixed $time      Pass to DateTime constructor.
     *
     * @return \DateTime
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createDate($arguments, $time = 'now')
    {
        return new \DateTime($time);
    }

    /**
     * Register custom expression language functions.
     *
     * @return void
     */
    private function registerFunctions()
    {
        $dummy = function () {
            // do nothing.
        };

        $this->language->register('date', $dummy, [ $this, 'createDate' ]);
    }
}
