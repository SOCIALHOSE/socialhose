<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Controller\Annotation\Roles;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use UserBundle\Entity\Notification\NotificationTheme;
use UserBundle\Repository\NotificationThemeRepository;

/**
 * Class NotificationThemeController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/notifications/themes",
 *     service="user.controller.notification_theme"
 * )
 */
class NotificationThemeController extends AbstractApiController
{

    /**
     * Get list of all notification's for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/default", methods={ "GET" })
     * @ApiDoc(
     *  resource=true,
     *  section="NotificationTheme",
     *  output={
     *      "class"="UserBundle\Entity\Notification\NotificationTheme",
     *      "groups"={ "id", "notification_theme"}
     *  },
     *  statusCodes={
     *     200="Default notification successfully returned.",
     *     404="Can't find default notification theme."
     *  }
     * )
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function defaultAction()
    {
        /** @var NotificationThemeRepository $repository */
        $repository = $this->getManager()->getRepository(NotificationTheme::class);
        $notification = $repository->getDefault();

        if (! $notification instanceof NotificationTheme) {
            return $this->generateResponse('Can\'t find default notification theme', 404);
        }

        return $this->generateResponse($notification, 200, [
            'id',
            'notification_theme',
        ]);
    }
}
