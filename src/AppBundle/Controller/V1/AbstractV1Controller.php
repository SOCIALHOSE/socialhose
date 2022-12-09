<?php

namespace AppBundle\Controller\V1;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Response\View;
use AppBundle\Response\SearchResponseInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractV1Controller
 *
 * @package AppBundle\Controller\V1
 *
 * @deprecated
 * @see AbstractApiController
 */
abstract class AbstractV1Controller
{

    /**
     * Generate proper server response.
     *
     * @param mixed   $data   A data sent to client.
     * @param integer $code   Response http code.
     * @param array   $groups Serialization groups.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function generateResponse(
        $data = null,
        $code = null,
        array $groups = []
    ) {
        return new View($data, $groups, $code);
    }

    /**
     * @param mixed   $data  A data for pagination.
     * @param integer $page  A requested page, starts from 1.
     * @param integer $limit Required numbers of data per page.
     *
     * @return array
     */
    protected function paginate($data, $page, $limit)
    {
        if ($data instanceof SearchResponseInterface) {
            //
            // Response from index or cache already paginated so we just return
            // values.
            //
            return [
                'data' => $data->getDocuments(),
                'count' => count($data),
                'totalCount' => $data->getTotalCount(),
                'page' => $page,
                'limit' => $limit,
            ];
        } elseif ($data instanceof QueryBuilder) {
            $data
                ->setMaxResults($limit)
                ->setFirstResult(($page - 1) * $limit);

            $paginator = new Paginator($data);
            $data = iterator_to_array($paginator);

            return [
                'data' => $data,
                'count' => count($data),
                'totalCount' => $paginator->count(),
                'page' => $page,
                'limit' => $limit,
            ];
        }

        return [];// TODO add code for over paginated data.
    }

    /**
     * Returns a NotFoundHttpException.
     *
     * This will result in a 404 response code. Usage example:
     *
     *     throw $this->createNotFoundException('Page not found!');
     *
     * @param string          $message  A message.
     * @param \Exception|null $previous The previous exception.
     *
     * @return NotFoundHttpException
     */
    protected function createNotFoundException($message = 'Not Found', \Exception $previous = null)
    {
        return new NotFoundHttpException($message, $previous);
    }
}
