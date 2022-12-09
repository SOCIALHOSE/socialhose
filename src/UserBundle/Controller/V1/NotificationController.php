<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\AbstractCRUDController;
use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Form\ActivatedEntitiesBatchType;
use ApiBundle\Form\EntitiesBatchType;
use ApiBundle\Form\PublishedEntitiesBatchType;
use ApiBundle\Form\NotificationSubscribeBatchType;
use ApiBundle\Security\Inspector\InspectorInterface;
use AppBundle\Exception\LimitExceedException;
use AppBundle\Model\SortingOptions;
use Doctrine\Common\Collections\Collection;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Notification\Notification;
use UserBundle\Entity\Notification\NotificationSendHistory;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Enum\StatusFilterEnum;
use UserBundle\Form\NotificationType;
use UserBundle\Mailer\MailerInterface;
use UserBundle\Manager\Notification\NotificationManagerInterface;
use UserBundle\Repository\NotificationRepository;
use UserBundle\Repository\NotificationSendHistoryRepository;
use UserBundle\Repository\RecipientRepository;
use UserBundle\Security\Inspector\NotificationInspector;
use UserBundle\UserBundleServices;

/**
 * Class NotificationController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/notifications",
 *     service="user.controller.notification"
 * )
 */
class NotificationController extends AbstractCRUDController
{

    /**
     * Get list of all notification's for current user.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("", methods={ "GET" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
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
     *          "description"="Field name for sorting. Available: name, type,
     *          published, sourcesCount, status",
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
     *          "name"="onlyPublished",
     *          "dataType"="boolean",
     *          "description"="Return only published email's if true",
     *          "default"="false",
     *          "required"=false
     *     },
     *     {
     *          "name"="entityId",
     *          "dataType"="integer",
     *          "description"="Recipient or group entity id",
     *          "required"=false
     *     },
     *     {
     *          "name"="filter",
     *          "dataType"="string",
     *          "description"="Filter notifications by part of name",
     *          "required"=false
     *     },
     *     {
     *          "name"="statusFilter",
     *          "dataType"="string",
     *          "description"="Filter notifications by subscription status of specified entity id.",
     *          "requirements"="(yes|no|all)",
     *          "default"="all",
     *          "required"=false
     *     }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *      "notifications"={
     *          "class"="Pagination<UserBundle\Entity\Notification\Notification>",
     *          "groups"={ "notification_list", "schedule", "id" }
     *      },
     *      "meta"={
     *          "dataType"="model",
     *          "description"="Response meta information",
     *          "required"=true,
     *          "readonly"=true,
     *          "children"={
     *           "sort"={
     *               "dataType"="model",
     *               "requited"=true,
     *               "readonly"=true,
     *               "children"={
     *                   "field"={
     *                       "dataType"="string",
     *                       "description"="Field name for sorting. Available:
     *                       name, type, published, sourcesCount, status",
     *                       "required"=true,
     *                       "readonly"=true
     *                   },
     *                   "direction"={
     *                       "dataType"="string",
     *                       "description"="Sort direction. Available: asc,
     *                       desc",
     *                       "required"=true,
     *                       "readonly"=true
     *                   }
     *               }
     *           }
     *          }
     *      }
     *     }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        /** @var NotificationRepository $repository */
        $repository = $this->getManager()->getRepository(Notification::class);

        $currentUser = $this->getCurrentUser();
        $sortingOptions = SortingOptions::fromRequest($request, 'name');

        $entityId = trim($request->query->get('entityId'));
        $nameFilter = trim($request->query->get('filter'));
        if ($entityId !== '') {
            /** @var RecipientRepository $recipientRepository */
            $recipientRepository = $this->getManager()->getRepository(AbstractRecipient::class);
            $recipient = $recipientRepository->find($entityId);
            if (! $recipient instanceof AbstractRecipient) {
                return $this->generateResponse("Can't find  recipient or group recipient with id {$entityId}.", 404);
            }
            //
            // Check access.
            //
            $reasons = $this->checkAccess(InspectorInterface::READ, $recipient);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            $statusFilter = trim($request->query->get('statusFilter', StatusFilterEnum::ALL));

            if ($statusFilter !== '') {
                if (! StatusFilterEnum::isValid($statusFilter)) {
                    return $this->generateResponse("'statusFilter' should be one of ". implode(', ', StatusFilterEnum::getAvailables()));
                }

                $statusFilter = new StatusFilterEnum($statusFilter);
            }
            $qb = $repository->getQueryBuilderForRecipient(
                $recipient,
                $currentUser,
                $sortingOptions,
                $statusFilter,
                $nameFilter
            );
        } else {
            $onlyPublished = $request->query->get('onlyPublished', 'false') !== 'false';
            $qb = $repository->getQueryBuilder($sortingOptions, $currentUser, $onlyPublished, $nameFilter);
        }

