Feature: Use 'Date' filter
  As an authenticated user
  I should be able to use 'Date' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' in documents which found maximum 10 days ago.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "last"
          "days": 10
        }
      }
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "documents": {
        "data": "@array@.every(
          entity('CacheBundle:Document', 'document, id'),
          oneOf(
            field('title', contains('cat', true)),
            field('content', contains('cat', true))
          ),
          field('published', gte('#now().modify(\"- 10 days\").format(\"c\")#'))
        )",
        "count": "@integer@.greaterThan(0)",
        "totalCount": "@integer@.greaterThan(0)",
        "page": 1,
        "limit": 100
      },
      "advancedFilters": "@array@",
      "stats": "@object@"
    }
    """

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' in documents which found between some period.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "between",
          "start": "#now().modify(\"- 30 days\").format(\"Y-m-d\")#",
          "end": "#now().modify(\"- 1 days\").format(\"Y-m-d\")#"
        }
      }
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "documents": {
        "data": "@array@.every(
          entity('CacheBundle:Document', 'document, id'),
          oneOf(
            field('title', contains('cat', true)),
            field('content', contains('cat', true))
          ),
          field('published', between(
            '#now().modify(\"- 30 days\").format(\"c\")#',
            '#now().modify(\"- 1 days\").format(\"c\")#'
          ))
        )",
        "count": "@integer@.greaterThan(0)",
        "totalCount": "@integer@.greaterThan(0)",
        "page": 1,
        "limit": 100
      },
      "advancedFilters": "@array@",
      "stats": "@object@"
    }
    """

  Scenario:
    I search 'cat' in documents which filtered by date filter with invalid type.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "invalid"
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """

  Scenario:
    I search 'cat' in documents which filtered by date filter with 'last' type
    but not provide 'days' field.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "last"
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """

  Scenario Outline:
    I search 'cat' in documents which filtered by date filter with 'last' type
    but provide invalid 'days' values.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "last",
          "days": <value>
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """

  Examples:
    | value     |
    | "invalid" |
    | 0         |
    | -10       |

  Scenario:
    I search 'cat' in documents which filtered by date filter with 'between' type
    but not provide 'start' and 'end' values.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "between"
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """

  Scenario Outline:
    I search 'cat' in documents which filtered by date filter with 'between' type
    but provide invalid 'start' and 'end' values.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "between",
          "start": "<date>",
          "end": "<date>"
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """

    Examples:
      | date       |
      | 2017-13-20 |
      | some       |
      | 2017-01-40 |

  Scenario:
    I search 'cat' in documents which filtered by date filter with 'between' type
    but provide 'start' greater than 'end'.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "date": {
          "type": "between",
          "start": "#now().modify(\"- 15 days\").format(\"Y-m-d\")#",
          "end": "#now().modify(\"- 30 days\").format(\"Y-m-d\")#"
        }
      }
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": "@array@"
    }
    """