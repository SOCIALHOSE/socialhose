<?php

namespace ApiDocBundle\Extractor\Handler;

use ApiBundle\Controller\Annotation\Roles;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Route;

/**
 * Class RolesHandler
 * @package ApiDocBundle\Extractor\Handler
 */
class RolesHandler implements HandlerInterface
{

    /**
     * Parse route parameters in order to populate ApiDoc.
     *
     * @param ApiDoc            $annotation  A ApiDoc annotation instance.
     * @param array             $annotations All founded annotations.
     * @param Route             $route       A Route instance.
     * @param \ReflectionMethod $method      A ReflectionMethod instance.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handle(
        ApiDoc $annotation,
        array $annotations,
        Route $route,
        \ReflectionMethod $method
    ) {
        foreach ($annotations as $annot) {
            if ($annot instanceof Roles) {
                $annotation->setAuthentication(true);

                $annotation->setAuthenticationRoles($annot->roles);
                $annotation->addStatusCode(401, 'JWT token not provided, expired or invalid.');
                $annotation->addHeader('Authorization', [
                    'description' => 'Bearer authorizations through JWT token.',
                    'required' => true,
                ]);
            }
        }
    }
}
