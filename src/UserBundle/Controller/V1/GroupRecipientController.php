<?php

namespace UserBundle\Controller\V1;

use ApiBundle\Controller\Annotation\Roles;
use ApiBundle\Security\Inspector\InspectorInterface;
use AppBundle\Model\SortingOptions;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Recipient\AbstractRecipient;
use UserBundle\Entity\Recipient\GroupRecipient;
use UserBundle\Entity\Recipient\PersonRecipient;
use UserBundle\Enum\StatusFilterEnum;
use UserBundle\Repository\GroupRecipientRepository;
use UserBundle\Repository\PersonRecipientRepository;
use UserBundle\Utils\AdditionalConditions;

/**
 * Class GroupRecipientController
 * @package UserBundle\Controller\V1
 *
 * @Route(
 *     "/recipients/groups",
 *     service="user.controller.group_recipient"
 * )
 */
class GroupRecipientController extends AbstractRecipientController
{

    /**
     * Get list of available person recipients.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("", methods={ "GET" })
     * @ApiDoc(
     *  resource="Group",
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
     *          "name"="recipientId",
     *          "dataType"="string",
     *          "description"="If set get recipients for specified recipient group",
     *          "requirements"="\d+",
     *          "required"=false
     *     },
     *     {
     *          "name"="sortField",
     *          "dataType"="string",
     *          "description"="Field name for sorting. Available: name, active, recipientsNumber, creationDate",
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
     *          "name"="filter",
     *          "dataType"="string",
     *          "description"="Keyword for searching groups by names",
     *          "requirements"="\w+"
     *     },
     *     {
     *          "name"="statusFilter",
     *          "dataType"="string",
     *          "description"="Keyword for searching groups by status",
     *          "requirements"="(no|yes|all)"
     *     },
     *     {
     *          "name"="include",
     *          "dataType"="string",
     *          "description"="Comma separated list of recipient group ids."
     *     },
     *     {
     *          "name"="exclude",
     *          "dataType"="string",
     *          "description"="Comma separated list of recipient group ids."
     *     }
     *  },
     *  output={
     *     "class"="",
     *     "data"={
     *      "groups"={
     *          "class"="UserBundle\Entity\Recipient\PersonRecipient",
     *          "groups"={ "id", "recipient" }
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
     *                       "description"="Field name for sorting. Available: name, email, creationDate, active",
     *                       "required"=true,
     *                       "readonly"=true
     *                   },
     *                   "direction"={
     *                       "dataType"="string",
     *                       "description"="Sort direction. Available: asc, desc",
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
     *     200="Recipients groups  successfully funded.",
     *     403="Don't has requested permissions."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function listAction(Request $request)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        $recipientId = trim($request->query->get('recipientId'));
        $currentUser = $this->getCurrentUser();

        //
        // Get sort parameters and filter.
        //
        $sortingOptions = SortingOptions::fromRequest($request, 'name');
        $filter = trim($request->query->get('filter'));

        /** @var GroupRecipientRepository $groupRepository */
        $groupRepository = $em->getRepository(GroupRecipient::class);

        //
        // If we got group id we should try to fetch proper recipient group and
        // check that user can get this group recipient.
        //
        if ($recipientId !== '') {
            /** @var PersonRecipientRepository $personRepository */
            $personRepository = $em->getRepository(PersonRecipient::class);
            $person = $personRepository->getForUser($recipientId, $currentUser->getId());

            if (! $person instanceof PersonRecipient) {
                return $this->generateResponse("Can't find recipient with id {$recipientId}.", 404);
            }

            //
            // Check access.
            //
            $reasons = $this->checkAccess(InspectorInterface::READ, $person);
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

            $additionalCond = AdditionalConditions::fromRequest($request);

            $qb = $groupRepository->getQueryBuilderForPerson(
                $currentUser->getId(),
                $person->getId(),
                $statusFilter,
                $sortingOptions,
                $filter,
                $additionalCond
            );
        } else {
            $qb = $groupRepository->getQueryBuilderForUser(
                $currentUser->getId(),
                $sortingOptions,
                $filter
            );
        }

        //
        // We should get all paginated data and put 'subscribed' field value into
        // Notification entity.
        //
        /** @var SlidingPagination $pagination */
        $pagination = $this->paginate($request, $qb);

