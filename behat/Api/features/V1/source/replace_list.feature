Feature: Replace used lists for source
  As an authenticated user
  I should be able to replace used lists for specified source

  @db-fixtures @source-index-fixtures
  Scenario:
    I replace lists for one of source.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/1/list
    """
    {
      "sourceLists": [ 1, 3, 5, 7, 9, 11 ]
    }
    """
    Then I got response with code 204

    # Check database.
    And database has entity CacheBundle:SourceToSourceList
      | source | 1 |
      | list   | 1 |
    And has entity CacheBundle:SourceToSourceList
      | source | 1 |
      | list   | 3 |
    And has entity CacheBundle:SourceToSourceList
      | source | 1 |
      | list   | 5 |
    And has entity CacheBundle:SourceToSourceList
      | source | 1 |
      | list   | 7 |
    And has entity CacheBundle:SourceToSourceList
      | source | 1 |
      | list   | 9 |
    And has entity CacheBundle:SourceToSourceList
      | source | 1  |
      | list   | 11 |

    And I wait 1000 milliseconds

    # Check index.
    And source index has 1 documents
      | _id      | in | 1                 |
      | list_ids | in | 1, 3, 5, 7, 9, 11 |


  @db-fixtures @source-index-fixtures
  Scenario:
    I replace lists for unknown source.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/10000/list
    """
    {
      "sourceLists": [ 1, 3, 5, 7, 9, 11 ]
    }
    """
    Then I got response with code 404
    And it's contains
    """
    {
      "errors": [
        {
          "message": "Can't find source with id 10000",
          "transKey": "replaceSourceUnknown",
          "type": "error",
          "parameters": {
            "current": "10000"
          }
        }
      ]
    }
    """

  @db-fixtures @source-index-fixtures
  Scenario:
    I make empty request.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/1/list
    """
    {
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "sourceLists: This value should not be empty.",
          "transKey": "replaceSourceListsEmpty",
          "type": "error",
          "parameters": {
            "current": null
          }
        }
      ]
    }
    """

  @db-fixtures @source-index-fixtures
  Scenario Outline:
    I replace lists for unknown or not owned lists.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/1/list
    """
    {
      "sourceLists": [ <list_id> ]
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "sourceLists: This value is invalid.",
          "transKey": "replaceSourceListInvalid",
          "type": "error",
          "parameters": {
            "current": [ <list_id> ],
            "invalid": [ <list_id> ]
          }
        }
      ]
    }
    """

  Examples:
    | list_id |
    | 1000    |
    | 2       |