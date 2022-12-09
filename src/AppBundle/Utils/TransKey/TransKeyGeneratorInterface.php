<?php

namespace AppBundle\Utils\TransKey;

use Symfony\Component\Form\FormInterface;

/**
 * Interface TransKeyGeneratorInterface
 * @package AppBundle\Utils\TransKey
 */
interface TransKeyGeneratorInterface
{

    /**
     * Generate proper transformation key for specified form.
     *
     * @param FormInterface $form A FormInterface instance.
     *
     * @return string
     */
    public function generate(FormInterface $form);
}
