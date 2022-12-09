Feature: Use 'Source' advanced filter
  As an authenticated user
  I should be able to use 'Source' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents from US and after it add `source`
    advanced filters with value 'CNN'.

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
        "source": "CNN"
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
          field('source',
            field('country', 'US'),
            field('title', 'CNN')
          )
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
            "source": "CNN"
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
        "source": "CNN"
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
          field('source',
            field('country', 'US'),
            field('title', 'CNN')
          )
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
            "source": "CNN"
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
    I search 'cat' which appears in documents from US and after it add `source`
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
        "source": ""
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
          field('source',
            field('country', 'US')
          )
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
            "source": ""
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

  @db-fixtures
  Scenario:
    I search 'cat' and use `Source` advanced filters with unknown value.

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
        "source": "some"
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
          "filters": {
            "country": {
              "include": [ "US" ]
            }
          },
          "advancedFilters": {
            "source": "some"
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


