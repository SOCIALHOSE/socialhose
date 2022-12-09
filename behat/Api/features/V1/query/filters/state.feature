Feature: Use 'State' filter
  As an authenticated user
  I should be able to use 'State' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents from state Arizona.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "state": {
          "include": [ "AZ" ]
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
          field('source', field('state', 'Arizona'))
        )",
        "count": "@integer@.greaterThan(0)",
        "totalCount": "@integer@.greaterThan(0)",
        "page": 1,
        "limit": 100
      },
      "advancedFilters": "@array@",
      "stats": "@object@",
      "meta": {
        "type": "query",
        "status": "synced",
        "search": {
          "query": "cat",
          "filters": {
            "state": {
              "include": [ "AZ" ]
            }
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents not from state Arizona.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "state": {
          "exclude": [ "AZ" ]
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
          not(field('source', field('state', 'Arizona')))
        )",
        "count": "@integer@.greaterThan(0)",
        "totalCount": "@integer@.greaterThan(0)",
        "page": 1,
        "limit": 100
      },
      "advancedFilters": "@array@",
      "stats": "@object@",
      "meta": {
        "type": "query",
        "status": "synced",
        "search": {
          "query": "cat",
          "filters": {
            "state": {
              "exclude": [ "AZ" ]
            }
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  @external-index-fixtures @db-fixtures
  Scenario:
  I search 'cat' which appears in documents not from Louisiana and Maryland.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "state": {
          "include": [ "LA", "MD" ]
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
          field('source', oneOf(
            field('state', 'Louisiana'),
            field('state', 'Maryland')
          ))
        )",
        "count": "@integer@.greaterThan(0)",
        "totalCount": "@integer@.greaterThan(0)",
        "page": 1,
        "limit": 100
      },
      "advancedFilters": "@array@",
      "stats": "@object@",
      "meta": {
        "type": "query",
        "status": "synced",
        "search": {
          "query": "cat",
          "filters": {
            "state": {
              "include": [ "LA", "MD" ]
            }
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  Scenario:
    I search 'cat' which appears in documents from unknown state.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "state": {
          "include": [ "unknown" ]
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
    I search 'cat' which appears in documents not from unknown state.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "state": {
          "exclude": [ "unknown" ]
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