<?php

namespace AppBundle\Utils\TransKey;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RecursiveTransKeyGenerator
 * @package AppBundle\Utils\TransKey
 */
class RecursiveTransKeyGenerator implements TransKeyGeneratorInterface
{

    /**
     * Generate proper transformation key for specified form.
     *
     * @param FormInterface $form A FormInterface instance.
     *
     * @return string
     */
    public function generate(FormInterface $form)
    {
        $parent = $form->getParent();
        if ($parent === null) {
            //
            // We should'nt include parent form name in key. Instead of it
            // we add action name which we generate from HTTP method and also
            // managed entity short name.
            //
            switch ($form->getConfig()->getMethod()) {
                case Request::METHOD_POST:
                    $action = 'create';
                    break;

                case Request::METHOD_GET:
                    $action = 'get';
                    break;

                case Request::METHOD_PUT:
                    $action = 'update';
                    break;

                case Request::METHOD_DELETE:
                    $action = 'delete';
                    break;

                default:
                    throw new \InvalidArgumentException(
                        "Unsupported method {$form->getConfig()->getMethod()}"
                    );
            }

            return $action . \app\c\getShortName($form->getConfig()->getDataClass());
        }

        //
        // We should use concrete field key generator.
        //
        $generator = $parent->getConfig()->getOption('key');
        $parentKey = $generator->generate($parent);
        $name = $form->getName();
        if (is_numeric($name)) {
            //
            // Entry type of CollectionType had order number instead of name and
            // we should'nt add it to translation key.
            //
            $name = '';
        }

        return ($parentKey === '') ? $name : $parentKey.ucfirst($name);
    }
}
