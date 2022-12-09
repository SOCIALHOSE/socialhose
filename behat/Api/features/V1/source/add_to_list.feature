Feature: Add sources to list
  As an authenticated user
  I should be able to place sources in my lists

  @db-fixtures @source-index-fixtures
  Scenario:
    I make empty request.

    Given I authenticated as test@email.com with password test
    And I make POST request to /api/v1/source-index/add-to-sources-list
    """
    {
      "sources": [ 1, 2 ],
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

    And has entity CacheBundle:SourceToSourceList
      | source | 2 |
      | list   | 1 |
    And has entity CacheBundle:SourceToSourceList
      | source | 2 |
      | list   | 3 |
    And has entity CacheBundle:SourceToSourceList
      | source | 2 |
      | list   | 5 |
    And has entity CacheBundle:SourceToSourceList
      | source | 2 |
      | list   | 7 |
    And has entity CacheBundle:SourceToSourceList
      | source | 2 |
      | list   | 9 |
    And has entity CacheBundle:SourceToSourceList
      | source | 2  |
      | list   | 11 |

    And I wait 1000 milliseconds

    # Check index.
    And source index has 2 documents
      | _id      | in | 1, 2              |
      | list_ids | in | 1, 3, 5, 7, 9, 11 |