        $serializationGroups = [ 'recipient', 'id' ];
        if ($recipientId !== '') {
            $data = array_map(function (array $element) {
                /** @var AbstractRecipient $recipient */
                $recipient = $element[0];

                $recipient->enrolled = (bool) $element['enrolled'];

                return $recipient;
            }, iterator_to_array($pagination));

            $serializationGroups[] = 'sublist';
            $totalCount = $pagination->getTotalItemCount();

            $pagination = $this->paginate($request, $data);
            $pagination->setTotalItemCount($totalCount);
        }

        return $this->generateResponse(
            [
                'groups' => $pagination,
                'meta' => [ 'sort' => $sortingOptions ],
            ],
            200,
            $serializationGroups
        );
    }

    /**
     * Create new recipient group.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("", methods={ "POST" })
     * @ApiDoc(
     *  resource="Group",
     *  section="Receivers",
     *  input={
     *     "class"="UserBundle\Form\GroupRecipientType",
     *     "name"=false
     *  },
     *  output={
     *      "class"="UserBundle\Entity\Recipient\GroupRecipient",
     *      "groups"={ "id", "recipient" }
     *  },
     *  statusCodes={
     *     204="New recipient group successfully created.",
     *     400="Invalid parameters."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    public function createAction(Request $request)
    {
        return parent::createEntity($request, GroupRecipient::create()->setOwner($this->getCurrentUser()));
    }

    /**
     * Update specified recipient group.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/{id}", methods={ "PUT" }, requirements={ "id": "\d*" })
     * @ApiDoc(
     *  resource="Group",
     *  section="Receivers",
     *  input={
     *     "class"="UserBundle\Form\GroupRecipientType",
     *     "name"=false
     *  },
     *  output={
     *      "class"="UserBundle\Entity\Recipient\GroupRecipient",
     *      "groups"={ "id", "recipient" }
     *  },
     *  statusCodes={
     *     204="Recipient group successfully updated.",
     *     400="Invalid parameters."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A GroupRecipient entity id.
     *
     * @return \ApiBundle\Entity\ManageableEntityInterface|\ApiBundle\Response\ViewInterface
     */
    public function putAction(Request $request, $id)
    {
        return parent::putEntity($request, $id);
    }

    /**
     * Delete recipient groups.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/delete", methods={ "POST" })
     * @ApiDoc(
     *  resource="Group",
     *  section="Receivers",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of recipient group ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *      }
     *      }
     *  },
     *  output={
     *      "class"="UserBundle\Entity\Recipient\GroupRecipient",
     *      "groups"={ "id", "recipient" }
     *  },
     *  statusCodes={
     *     204="Recipient groups successfully deleted.",
     *     400="Invalid parameters."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface|null
     */
    public function deleteAction(Request $request)
    {
        return $this->batchDelete($request);
    }

    /**
     * Activate/deactivate recipient groups.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/active", methods={ "PUT" })
     * @ApiDoc(
     *  resource="Group",
     *  section="Receivers",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of recipient groups ids",
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
     *     204="Recipient groups successfully activated/deactivated."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function activateAction(Request $request)
    {
        return $this->batchActiveToggle($request);
    }

    /**
     * Subscribe/unsubscribe recipient from specified notifications.
     *
     * @Roles("ROLE_MASTER_USER")
     *
     * @Route("/{id}/subscribe", methods={ "PUT" })
     * @ApiDoc(
     *  resource="Group",
     *  section="Receivers",
     *  input={
     *     "class"="",
     *      "data"={
     *          "ids"={
     *              "dataType"="Array of recipients ids",
     *              "actualType"="collection",
     *              "subtype"="string",
     *              "required"=true,
     *              "readonly"=true
     *          },
     *          "subscribe"={
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
     *     204="Recipient group successfully subscribed/unsubscribed."
     *  }
     * )
     *
     * @param Request $request A Request instance.
     * @param integer $id      A PersonRecipient entity instance.
     *
     * @return \ApiBundle\Response\ViewInterface
     */
    public function subscribeAction(Request $request, $id)
    {
        return $this->batchSubscriptionToggle($request, $id);
    }
}
