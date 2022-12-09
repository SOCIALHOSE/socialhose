<?php

namespace CacheBundle\Service\Factory\Analytic;

use AppBundle\Doctrine\ORM\BaseEntityRepository;
use AppBundle\Exception\NotAllowedException;
use CacheBundle\DTO\AnalyticDTO;
use CacheBundle\Entity\Analytic\Analytic;
use CacheBundle\Entity\Analytic\AnalyticContext;
use CacheBundle\Entity\Feed\AbstractFeed;
use IndexBundle\Filter\FilterInterface;
use UserBundle\Entity\User;
use UserBundle\Enum\AppPermissionEnum;

/**
 * Class AnalyticFactory
 *
 * @package CacheBundle\Service\Factory\Analytic
 */
class AnalyticFactory implements AnalyticFactoryInterface
{

    /**
     * @var BaseEntityRepository
     */
    private $analyticRepository;

    /**
     * AnalyticFactory constructor.
     *
     * @param BaseEntityRepository $analyticRepository A internal analytic repository.
     */
    public function __construct(BaseEntityRepository $analyticRepository)
    {
        $this->analyticRepository = $analyticRepository;
    }

    /**
     * @param AnalyticDTO $dto A analytic dto instance.
     * @param User $user User for which we create analytic.
     *
     * @return Analytic
     *
     * @throws \InvalidArgumentException If got invalid dto.
     * @throws NotAllowedException If specified user can't create save.
     */
    public function createAnalytic(AnalyticDTO $dto, User $user)
    {
        if (!$user->isAllowedTo(AppPermissionEnum::analytics())) {
            throw new NotAllowedException($user, AppPermissionEnum::analytics());
        }

        $context = $this->createContext($dto);

        return new Analytic($user, $context);
    }

    /**
     * @param AnalyticDTO $dto A data required for creating analytic.
     *
     * @return AnalyticContext
     */
    private function createContext(AnalyticDTO $dto)
    {
        if (!\app\a\allInstanceOf($dto->feeds, AbstractFeed::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$dto->feeds\' should be an array of \'%s\' instances',
                AbstractFeed::class
            ));
        }

        if (!\app\a\allInstanceOf($dto->filters, FilterInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$dto->filters\' should be an array of \'%s\' instances',
                FilterInterface::class
            ));
        }

        if (!is_array($dto->rawFilters)) {
            throw new \InvalidArgumentException('\'$dto->rawFilters\' should be an array');
        }

        $hash = $this->computeHash($dto->feeds, $dto->filters);

        $analytic = $this->analyticRepository->find($hash);
        if ($analytic === null) {
            $analytic = new AnalyticContext(
                $hash,
                $dto->feeds,
                $dto->filters,
                $dto->rawFilters
            );
        }

        return $analytic;
    }

    /**
     * Compute hash for analytic.
     *
     * @param AbstractFeed[] $feeds Array of used feeds.
     * @param FilterInterface[] $filters Array of used filters.
     *
     * @return string
     */
    private function computeHash(array $feeds, array $filters)
    {
        $collectionIds = $this->getUniqueCollectionIds($feeds);
        sort($collectionIds);

        foreach ($filters as $filter) {
            $filter->sort();
        }

        return md5(serialize($collectionIds) . serialize($filters));
    }

    /**
     * @param AbstractFeed[] $feeds Collection of used feeds.
     *
     * @return string[]
     */
    private function getUniqueCollectionIds(array $feeds)
    {
        $collectionIds = [];

        foreach ($feeds as $feed) {
            $collectionIds[$feed->getCollectionId()] = true;
        }

        return array_keys($collectionIds);
    }


    /**
     * @param AnalyticDTO $dto
     * @param User $user
     * @param Analytic $oldAnalytic
     * @return Analytic|AnalyticContext|object|null
     */
    public function updateAnalytic(AnalyticDTO $dto, User $user, Analytic $oldAnalytic)
    {
        if (!$user->isAllowedTo(AppPermissionEnum::analytics())) {
            throw new NotAllowedException($user, AppPermissionEnum::analytics());
        }

        if (!\app\a\allInstanceOf($dto->feeds, AbstractFeed::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$dto->feeds\' should be an array of \'%s\' instances',
                AbstractFeed::class
            ));
        }

        if (!\app\a\allInstanceOf($dto->filters, FilterInterface::class)) {
            throw new \InvalidArgumentException(sprintf(
                '\'$dto->filters\' should be an array of \'%s\' instances',
                FilterInterface::class
            ));
        }

        if (!is_array($dto->rawFilters)) {
            throw new \InvalidArgumentException('\'$dto->rawFilters\' should be an array');
        }

        $hash = $this->computeHash($dto->feeds, $dto->filters);
        $analytic = $this->analyticRepository->find($hash);
        if ($analytic === null) {
            $analytic = new AnalyticContext(
                $hash,
                $dto->feeds,
                $dto->filters,
                $dto->rawFilters
            );

            /** @var $oldAnalytic $analytic */
            $oldAnalytic->setContext($analytic);
        }

        return $oldAnalytic;
    }
}
