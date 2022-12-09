Feature: Use 'Publisher' advanced filter
  As an authenticated user
  I should be able to use 'Publisher' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' and use `Publisher` advanced filters with value 'msnbc'.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "publisher": "msnbc"
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
          field('publisher', contains('msnbc', true))
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
            "publisher": "msnbc"
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
        "publisher": "msnbc"
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
          field('publisher', contains('msnbc', true))
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
            "publisher": "msnbc"
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
    I search 'cat' and use `Publisher` advanced filters with empty value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "publisher": ""
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
            "publisher": ""
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
    I search 'cat' and use `Publisher` advanced filters with invalid value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "advancedFilters": {
        "publisher": "some"
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
            "publisher": "some"
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

