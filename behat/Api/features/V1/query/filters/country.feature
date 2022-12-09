Feature: Use 'Country' filter
  As an authenticated user
  I should be able to use 'Country' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents with US language.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "country": {
          "include": [ "US" ]
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
          field('source', field('country', 'US'))
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
            "country": {
              "include": [ "US" ]
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
    I search 'cat' which appears in documents not with US language.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "country": {
          "exclude": [ "US" ]
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
          not(field('source', field('country', 'US')))
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
            "country": {
              "exclude": [ "US" ]
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
  I search 'cat' which appears in documents with US and RU languages.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "country": {
          "include": [ "US", "RU" ]
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
            field('country', 'US'),
            field('country', 'RU')
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
            "country": {
              "include": [ "US", "RU" ]
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
    I search 'cat' which appears in documents with unknown language.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "country": {
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
    I search 'cat' which appears in documents not with unknown language.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "country": {
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