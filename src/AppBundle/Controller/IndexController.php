<?php

namespace AppBundle\Controller;

use AppBundle\Controller\V1\AbstractV1Controller;
use CacheBundle\Entity\Feed\AbstractFeed;
use CacheBundle\Feed\Formatter\FeedFormatterInterface;
use CacheBundle\Feed\Formatter\FormatterOptions;
use CacheBundle\Repository\CommonFeedRepository;
use Common\Enum\FormatNameEnum;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UserBundle\Enum\ThemeOptionExtractEnum;

/**
 * Class IndexController
 * @package AppBundle\Controller
 *
 * @Route("/", service="app.controller.index")
 */
class IndexController extends AbstractV1Controller
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FeedFormatterInterface
     */
    private $feedFormatter;

    /**
     * IndexController constructor.
     *
     * @param EntityManagerInterface $em            A EntityManagerInterface
     *                                              instance.
     * @param FeedFormatterInterface $feedFormatter A FeedFormatterInterface
     *                                              instance.
     */
    public function __construct(EntityManagerInterface $em, FeedFormatterInterface $feedFormatter)
    {
        $this->em = $em;
        $this->feedFormatter = $feedFormatter;
    }

    /**
     * @Route("/feed/{id}.{format}")
     *
     * @param Request $request A HTTP Request instance.
     * @param integer $id      A Feed entity id.
     * @param string  $format  A format name.
     *
     * @return Response
     */
    public function exportFeedAction(Request $request, $id, $format)
    {
        /** @var CommonFeedRepository $repository */
        $repository = $this->em->getRepository(AbstractFeed::class);

        $feed = $repository->find($id);
        if ((! $feed instanceof AbstractFeed) || ! $feed->getExported()) {
            throw $this->createNotFoundException();
        }

        $format = strtolower(trim($format));
        if (! FormatNameEnum::isValid($format)) {
            throw new BadRequestHttpException('Unknown format '. $format);
        }

        $data = $this->feedFormatter->formatFeed($feed, new FormatterOptions(
            new FormatNameEnum($format),
            $this->getNumber($request),
            $this->getExtract($request),
            $this->getShowImage($request),
            $this->getAsPlain($request)
        ));

        return Response::create($data->getData(), 200, [
            'Content-Type' => $data->getMime(),
        ]);
    }

    /**
     * @Route(
     *     "/{part}",
     *     methods={ "GET" },
     *     requirements={ "part"=".*" },
     *     defaults={ "part"="" }
     * )
     * @Template("AppBundle::index.html.twig")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }

    /**
     * @param Request $request A HTTP Request instance.
     *
     * @return integer
     */
    private function getNumber(Request $request)
    {
        $number = $request->query->getInt('n', 30);

        if (($number < 1) || ($number > 200)) {
            throw new BadRequestHttpException("'n' should be integer between 1 and 200.");
        }

        return $number;
    }

    /**
     * @param Request $request A HTTP Request instance.
     *
     * @return ThemeOptionExtractEnum
     */
    private function getExtract(Request $request)
    {
        $extract = strtolower(trim($request->query->get('ext', 'n')));

        switch ($extract) {
            case 's':
                $extract = ThemeOptionExtractEnum::start();
                break;

            case 'sc':
                $extract = ThemeOptionExtractEnum::context();
                break;

            case 'n':
                $extract = ThemeOptionExtractEnum::no();
                break;

            default:
                throw new BadRequestHttpException("'ext' should be one of: s, sc, n.");
        }

        return $extract;
    }

    /**
     * @param Request $request A Request instance.
     *
     * @return boolean
     */
    private function getShowImage(Request $request)
    {
        $showImage = $request->query->get('img', '0');

        if (($showImage !== '0') && ($showImage !== '1')) {
            throw new BadRequestHttpException("'img' should be 0 or 1.");
        }

        return $showImage === '1';
    }

    /**
     * @param Request $request A Request instance.
     *
     * @return boolean
     */
    private function getAsPlain(Request $request)
    {
        $textFormat = strtolower(trim($request->query->get('text_format')));

        if (($textFormat !== '') && ($textFormat !== 'text')) {
            throw new BadRequestHttpException("'text_format' should not be defined or contains 'text' value.");
        }

        return $textFormat === 'text';
    }
}
