<?php

namespace CacheBundle\Service\Factory\Analytic;

use AppBundle\Exception\NotAllowedException;
use CacheBundle\DTO\AnalyticDTO;
use CacheBundle\Entity\Analytic\Analytic;
use UserBundle\Entity\User;

/**
 * Interface AnalyticFactoryInterface
 *
 * Factory for creating saved analytics for specified users.
 *
 * @package CacheBundle\Service\Factory\Analytic
 */
interface AnalyticFactoryInterface
{

    /**
     * @param AnalyticDTO $dto  A analytic dto instance.
     * @param User        $user User for which we create analytic.
     *
     * @return Analytic
     *
     * @throws \InvalidArgumentException If got invalid dto.
     * @throws NotAllowedException If specified user can't create save.
     */
    public function createAnalytic(AnalyticDTO $dto, User $user);

    /**
     * @param AnalyticDTO $dto
     * @param User $user
     * @param Analytic $analytic
     * @return mixed
     */
    public function updateAnalytic(AnalyticDTO $dto, User $user, Analytic $analytic);

}
