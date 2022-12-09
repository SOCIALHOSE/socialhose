Feature: Get list of notifications
  As an authenticated user
  I should be able to get list of my notifications

  @db-fixtures
  Scenario:
    I get list of my notification's.

    Given I authenticated as test@email.com with password test
    When I make GET request to /api/v1/notifications
    Then I got successful response
    And it's contains
    """
    {
      "notifications": {
        "data": "@array@.every(
          entity('UserBundle:Notification', 'notification_list, schedule, id'),
          field('owner', field('id', 1))
        )",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": "@integer@",
        "limit": "@integer@"
      },
      "meta": {
        "sort": {
          "field": "name",
          "direction": "asc"
        }
      }
    }
    """

    When I make GET request to /api/v1/notifications
      | onlyPublished | true |
    Then I got successful response
    And it's contains
    """
    {
      "notifications": {
        "data": "@array@.every(
            entity('UserBundle:Notification', 'notification_list, schedule, id'),
            field('owner', field('id', 1)),
            field('published', true)
        )",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": "@integer@",
        "limit": "@integer@"
      },
      "meta": {
        "sort": {
          "field": "name",
          "direction": "asc"
        }
      }
    }
    """
