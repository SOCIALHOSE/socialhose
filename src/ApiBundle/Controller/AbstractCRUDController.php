<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\ManageableEntityInterface;
use ApiBundle\Form\EntitiesBatchType;
use ApiBundle\Security\Inspector\InspectorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractApiController
 * @package AppBundle\Controller
 */
class AbstractCRUDController extends AbstractApiController
{

    /**
     * Entity fqcn.
     *
     * @var string
     */
    protected $entity;

    /**
     * @param ContainerInterface $container A ContainerInterface instance.
     * @param string             $entity    Managed entity fqcn.
     */
    public function __construct(
        ContainerInterface $container,
        $entity
    ) {
        parent::__construct($container);
        $this->entity = $entity;
    }

    /**
     * Create new entity.
     *
     * @param Request                   $request A Request instance.
     * @param ManageableEntityInterface $entity  A ManageableEntityInterface
     *                                           instance.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    protected function createEntity(Request $request, ManageableEntityInterface $entity)
    {
        $form = $this->createForm($entity->getCreateFormClass(), $entity);

        // Submit data into form.
        $form->submit($request->request->all());
        if ($form->isValid()) {
            // Check that current user can create this entity.
            // If user don't have rights to create this entity we should send all
            // founded restrictions to client.
            $reasons = $this->checkAccess(InspectorInterface::CREATE, $entity);
            if (count($reasons) > 0) {
                // User don't have rights to create this entity so send all
                // founded restriction reasons to client.
                return $this->generateResponse($reasons, 403);
            }

            $em = $this->getManager();
            $em->persist($entity);
            $em->flush();

            return $entity;
        }

        // Client send invalid data.
        return $this->generateResponse($form, 400);
    }

    /**
     * Get information about single entity.
     *
     * @param integer|ManageableEntityInterface|null $id A entity id.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    protected function getEntity($id)
    {
        $foundedEntity = $id;
        if (is_numeric($id)) {
            $repository = $this->getManager()->getRepository($this->entity);

            $foundedEntity = $repository->find($id);
        }

        if ($foundedEntity === null) {
            $name = \app\c\getShortName($this->entity);
            // Remove 'Abstract' prefix if it exists.
            if (strpos($name, 'Abstract') !== false) {
                $name = substr($name, 8);
            }

            return $this->generateResponse("Can't find {$name} with id {$id}.", 404);
        }

        // Check that current user can read this entity.
        // If user don't have rights to read this entity we should send all
        // founded restrictions to client.
        $reasons = $this->checkAccess(InspectorInterface::READ, $foundedEntity);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        return $foundedEntity;
    }

    /**
     * Update entity.
     *
     * @param Request                                $request A Request instance.
     * @param integer|ManageableEntityInterface|null $entity  A entity id.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    protected function putEntity(Request $request, $entity)
    {
        $em = $this->getManager();

        $foundedEntity = $entity;
        if (is_numeric($entity)) {
            $repository = $em->getRepository($this->entity);
            /** @var \ApiBundle\Entity\ManageableEntityInterface $entity */
            $foundedEntity = $repository->find($entity);
        }

        if ($foundedEntity === null) {
            $name = \app\c\getShortName($this->entity);
            // Remove 'Abstract' prefix if it exists.
            if (strpos($name, 'Abstract') !== false) {
                $name = substr($name, 8);
            }

            return $this->generateResponse("Can't find {$name} with id {$entity}.", 404);
        }

        $form = $this->createForm($foundedEntity->getUpdateFormClass(), $foundedEntity, [
            'method' => 'PUT',
        ]);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            // Check that current user can update this entity.
            // If user don't have rights to update this entity we should send all
            // founded restrictions to client.
            $reasons = $this->checkAccess(InspectorInterface::UPDATE, $foundedEntity);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            $em->persist($foundedEntity);
            $em->flush();

            return $foundedEntity;
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Delete entity.
     *
     * @param integer|ManageableEntityInterface|null $entity A entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function deleteEntity($entity)
    {
        $em = $this->getManager();

        $foundedEntity = $entity;
        if (is_numeric($entity)) {
            $repository = $em->getRepository($this->entity);
            /** @var \ApiBundle\Entity\ManageableEntityInterface $entity */
            $foundedEntity = $repository->find($entity);
        }

        if ($foundedEntity === null) {
            $name = \app\c\getShortName($this->entity);
            // Remove 'Abstract' prefix if it exists.
            if (strpos($name, 'Abstract') !== false) {
                $name = substr($name, 8);
            }

            return $this->generateResponse("Can't find {$name} with id {$entity}.", 404);
        }
        // Check that current user can delete this entity.
        // If user don't have rights to delete this entity we should send all
        // founded restrictions to client.
        $reasons = $this->checkAccess(InspectorInterface::DELETE, $foundedEntity);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        $em->remove($foundedEntity);
        $em->flush();

        return $this->generateResponse();
    }

    /**
     * @param Request         $request    A Request instance.
     * @param string|callable $permission A requested permission.
     * @param string          $formClass  Form class fqcn.
     * @param callable        $processor  Function which process founded entities.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    protected function batchProcessing(
        Request $request,
        $permission,
        $formClass,
        callable $processor
    ) {
        $this->checkFormClass($formClass);

        $form = $this->createForm($formClass, null, [ 'class' => $this->entity ]);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (is_callable($permission)) {
                $permission = call_user_func_array($permission, $data);
            }

            if (! is_string($permission)) {
                throw new \InvalidArgumentException('$permission should be string or callable');
            }

            $reasons = $this->checkAccess($permission, $data['entities']);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            $response = call_user_func_array($processor, $data);
            if ($response === null) {
                $response = $this->generateResponse();
            }

            return $response;
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * @param string $formClass Form class fqcn.
     *
     * @return void
     */
    private function checkFormClass($formClass)
    {
        if (! is_string($formClass) || ! class_exists($formClass)) {
            throw new \InvalidArgumentException('$formClass should be fqcn');
        }

        if (($formClass !== EntitiesBatchType::class)
            && ! in_array(EntitiesBatchType::class, class_parents($formClass), true)) {
            throw new \InvalidArgumentException('Invalid form class '. $formClass);
        }
    }
}
