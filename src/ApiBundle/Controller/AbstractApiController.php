<?php

namespace ApiBundle\Controller;

use ApiBundle\ApiBundleServices;
use ApiBundle\Response\View;
use ApiBundle\Security\AccessChecker\AccessCheckerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class AbstractApiController
 * @package ApiBundle\Controller
 */
class AbstractApiController
{

    /**
     * Default page value in pagination if 'page' filter not provided.
     */
    const DEFAULT_PAGE = 1;

    /**
     * Default limit (max entities per page) value in pagination if 'limit'
     * filter not provided.
     */
    const DEFAULT_LIMIT = 100;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container A ContainerInterface instance.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get service from container.
     *
     * @param string $id The service identifier.
     *
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Gets a container configuration parameter by its name.
     *
     * @param string $name The parameter name.
     *
     * @return mixed
     */
    protected function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type.
     * @param mixed  $data    The initial data for the form.
     * @param array  $options Options for the form.
     *
     * @return FormInterface
     */
    protected function createForm($type, $data = null, array $options = [])
    {
        return $this->container->get('form.factory')
            ->create($type, $data, $options);
    }

    /**
     * Return default entity manager.
     *
     * @return ObjectManager
     */
    protected function getManager()
    {
        /** @var Registry $doctrine */
        $doctrine =  $this->container->get('doctrine');

        $manager = $doctrine->getManager();

        if (! $manager instanceof ObjectManager) {
            throw new \LogicException('Should be instance of '. ObjectManager::class);
        }

        return $manager;
    }

    /**
     * Get current User entity instance.
     *
     * @return \UserBundle\Entity\User
     */
    protected function getCurrentUser()
    {
        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface $storage */
        $storage = $this->container->get('security.token_storage');

        return $storage->getToken()->getUser();
    }

    /**
     * @param string          $action Action name.
     * @param object|object[] $entity A Entity instance or array of entity instances.
     *
     * @return string[] Array of restriction reasons.
     */
    protected function checkAccess($action, $entity)
    {
        /** @var AccessCheckerInterface $checker */
        $checker = $this->container->get(ApiBundleServices::ACCESS_CHECKER);

        if ($entity instanceof \Traversable) {
            $entity = iterator_to_array($entity);
        } elseif (is_object($entity)) {
            $entity = [ $entity ];
        }

        if (! is_array($entity)) {
            throw new \InvalidArgumentException('Expects single object or array of objects.');
        }

        $grantChecker = \nspl\f\partial([ $checker, 'isGranted' ], $action);

        return \nspl\a\flatten(\nspl\a\map($grantChecker, $entity));
    }

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
     * Paginate given data.
     *
     * @param Request $request      A Request instance.
     * @param mixed   $results      Any values which have proper pagination listener.
     * @param integer $defaultLimit Default limit.
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    protected function paginate(Request $request, $results, $defaultLimit = self::DEFAULT_LIMIT)
    {
        /** @var PaginatorInterface $paginator */
        $paginator = $this->get('knp_paginator');

        $page = $request->query->getInt('page', self::DEFAULT_PAGE);
        $limit = $request->query->getInt('limit', $defaultLimit);

        return $paginator->paginate($results, $page, $limit);
    }

    /**
     * Forwards the request to another controller.
     *
     * @param string $controller The controller name (a string like BlogBundle:Post:index).
     * @param array  $path       An array of path parameters.
     * @param array  $query      An array of query parameters.
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     */
    protected function forward($controller, array $path = [], array $query = [])
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $path['_forwarded'] = $request->attributes;
        $path['_controller'] = $controller;
        $subRequest = $request->duplicate($query, null, $path);

        return $this->container
            ->get('http_kernel')
            ->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
