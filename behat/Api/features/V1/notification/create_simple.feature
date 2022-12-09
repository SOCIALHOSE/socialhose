Feature: Create simple notification
  As an authenticated user
  I should be able to create new simple notification

  @db-fixtures
  Scenario:
    I try to create new simple notification.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/notifications
    """
    {
      "name": "New notification",
      "recipients": [ 1 ],
      "notificationType": "alert",
      "themeType": "plain",
      "theme": 1,
      "subject": "Some subject",
      "published": true,
      "automatedSubject": false,
      "allowUnsubscribe": true,
      "unsubscribeNotification": true,
      "sources": [
        {
          "type": "feed",
          "id": 1
        }
      ],
      "sendWhenEmpty": false,
      "timezone": "Asia/Novosibirsk",
      "automatic": [
        {
          "type": "daily",
          "time": "15m",
          "days": "all"
        },
        {
          "type": "weekly",
          "period": "third",
          "day": "monday",
          "hour": 11,
          "minute": 45
        },
        {
          "type": "monthly",
          "day": 3,
          "hour": 11,
          "minute": 0
        },
        {
          "type": "monthly",
          "day": "last",
          "hour": 0,
          "minute": 55
        }
      ],
      "sendUntil": "2017-10-01",
      "plainDiff": {},
      "enhancedDiff": {}
    }
    """
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('UserBundle:Notification', 'notification, schedule, id')
      .field('type', 'alert')
      .field('name', 'New notification')
      .field('subject', 'Some subject')
      .field('owner', field('id', 1))
      .field('sources',
        count(1),
        one(field('type', 'feed'), field('id', 1), field('name', 'test1'))
      )
    """
    And database has entity UserBundle:Notification\Notification
      | name                    | New notification |
      | notificationType        | alert            |
      | owner                   | 1                |
      | subject                 | Some subject     |
      | automatedSubject        | false            |
      | published               | true             |
      | allowUnsubscribe        | true             |
      | unsubscribeNotification | true             |
      | sendWhenEmpty           | false            |
      | timezone                | Asia/Novosibirsk |
      | sendUntil               | 2017-10-01       |
      | active                  | true             |

  @db-fixtures
  Scenario:
    I try to create new simple notification without sources, recipients and scheduling.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/notifications
    """
    {
      "name": "New notification",
      "recipients": [],
      "notificationType": "alert",
      "themeType": "plain",
      "theme": 1,
      "subject": "Some subject",
      "published": true,
      "automatedSubject": false,
      "allowUnsubscribe": true,
      "unsubscribeNotification": true,
      "sources": [],
      "sendWhenEmpty": false,
      "timezone": "Asia/Novosibirsk",
      "automatic": [],
      "sendUntil": "2017-10-01",
      "plainDiff": {},
      "enhancedDiff": {}
    }
    """
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('UserBundle:Notification', 'notification, schedule, id')
      .field('type', 'alert')
      .field('name', 'New notification')
      .field('subject', 'Some subject')
      .field('owner', field('id', 1))
      .field('recipients', count(0))
      .field('sources', count(0))
      .field('automatic', count(0))
    """
    And database has entity UserBundle:Notification\Notification
      | name                    | New notification |
      | notificationType        | alert            |
      | owner                   | 1                |
      | subject                 | Some subject     |
      | automatedSubject        | false            |
      | published               | true             |
      | allowUnsubscribe        | true             |
      | unsubscribeNotification | true             |
      | sendWhenEmpty           | false            |
      | timezone                | Asia/Novosibirsk |
      | sendUntil               | 2017-10-01       |
      | active                  | true             |

  @db-fixtures
  Scenario:
    I try to create new simple notification with invalid recipient.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/notifications
    """
    {
      "name": "New notification",
      "recipients": [ 1000 ],
      "notificationType": "alert",
      "themeType": "plain",
      "theme": 1,
      "subject": "Some subject",
      "published": true,
      "automatedSubject": false,
      "allowUnsubscribe": true,
      "unsubscribeNotification": true,
      "sources": [],
      "sendWhenEmpty": false,
      "timezone": "Asia/Novosibirsk",
      "automatic": [],
      "sendUntil": "2017-10-01",
      "plainDiff": {},
      "enhancedDiff": {}
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "This value is not valid.",
          "transKey": "createNotificationRecipientsInvalid",
          "type": "error",
          "parameters": {
            "current": [ 1000 ],
            "available": null
          }
        }
      ]
    }
    """
    And database don't has entity UserBundle:Notification\Notification
      | name                    | New notification |
      | notificationType        | alert            |
      | owner                   | 1                |
      | subject                 | Some subject     |
      | automatedSubject        | false            |
      | published               | true             |
      | allowUnsubscribe        | true             |
      | unsubscribeNotification | true             |
      | sendWhenEmpty           | false            |
      | timezone                | Asia/Novosibirsk |
      | sendUntil               | 2017-10-01       |
      | active                  | true             |

  @db-fixtures
  Scenario Outline:
    I try to create new simple notification with invalid source.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/notifications
    """
    {
      "name": "New notification",
      "recipients": [],
      "notificationType": "alert",
      "themeType": "plain",
      "theme": 1,
      "subject": "Some subject",
      "published": true,
      "automatedSubject": false,
      "allowUnsubscribe": true,
      "unsubscribeNotification": true,
      "sources": [
        {
          <payload>
        }
      ],
      "sendWhenEmpty": false,
      "timezone": "Asia/Novosibirsk",
      "automatic": [],
      "sendUntil": "2017-10-01",
      "plainDiff": {},
      "enhancedDiff": {}
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@
        .one(
          field('message', '<message>'),
          field('transKey', '<transKey>'),
          field('type', 'error')
        )
      "
    }
    """
    And database don't has entity UserBundle:Notification\Notification
      | name                    | New notification |
      | notificationType        | alert            |
      | owner                   | 1                |
      | subject                 | Some subject     |
      | automatedSubject        | false            |
      | published               | true             |
      | allowUnsubscribe        | true             |
      | unsubscribeNotification | true             |
      | sendWhenEmpty           | false            |
      | timezone                | Asia/Novosibirsk |
      | sendUntil               | 2017-10-01       |
      | active                  | true             |

  Examples:
    | payload                    | message                         | transKey                             |
    |                            | Some of sources has invalid id. | createNotificationSources            |
    | "type": "feed"             | This value should not be blank. | createNotificationSourcesIdEmpty     |
    | "id": 1                    | This value should not be blank. | createNotificationSourcesTypeEmpty   |
    | "type": "some", "id": 1    | This value is not valid.        | createNotificationSourcesTypeInvalid |
    | "type": "feed", "id": 1000 | Some of sources has invalid id. | createNotificationSources            |
