Feature: Use 'Headline' filter
  As an authenticated user
  I should be able to use 'Headline' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat'.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {}
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
            field('title', contains('dog', true)),
            field('title', contains('fish', true)),
            field('content', contains('cat', true)),
            field('content', contains('dog', true)),
            field('content', contains('fish', true))
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
          "filters": {},
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat', and also include 'dog' in headline.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "headline": {
          "include": "dog"
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
          field('title',
            contains('cat', true),
            contains('dog', true)
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
            "headline": {
              "include": "dog"
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
    I search 'cat' which not include 'cat' in headline.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "headline": {
          "exclude": "cat"
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
          field('title',
            not(contains('cat', true)),
            contains('some', true)
          ),
          field('content', contains('cat', true))
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
            "headline": {
              "exclude": "cat"
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
    I search 'cat' without 'dog' and 'fish' in headline.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "headline": {
          "exclude": "dog, fish"
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
          field('title',
            not(contains('dog', true)),
            not(contains('fish', true))
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
            "headline": {
              "exclude": "dog, fish"
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
    I search 'cat' with 'dog' but without 'fish' in headline.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "headline": {
          "include": "dog",
          "exclude": "fish"
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
          field('title',
            contains('cat', true),
            contains('dog', true),
            not(contains('fish', true))
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
            "headline": {
              "include": "dog",
              "exclude": "fish"
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
    I search documents 'fish' without 'cat' in headline.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "fish",
      "page": 1,
      "filters": {
        "headline": {
          "exclude": "cat"
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
            field('title', contains('fish', true)),
            field('content', contains('fish', true))
          ),
          field('title', not(contains('cat', true)))
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
          "query": "fish",
          "filters": {
            "headline": {
              "exclude": "cat"
            }
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """