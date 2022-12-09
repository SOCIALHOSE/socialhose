<?php

namespace UserBundle\Repository;

use AppBundle\Model\SortingOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\User;
use UserBundle\Enum\StatusFilterEnum;
use UserBundle\Utils\AdditionalConditions;

/**
 * GroupRecipientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupRecipientRepository extends EntityRepository
{

    /**
     * Get available for specified user.
     *
     * @param User $user A User entity instance.
     *
     * @return QueryBuilder
     */
    public function getAvailableForUser(User $user)
    {
        return $this->createQueryBuilder('Grp')
            ->where('Grp.owner = :user')
            ->setParameter('user', $user->getId());
    }

    /**
     * @param integer        $user           A User entity id.
     * @param SortingOptions $sortingOptions A SortingOptions instance.
     * @param string         $nameFilter     Filter recipient groups by name.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderForUser(
        $user,
        SortingOptions $sortingOptions,
        $nameFilter = ''
    ) {
        $sortField = $this->resolveSortField($sortingOptions);
        $expr = $this->_em->getExpressionBuilder();

        $condition = $expr->andX($expr->eq('Grp.owner', ':user'));
        $parameters = new ArrayCollection([ new Parameter('user', $user) ]);

        if ($nameFilter !== '') {
            $condition->add($expr->like('Grp.name', ':filter'));
            $parameters[] = new Parameter('filter', '%'. $nameFilter .'%');
        }

        return $this->createQueryBuilder('Grp')
            ->where($condition)
            ->setParameters($parameters)
            ->orderBy($sortField, $sortingOptions->getSortDirection());
    }

    /**
     * @param integer              $user                 A User entity id.
     * @param integer              $person               A PersonRecipient entity
     *                                                   id.
     * @param StatusFilterEnum     $statusFilter         A StatusFilterEnum instance.
     * @param SortingOptions       $sortingOptions       A SortingOptions instance.
     * @param string               $filter               Filter recipient groups
     *                                                   by name.
     * @param AdditionalConditions $additionalConditions A AdditionalConditions
     *                                                   instance.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilderForPerson(
        $user,
        $person,
        StatusFilterEnum $statusFilter,
        SortingOptions $sortingOptions,
        $filter,
        AdditionalConditions $additionalConditions
    ) {
        $sortField = $this->resolveSortField($sortingOptions);
        $expr = $this->_em->getExpressionBuilder();

        $condition = $expr->andX($expr->eq('Grp.owner', ':user'));
        $parameters = new ArrayCollection([
            new Parameter('user', $user),
            new Parameter('person', $person),
        ]);
        $parameters = $additionalConditions->addToParameters($parameters);

        if ($filter !== '') {
            $condition->add($expr->like('Grp.name', ':filter'));
            $parameters[] = new Parameter('filter', '%'. $filter .'%');
        }

        $qb = $this->createQueryBuilder('Grp');

        switch ($statusFilter->getValue()) {
            //
            // Show only not enrolled groups.
            //
            case StatusFilterEnum::NO:
                //
                // Select groups ids which has association with specified recipient
                // and remove them from results.
                //
                $subCondition = $expr->andX(
                    $expr->eq('_Person.id', ':person'),
                    $expr->eq('_Grp.owner', ':user')
                );
                $subCondition = $additionalConditions->addToConditions($subCondition, '_Grp');

                $subDql = $this->createQueryBuilder('_Grp')
                    ->select('_Grp.id')
                    ->leftJoin('_Grp.recipients', '_Person')
                    ->where($subCondition)
                    ->getDQL();

                $condition->add($expr->notIn('Grp', $subDql));
                $qb->addSelect('0 AS enrolled');
                break;

            //
            // Fetch only enrolled groups.
            //
            case StatusFilterEnum::YES:
                $condition->add($expr->eq('Person.id', ':person'));
                $condition = $additionalConditions->addToConditions($condition, 'Grp');


                $qb
                    ->join('Grp.recipients', 'Person')
                    ->addSelect('1 AS enrolled');
                break;

            //
            // If we not apply filters we should check in which groups specified
            // recipient is enrolled.
            //
            case StatusFilterEnum::ALL:
                $countCondition = $expr->andX(
                    $expr->eq('_Person.id', ':person'),
                    $expr->eq('_Grp.id', 'Grp.id')
                );

                $countCondition = $additionalConditions->addToConditions($countCondition, 'Grp');

                $countDQL = $this->createQueryBuilder('_Grp')
                    ->select('COUNT(_Grp.id)')
                    ->join('_Grp.recipients', '_Person')
                    ->where($countCondition)
                    ->getDQL();

                $qb->addSelect("(CASE WHEN ({$countDQL}) > 0 THEN 1 ELSE 0 END) AS enrolled");
                break;
        }

        return $qb
            ->where($condition)
            ->setParameters($parameters)
            ->orderBy($sortField, $sortingOptions->getSortDirection());
    }

    /**
     * Get group recipient by id.
     *
     * @param integer $id A GroupRecipient entity id.
     *
     * @return GroupRecipient|null
     */
    public function get($id)
    {
        return $this->createQueryBuilder('Grp')
            ->where('Grp.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param SortingOptions $sortingOptions A SortingOptions instance.
     *
     * @return string
     */
    private function resolveSortField(SortingOptions $sortingOptions)
    {
        $sortField = $sortingOptions->getFieldName();
        switch ($sortField) {
            case 'active':
            case 'name':
            case 'recipientsNumber':
                $sortField = "Grp.{$sortField}";
                break;

            case 'creationDate':
                $sortField = 'Grp.createdAt';
                break;

            default:
                throw new \InvalidArgumentException("Unknown field name '{$sortField}'.");
        }

        return $sortField;
    }
}