        //
        // We should get all paginated data and put 'subscribed' field value into
        // Notification entity.
        //
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginate($request, $qb);
        $elements = iterator_to_array($pagination);
        $elements = array_map(function (array $element) {
            /** @var Notification $notification */
            $notification = $element[0];

            $notification->subscribed = (bool) $element['subscribed'];

            return $notification;
        }, $elements);

        return $this->generateResponse(
            [
                'notifications' => [
                    'data' => $elements,
                    'count' => count($elements),
                    'totalCount' => $pagination->getTotalItemCount(),
                    'page' => $pagination->getCurrentPageNumber(),
                    'limit' => $pagination->getItemNumberPerPage(),
                ],
                'meta' => [ 'sort' => $sortingOptions ],
            ],
            200,
            [ 'notification_list', 'schedule', 'id' ]
        );
    }

    /**
     * Create new no.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("", methods={ "POST" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="UserBundle\Form\NotificationType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="UserBundle\Entity\Notification\Notification",
     *     "groups"={ "notification", "schedule", "id" }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        $user = $this->getCurrentUser();
        $notification = Notification::create()
            ->setOwner($user)
            ->setBillingSubscription($user->getBillingSubscription());

        $form = $this->createForm(NotificationType::class, $notification);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $reasons = $this->checkAccess(NotificationInspector::CREATE, $notification);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            $appLimit = $notification->getNotificationType()->toAppLimit();
            try {
                $user->useLimit($appLimit);
            } catch (LimitExceedException $exception) {
                return $this->generateResponse([
                    'failedRestriction' => (string) $appLimit,
                    'restrictions' => $user->getRestrictions(),
                ], 402);
            }

            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->persists($notification);

            $this->getManager()->persist($user);
            $this->getManager()->flush();

            return $this->generateResponse($notification, 200, [
                'notification',
                'schedule',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Update alert.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}", methods={ "PUT" }, requirements={ "id"="\d+" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="UserBundle\Form\NotificationType",
     *     "name"=false
     *  },
     *  output={
     *     "class"="UserBundle\Entity\Notification\Notification",
     *     "groups"={ "notification", "schedule", "id" }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Notification entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function updateAction(Request $request, $id)
    {
        /** @var NotificationRepository $repository */
        $repository = $this->getManager()->getRepository(Notification::class);

        $notification = $repository->get($id);
        if (! $notification instanceof Notification) {
            return $this->generateResponse("Can't find notification with id {$id}.", 404);
        }

        $form = $this->createForm(NotificationType::class, $notification);

        $form->submit($request->request->all());
        if ($form->isSubmitted() && $form->isValid()) {
            $reasons = $this->checkAccess(NotificationInspector::UPDATE, $notification);
            if (count($reasons) > 0) {
                return $this->generateResponse($reasons, 403);
            }

            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->persists($notification);

            return $this->generateResponse($notification, 200, [
                'notification',
                'schedule',
                'id',
            ]);
        }

        return $this->generateResponse($form, 400);
    }

    /**
     * Get notification.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}", methods={ "GET" }, requirements={ "id"="\d+" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  output={
     *     "class"="UserBundle\Entity\Notification\Notification",
     *     "groups"={ "notification", "schedule", "id" }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param integer $id A Notification entity id.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function getAction($id)
    {
        /** @var NotificationRepository $repository */
        $repository = $this->getManager()->getRepository(Notification::class);

        $notification = $repository->get($id);
        if (! $notification instanceof Notification) {
            return $this->generateResponse("Can't find notification with id {$id}.", 404);
        }
        $reasons = $this->checkAccess(NotificationInspector::READ, $notification);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        return $this->generateResponse($notification, 200, [
            'notification',
            'schedule',
            'id',
        ]);
    }

    /**
     * Activate notifications.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/active", methods={ "PUT" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of notifications ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          },
     *          "active"={
     *              "dataType"="Boolean flag",
     *              "actualType"="boolean",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          }
     *      }
     *
     *  },
     *  statusCodes={
     *     204="Notification successfully activated."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function activeAction(Request $request)
    {
        $processor = function (Collection $notifications, $active) {
            /** @var NotificationManagerInterface $manager */
            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->activatedToggle(
                $notifications->toArray(),
                (bool) $active
            );
        };

        return $this->batchProcessing(
            $request,
            NotificationInspector::UPDATE,
            ActivatedEntitiesBatchType::class,
            $processor
        );
    }

    /**
     * Publish notifications.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/published", methods={ "PUT" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of notifications ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          },
     *          "published"={
     *              "dataType"="Boolean flag",
     *              "actualType"="boolean",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          }
     *      }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function publishedAction(Request $request)
    {
        $processor = function (Collection $notifications, $subscribed) {
            /** @var NotificationManagerInterface $manager */
            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->publishedToggle(
                $notifications->toArray(),
                (bool) $subscribed
            );
        };

        return $this->batchProcessing(
            $request,
            NotificationInspector::UPDATE,
            PublishedEntitiesBatchType::class,
            $processor
        );
    }

    /**
     * Subscribe alert.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/subscribe", methods={ "POST" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of notifications ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          },
     *          "subscribed"={
     *              "dataType"="Boolean flag",
     *              "actualType"="boolean",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          }
     *      }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function subscribeAction(Request $request)
    {
        $processor = function (Collection $notifications, $subscribed) {
            $user = $this->getCurrentUser();

            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->subscriptionToggle(
                $user->getRecipient(),
                $notifications->toArray(),
                (bool) $subscribed
            );

            if ($subscribed === false) {
                /** @var Notification $notification */
                foreach ($notifications as $notification) {
                    if ($notification->isUnsubscribeNotification()) {
                        /** @var MailerInterface $mailer */
                        $mailer = $this->get(UserBundleServices::MAILER);
                        $mailer->sendUnsubscribe($notification, $user);
                    }
                }
            }
        };

        return $this->batchProcessing(
            $request,
            function (Collection $notifications, $subscribed) {
                return $subscribed ? NotificationInspector::SUBSCRIBE : NotificationInspector::UNSUBSCRIBE;
            },
            NotificationSubscribeBatchType::class,
            $processor
        );
    }

    /**
     * Delete notification.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/delete", methods={ "POST" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of notifications ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *      }
     *      }
     *  },
     *  statusCodes={
     *     204="Notifications successfully removed."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function deleteAction(Request $request)
    {
        $processor = function (Collection $notifications) {
            /** @var NotificationManagerInterface $manager */
            $manager = $this->get(UserBundleServices::NOTIFICATION_MANAGER);
            $manager->remove($notifications->toArray());

            /** @var Notification $notification */
            $user = $this->getCurrentUser();
            foreach ($notifications as $notification) {
                $appLimit = $notification->getNotificationType()->toAppLimit();

                $user->releaseLimit($appLimit);
            }

            $this->getManager()->persist($user);
            $this->getManager()->flush();
        };

        return $this->batchProcessing(
            $request,
            NotificationInspector::DELETE,
            EntitiesBatchType::class,
            $processor
        );
    }

    /**
     * sendHistory alert.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/{id}/history", methods={ "GET" }, requirements={ "id"="\d+" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *  filters={
     *     {
     *          "name"="offset",
     *          "dataType"="integer",
     *          "description"="Offset from beginning of collection, start from
     *          1",
     *          "requirements"="\d+",
     *          "default"="1"
     *     },
     *     {
     *          "name"="limit",
     *          "dataType"="integer",
     *          "description"="Max entities per page, default 10",
     *          "requirements"="\d+",
     *          "default"="10"
     *     },
     *  },
     *  input={
     *      "class"="",
     *      "data"={
     *          "page"={
     *              "dataType"="Number page",
     *              "actualType"="integer",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          },
     *          "limit"={
     *              "dataType"="Limit",
     *              "actualType"="integer",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          }
     *      }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *          "data"={
     *              "description"="Requested entities.",
     *              "dataType"="Collection of notification send dates",
     *              "actualType"="collection",
     *              "subType"="History",
     *              "required"=true,
     *              "readonly"=true,
     *              "children"={
     *               "date"={
     *                   "dataType"="string",
     *                   "required"=true,
     *                   "readonly"=true
     *               }
     *           }
     *          },
     *          "count"={
     *              "description"="Count of requested entities on current
     *              page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "totalCount"={
     *              "description"="Total count of founded entities.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "page"={
     *              "description"="Current page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "limit"={
     *              "description"="Max entities per page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          }
     *     }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A Notification entity instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function historyAction(Request $request, $id)
    {
        $notification = $this->getManager()->getRepository(Notification::class)->find($id);
        if (! $notification instanceof Notification) {
            return $this->generateResponse("Not found notification with id {$id}.", 404);
        }

        //
        // Check access to notification.
        //
        $reasons = $this->checkAccess(NotificationInspector::READ, $notification);
        if (count($reasons) > 0) {
            return $this->generateResponse($reasons, 403);
        }

        /** @var NotificationSendHistoryRepository $repository */
        $repository = $this->getManager()->getRepository(NotificationSendHistory::class);
        $qb = $repository->getListForNotification($id);
        $pagination = $this->paginate($request, $qb);

        return $this->generateResponse($pagination);
    }

    /**
     * Filters notifications.
     *
     * @Roles("ROLE_SUBSCRIBER")
     *
     * @Route("/filters", methods={ "GET" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
     *     filters={
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
     *          "description"="Field name for sorting. Available: name, type,
     *          published, sourcesCount, status",
     *          "requirements"="\w+",
     *          "default"="name",
     *          "required"=false
     *     }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *          "data"={
     *              "description"="Requested entities.",
     *              "dataType"="Collection of notification send dates",
     *              "actualType"="collection",
     *              "subType"="History",
     *              "required"=true,
     *              "readonly"=true,
     *              "children"={
     *               "date"={
     *                   "dataType"="string",
     *                   "required"=true,
     *                   "readonly"=true
     *               }
     *           }
     *          },
     *          "count"={
     *              "description"="Count of requested entities on current
     *              page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "totalCount"={
     *              "description"="Total count of founded entities.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "page"={
     *              "description"="Current page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "limit"={
     *              "description"="Max entities per page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          }
     *     }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function filtersAction(Request $request)
    {
        /** @var NotificationRepository $repository */
        $repository = $this->getManager()->getRepository(Notification::class);
        $sortingOptions = SortingOptions::fromRequest($request, 'name');
        $typeFilter = trim($request->query->get('type'));
        switch ($typeFilter) {
            case 'owner':
                $filter = $repository->computeUserNotificationsCount($sortingOptions);
                break;
            case 'recipient':
                $filter = $repository->computeRecipientNotificationsCount($sortingOptions);
                break;
            case 'feed':
                $filter = $repository->getCountFeedNotifications($sortingOptions);
                break;
            default:
                return $this->generateResponse("Bad type value.", 400);
        }
        // We should get all paginated data
        // Notification entity.
        //
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginate($request, $filter);
        $elements = iterator_to_array($pagination);

        return $this->generateResponse(
            [
                'filters' => [
                    'data' => $elements,
                    'count' => count($elements),
                    'totalCount' => $pagination->getTotalItemCount(),
                    'page' => $pagination->getCurrentPageNumber(),
                    'limit' => $pagination->getItemNumberPerPage(),
                ],
                'meta' => [
                    'sort' => $sortingOptions,
                ],
            ],
            200
        );
    }


    /**
     * All notifications.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/all", methods={ "GET" })
     * @ApiDoc(
     *  resource=true,
     *  section="Notification",
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
     *          "description"="Field name for sorting. Available: name, type,
     *          published, sourcesCount, status",
     *          "requirements"="\w+",
     *          "default"="name",
     *          "required"=false
     *     }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *          "data"={
     *              "description"="Requested entities.",
     *              "dataType"="Collection of notification send dates",
     *              "actualType"="collection",
     *              "subType"="History",
     *              "required"=true,
     *              "readonly"=true,
     *              "children"={
     *               "date"={
     *                   "dataType"="string",
     *                   "required"=true,
     *                   "readonly"=true
     *               }
     *           }
     *          },
     *          "count"={
     *              "description"="Count of requested entities on current
     *              page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "totalCount"={
     *              "description"="Total count of founded entities.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "page"={
     *              "description"="Current page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          },
     *          "limit"={
     *              "description"="Max entities per page.",
     *              "dataType"="integer",
     *              "required"=true,
     *              "readonly"=true,
     *          }
     *     }
     *  },
     *  statusCodes={
     *     200="Subscribers successfully returned."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function notificationsAllAction(Request $request)
    {
        /** @var NotificationRepository $repository */
        $repository = $this->getManager()->getRepository(Notification::class);
        $sortingOptions = SortingOptions::fromRequest($request, 'name');
        $typeFilter = trim($request->query->get('filterType'));
        $filterId = trim($request->query->get('filterId'));

        $user = $this->getCurrentUser();
        if ($filterId !== '') {
            $qb = $repository->getQueryBuilderForFilter(
                $sortingOptions,
                $typeFilter,
                $filterId,
                $user
            );
        } else {
            $qb = $repository->getNotificationsAllQueryBuilder(
                $sortingOptions,
                $user->getBillingSubscription()
            );
        }
        //
        // We should get all paginated data and put 'subscribed' field value into
        // Notification entity.
        //
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginate($request, $qb);
        $elements = iterator_to_array($pagination);

        return $this->generateResponse(
            [
                'notifications' => [
                    'data' => $elements,
                    'count' => count($elements),
                    'totalCount' => $pagination->getTotalItemCount(),
                    'page' => $pagination->getCurrentPageNumber(),
                    'limit' => $pagination->getItemNumberPerPage(),
                ],
                'meta' => [ 'sort' => $sortingOptions ],
            ],
            200,
            [ 'notification_list', 'schedule', 'id' ]
        );
    }
}
