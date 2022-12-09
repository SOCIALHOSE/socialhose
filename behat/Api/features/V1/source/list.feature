Feature: Get list of sources
  As an authenticated user
  I should be able to get list of sources

  @db-fixtures @source-index-fixtures
  Scenario:
    I make empty request.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/
    """
    {
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "sources": {
        "data": "@array@",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": 1,
        "limit": 20
      },
      "filters": "@object@"
    }
    """

  @db-fixtures @source-index-fixtures
  Scenario:
    I search for source with 'CNN' in title or url.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/
    """
    {
      "query": "CNN"
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "sources": {
        "data": "@array@
          .every(field('title', contains('CNN')))
        ",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": 1,
        "limit": 20
      },
      "filters": "@object@"
    }
    """

  @db-fixtures @source-index-fixtures
  Scenario Outline:
    I should be able to get more or less source per page.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/
    """
    {
      "limit": <limit>
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "sources": {
        "data": "@array@",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": 1,
        "limit": <limit>
      },
      "filters": "@object@"
    }
    """

  Examples:
    | limit |
    | 10    |
    | 1     |
    | 200   |

  @db-fixtures @source-index-fixtures
  Scenario Outline:
    I should'nt be able change requested page.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/
    """
    {
      "page": <page>
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "sources": {
        "data": "@array@",
        "count": "@integer@",
        "totalCount": "@integer@",
        "page": <page>,
        "limit": 20
      },
      "filters": "@object@"
    }
    """

    Examples:
      | page |
      | 2    |
      | 3    |
      | 20   |
