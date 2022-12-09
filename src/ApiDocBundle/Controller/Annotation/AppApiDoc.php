<?php

namespace ApiDocBundle\Controller\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Class AppApiDoc
 * Override ApiDoc in order to set some default fields.
 *
 * @package ApiDocBundle\Controller\Annotation
 *
 * @Annotation
 */
class AppApiDoc extends ApiDoc
{

    /**
     * AppApiDoc constructor.
     *
     * @param array $data Annotation data.
     */
    public function __construct(array $data)
    {
        //
        // Add default status codes.
        //
        if (! isset($data['statusCodes'])) {
            $data['statusCodes'] = [];
        }
        $data['statusCodes']['405'] = 'Invalid HTTP method.';

        parent::__construct($data);
    }
}
