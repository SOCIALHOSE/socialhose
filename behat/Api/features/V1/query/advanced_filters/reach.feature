Feature: Use 'Reach' advanced filter
  As an authenticated user
  I should be able to use 'Reach' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' and use `Reach` advanced filters with value '10000+'.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "reach": "10000+"
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
          field('views', greaterThan(10000), lowerThan(25000))
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
          "filters": {},
          "advancedFilters": {
            "reach": "10000+"
          }
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """
    And database has 1 entity CacheBundle:Query\SimpleQuery
      | id  | 4   |
      | raw | cat |

    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "reach": "10000+"
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
          field('views', greaterThan(10000), lowerThan(25000))
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
          "filters": {},
          "advancedFilters": {
            "reach": "10000+"
          }
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """
    And database has 1 entity CacheBundle:Query\SimpleQuery
      | id  | 4   |
      | raw | cat |
    And database don't has entity CacheBundle:Query\SimpleQuery
      | id  | 5   |
      | raw | cat |

  @db-fixtures
  Scenario:
    I search 'cat' and use `Reach` advanced filters with empty value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "reach": ""
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
    And database don't has entity CacheBundle:Query\SimpleQuery
      | id  | 4   |
      | raw | cat |

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' and use `Reach` advanced filters with invalid value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "reach": "111"
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
    And database don't has entity CacheBundle:Query\SimpleQuery
      | id  | 3   |
      | raw | cat |

