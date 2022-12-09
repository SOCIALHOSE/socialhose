Feature: Use 'Article Date' advanced filter
  As an authenticated user
  I should be able to use 'Article Date' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents from US and use `Article Date`
    advanced filters with value '31 Days'.

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
      },
      "advancedFilters": {
        "articleDate": "31 Days"
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
          field('source', field('country', 'US')),
          field('published', gte('#now().modify(\"- 31 Days\").format(\"c\")#'))
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
          "advancedFilters": {
            "articleDate": "31 Days"
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
      "filters": {
        "country": {
          "include": [ "US" ]
        }
      },
      "advancedFilters": {
        "articleDate": "31 Days"
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
          field('source', field('country', 'US')),
          field('published', gte('#now().modify(\"- 31 Days\").format(\"c\")#'))
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
          "advancedFilters": {
            "articleDate": "31 Days"
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
    I search 'cat' which appears in documents from US and also use `Article Date`
    advanced filters with empty value.

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
      },
      "advancedFilters": {
        "articleDate": ""
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
    I search 'cat' which appears in documents from US and use `Article Date`
    advanced filters with invalid value.

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
      },
      "advancedFilters": {
        "articleDate": "111"
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


