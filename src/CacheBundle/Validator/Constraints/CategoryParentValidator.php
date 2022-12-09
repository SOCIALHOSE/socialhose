<?php

namespace CacheBundle\Validator\Constraints;

use CacheBundle\Entity\Category;
use CacheBundle\Repository\CategoryRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\RuntimeException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class CategoryParentValidator
 * @package CacheBundle\Validator\Constraints
 */
class CategoryParentValidator extends ConstraintValidator
{

    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * CategoryParentValidator constructor.
     *
     * @param CategoryRepository $repository A CategoryRepository instance.
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated.
     * @param Constraint $constraint The constraint for the validation.
     *
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (! $constraint instanceof CategoryParent) {
            throw new UnexpectedTypeException($constraint, CategoryParent::CLASS_CONSTRAINT);
        }

        // Check current object.
        $current = $this->context->getObject();
        if (! $current instanceof Category) {
            throw new RuntimeException('This validator works only with categories.');
        }

        // We don't make any checks if current category is new.
        if ($current->getId() === null) {
            return;
        }

        // Check that we try to put category inside it self.
        if ($value instanceof Category) {
            $currentId = $current->getId();
            $valueId = $value->getId();

            if ($valueId === $currentId) {
                $this->context->addViolation('Try to place category inside itself.');
            }

            // Check that we try to put category inside one of it childes.
            if ($this->repository->isChildOf($valueId, $currentId)) {
                $this->context->addViolation('Try to place category inside it child.');
            }
        }
    }
}
