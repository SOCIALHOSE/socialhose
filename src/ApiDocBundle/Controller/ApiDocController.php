<?php

namespace ApiDocBundle\Controller;

use Nelmio\ApiDocBundle\Controller\ApiDocController as BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\ApiDocExtractor;
use Nelmio\ApiDocBundle\Formatter\AbstractFormatter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiDocController
 * @package ApiDocBundle\Controller
 */
class ApiDocController extends BaseController
{

    /**
     * @param string $view View name.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($view = ApiDoc::DEFAULT_VIEW)
    {
        /** @var ApiDocExtractor $extractor */
        $extractor = $this->get('nelmio_api_doc.extractor.api_doc_extractor');
        /** @var AbstractFormatter $formatter */
        $formatter = $this->get('api_doc.formatter.html');

        $extractedDoc = $extractor->all($view);
        $htmlContent  = $formatter->format($extractedDoc);

        return new Response($htmlContent, 200, ['Content-Type' => 'text/html']);
    }
}
