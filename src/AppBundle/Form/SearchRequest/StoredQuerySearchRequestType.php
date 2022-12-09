<?php

namespace AppBundle\Form\SearchRequest;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StoredQuerySearchRequestType
 * @package AppBundle\Form\SearchRequest
 */
class StoredQuerySearchRequestType extends AbstractSearchRequestType
{

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('key', 'createFeed');
    }
}
