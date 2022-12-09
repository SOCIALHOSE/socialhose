Feature:
  Application should fetch document for stored queries in background.
  So we must have command that do it.

  @db-fixtures @external-index-fixtures
  Scenario:
    Fetch document for stored queries.


    Given I run command socialhose:stored_query:fetch

    Then command returned 0 exit code

    # Check entities in database.
    And database has entity CacheBundle:Query\StoredQuery
      | raw    | cat    |
      | status | synced |
    And has entity CacheBundle:Document
      | title | About cat |
    And don't has entity AppBundle:FetchJob
      | id | 1 |

    When I wait 1000 milliseconds until documents was indexed
    Then internal index has 1 document
      | query | eq | #getStoredQuery({'raw': 'cat', 'status': 'synced'}).getId()# |

    # After add new document.
    Given has new document in external index
      | sequence   | 2                       |
      | title      | New cat article         |
      | date_found | #date().getTimestamp()# |

    When I run command socialhose:stored_query:update
    Then command returned 0 exit code

    # Check entities in database.
    And database has entity CacheBundle:Query\StoredQuery
      | raw    | cat    |
      | status | synced |
    And has entity CacheBundle:Document
      | title | About cat |
    And has entity CacheBundle:Document
      | title | New cat article |

    When I wait 1000 milliseconds until documents was indexed
    Then internal index has 2 document
      | query | eq | #getStoredQuery({'raw': 'cat', 'status': 'synced'}).getId()# |

    # Add third document.
    Given has new document in external index
      | sequence   | 3                       |
      | title      | About dogs              |
      | date_found | #date().getTimestamp()# |

    When I run command socialhose:stored_query:update
    Then command returned 0 exit code

    # Check entities in database.
    And has entity CacheBundle:Document
      | title | About cat |
    And has entity CacheBundle:Document
      | title | New cat article |
    But don't has entity CacheBundle:Document
      | title | About dogs |

    When I wait 1000 milliseconds until documents was indexed
    Then internal index has 2 document
      | query | eq | #getStoredQuery({'raw': 'cat', 'status': 'synced'}).getId()# |