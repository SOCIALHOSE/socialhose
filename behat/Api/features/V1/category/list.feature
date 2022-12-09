Feature: Get list of categories
  As an authenticated
  I should be able to get list of my categories

  @db-fixtures
  Scenario:
    I try to get list of categories.

    Given I authenticated as test@email.com with password test
    When I make GET request to /api/v1/categories
    Then I got successful response
    And it's contains
    """
    {
      "data": "@array@
        .every(entity('CacheBundle:Category', 'category_tree, feed_tree, id'))
        .one(field('name', 'My Content'))
        .one(field('name', 'Deleted Content'))
      ",
      "count": "@integer@.greaterThan(1)",
      "totalCount": "@integer@.greaterThan(1)",
      "page": 1,
      "limit": "@integer@.greaterThan(1)"
    }
    """
