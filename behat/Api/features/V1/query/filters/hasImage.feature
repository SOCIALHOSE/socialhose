Feature: Use 'Has Image' filter
  As an authenticated user
  I should be able to use 'Has Image' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents which have image.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "hasImage": true
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
          field('image', isNotEmpty())
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
            "hasImage": true
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
    I search 'cat' which appears in documents which is may have images.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "hasImage": false
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
            field('image', isEmpty()),
            field('image', isNotEmpty())
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
            "hasImage": false
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  Scenario Outline:
    I search 'cat' which appears in documents which filtered by hasImage filters
    with invalid value.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "hasImage": <value>
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
    | value  |
    | "some" |
    | 10     |
    | "true" |