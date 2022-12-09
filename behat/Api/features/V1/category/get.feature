Feature: Get category
  As an authenticated user
  I should be able get information about specified category

  @db-fixtures
  Scenario:
    I try to get 'My Content' category information.

    Given I authenticated as test@email.com with password test
    When I make GET request to /api/v1/categories/1
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('CacheBundle:Category', 'category, feed_tree, id')
      .field('id', 1)
      .field('name', 'My Content')
    """

  @db-fixtures
  Scenario:
    I try to get category information by unknown id.

    Given I authenticated as test@email.com with password test
    When I make GET request to /api/v1/categories/1000
    Then I got response with code 404
    And it's contains
    """
    {
      "errors": [
        "Can't find Category with id 1000."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to get category owned by other user.

    Given I authenticated as test@email.com with password test
    When I make GET request to /api/v1/categories/9
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't read category owned by other user."
      ]
    }
    """