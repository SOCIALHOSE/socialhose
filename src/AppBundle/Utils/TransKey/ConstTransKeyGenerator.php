<?php

namespace AppBundle\Utils\TransKey;

use Symfony\Component\Form\FormInterface;

/**
 * Class ConstTransKeyGenerator
 * @package AppBundle\Utils\TransKey
 */
class ConstTransKeyGenerator implements TransKeyGeneratorInterface
{

    /**
     * @var string
     */
    private $key;

    /**
     * ConstTransKeyGenerator constructor.
     *
     * @param string $key Constant form key.
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Generate proper transformation key for specified form.
     *
     * @param FormInterface $form A FormInterface instance.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function generate(FormInterface $form)
    {
        return $this->key;
    }
}
