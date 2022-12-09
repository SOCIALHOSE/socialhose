Feature: Use 'Language' filter
  As an authenticated user
  I should be able to use 'Language' for filtering search results

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents with english language.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "language": [ "en" ]
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
          field('language', 'en')
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
            "language": [ "en" ]
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
    I search 'cat' which appears in documents with english and russian languages.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "language": [ "en", "ru" ]
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
            field('language', 'en'),
            field('language', 'ru')
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
            "language": [ "en", "ru" ]
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """

  Scenario:
    I search 'cat' which appears in documents with unknown languages.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "language": [ "unknown" ]
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

  @external-index-fixtures @db-fixtures
  Scenario:
    I search 'cat' which appears in documents with empty language filters.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/query/search
    """
    {
      "query": "cat",
      "page": 1,
      "filters": {
        "language": [  ]
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
          "filters": {
            "language": []
          },
          "advancedFilters": {}
        },
        "sources": [],
        "sourceLists": []
      }
    }
    """