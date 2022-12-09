<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use AppBundle\Controller\Traits\TokenStorageAwareTrait;
use AppBundle\Controller\V1\AbstractV1Controller;
use AppBundle\Model\SortingOptions;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use UserBundle\Entity\Notification\NotificationSendHistory;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Enum\NotificationTypeEnum;
use UserBundle\Repository\NotificationSendHistoryRepository;
use UserBundle\Repository\RecipientRepository;

/**
 * Class ReceiverController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/receivers",
 *     service="user.controller.receiver"
 * )
 */
class ReceiverController extends AbstractV1Controller
{

    const DEFAULT_LIMIT = 30;

    use TokenStorageAwareTrait;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ReceiverController constructor.
     *
     * @param TokenStorageInterface  $tokenStorage A TokenStorageInterface
     *                                             instance.
     * @param EntityManagerInterface $em           A EntityManagerInterface
     *                                             instance.
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /**
     * Get list of available receivers.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("", methods={ "GET" })
     * @ApiDoc(
     *  resource=true,
     *  section="Receivers",
     *  filters={
     *     {
     *          "name"="filter",
     *          "dataType"="string",
     *          "description"="Receivers name filter",
     *          "requirements"="[\w\s]+"
     *     },
     *     {
     *          "name"="exclude",
     *          "dataType"="string",
     *          "description"="Comma separated list of ids.",
     *          "requirements"="[\w,]+"
     *     }
     *  },
     *  output={
     *     "class"="Pagination<UserBundle\Entity\Recipient\AbstractRecipient>",
     *     "groups"={ "id", "recipient_autocompletion" }
     *  },
     *  statusCodes={
     *     200="Receivers successfully funded."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        $keyword = trim($request->query->get('filter'));
        $exclude = array_filter(array_map('trim', explode(',', $request->query->get('exclude', ''))));

        /** @var RecipientRepository $repository */
        $repository = $this->em->getRepository(AbstractRecipient::class);
        $recipients = $repository->search(
            $this->getCurrentUser()->getId(),
            self::DEFAULT_LIMIT,
            $keyword,
            $exclude
        );

        return $this->generateResponse($recipients, 200, [ 'recipient_autocompletion', 'id' ]);
    }

    /**
     * Get email history for specified receiver.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/emailHistory", methods={ "GET" }, requirements={ "id": "\d+" })
     * @ApiDoc(
     *  resource=true,
     *  section="Receivers",
     *  filters={
     *     {
     *          "name"="page",
     *          "dataType"="integer",
     *          "description"="Requested page number, start from 1",
     *          "requirements"="\d+",
     *          "default"="1"
     *     },
     *     {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "description"="Max entities per page, default 100",
     *          "requirements"="\d+",
     *          "default"="100"
     *     },
     *     {
     *          "name"="sortField",
     *          "dataType"="string",
     *          "description"="Field name for sorting. Available: name, type, scheduleTime, sentTime",
     *          "requirements"="\w+",
     *          "default"="name",
     *          "required"=false
     *     },
     *     {
     *          "name"="sortDirection",
     *          "dataType"="string",
     *          "description"="Sort direction. Available: asc, desc",
     *          "requirements"="(asc|desc)",
     *          "default"="asc",
     *          "required"=false
     *     },
     *     {
     *          "name"="typeFilter",
     *          "dataType"="string",
     *          "description"="Filter receivers by notification type of specified entity id.",
     *          "requirements"="(alerts|newsletter|all)",
     *          "default"="all",
     *          "required"=false
     *     }
     *  },
     *  output={
     *     "class"="Pagination<UserBundle\Entity\Notification\NotificationSendHistory>",
     *     "groups"={ "id", "history", "schedule" }
     *  },
     *  statusCodes={
     *     200="Receivers successfully funded."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A AbstractRecipient entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function historyAction(Request $request, $id)
    {
        $recipient = $this->em->find(AbstractRecipient::class, $id);
        if (! $recipient instanceof AbstractRecipient) {
            return $this->generateResponse("Can't find receiver with id {$id}.", 404);
        }

        $sortingOptions = SortingOptions::fromRequest($request, 'sentTime');
        $typeFilter = $request->query->get('typeFilter', 'all');
        if (($typeFilter !== 'all') && ! NotificationTypeEnum::isValid($typeFilter)) {
            return $this->generateResponse("'typeFilter' should be one of all, ". implode(', ', NotificationTypeEnum::getAvailables()));
        }

        /** @var NotificationSendHistoryRepository $repository */
        $repository = $this->em->getRepository(NotificationSendHistory::class);
        $qb = $repository->getListForRecipient($recipient, $sortingOptions, $typeFilter);

        $pagination = $this->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->generateResponse($pagination, 200, [ 'id', 'history', 'schedule' ]);
    }
}
