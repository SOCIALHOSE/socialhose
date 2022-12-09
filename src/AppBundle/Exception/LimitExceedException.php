<?php

namespace AppBundle\Exception;

use UserBundle\Entity\User;
use UserBundle\Enum\AppLimitEnum;

/**
 * Class LimitExceedException
 *
 * Occurred when we user try to reserve more limits that allowed.
 *
 * @package AppBundle\Exception
 */
class LimitExceedException extends \RuntimeException
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var AppLimitEnum
     */
    private $limit;

    /**
     * @var integer
     */
    private $currValue;

    /**
     * @var integer
     */
    private $requested;

    /**
     * @var integer
     */
    private $max;

    /**
     * NotAllowedException constructor.
     *
     * @param User         $user      Who try to make not allowed action.
     * @param AppLimitEnum $appLimit  Which limit is requested.
     * @param integer      $currValue Current limit value.
     * @param integer      $requested How much limit is requested.
     * @param integer      $max       Max value of limit.
     */
    public function __construct(
        User $user,
        AppLimitEnum $appLimit,
        $currValue,
        $requested,
        $max
    ) {
        parent::__construct(sprintf(
            'User \'%s\' is exceed limit \'%s\'. Current limit value %s, request %s but limit is %s',
            $user->getId(),
            $appLimit->getValue(),
            $currValue,
            $requested,
            $max
        ));

        $this->user = $user;
        $this->limit = $appLimit;
        $this->currValue = $currValue;
        $this->requested = $requested;
        $this->max = $max;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return AppLimitEnum
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return integer
     */
    public function getCurrValue()
    {
        return $this->currValue;
    }

    /**
     * @return integer
     */
    public function getRequested()
    {
        return $this->requested;
    }

    /**
     * @return integer
     */
    public function getMax()
    {
        return $this->max;
    }
}
