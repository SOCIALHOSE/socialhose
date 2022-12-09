Feature: Use 'Source Section' advanced filter
  As an authenticated user
  I should be able to use 'Source Section' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' and use `Source Section` advanced filters with value 'Lifestyle'.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "sourceSection": "Lifestyle"
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
          field('source', field('section', 'Lifestyle'))
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
            "sourceSection": "Lifestyle"
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
        "sourceSection": "Lifestyle"
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
          field('source', field('section', 'Lifestyle'))
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
            "sourceSection": "Lifestyle"
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
    And don't has entity CacheBundle:Query\SimpleQuery
      | id  | 5   |
      | raw | cat |

  @db-fixtures
  Scenario:
    I search 'cat' and use `Source Section` advanced filters with empty value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "sourceSection": ""
      }
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "documents": {
        "data": "@array@.every(
          entity('CacheBundle:Document', 'document, id')
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
            "sourceSection": ""
          }
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """
    And database has entity CacheBundle:Query\SimpleQuery
      | id  | 4   |
      | raw | cat |

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' and use `Source Section` advanced filters with invalid value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "sourceSection": "some"
      }
    }
    """
    Then I got successful response
    And it's contains
    """
    {
      "documents": {
        "data": [],
        "count": 0,
        "totalCount": 0,
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
            "sourceSection": "some"
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
