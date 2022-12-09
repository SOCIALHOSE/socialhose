Feature: Create category
  As an authenticated user
  I should able to create new category

  @db-fixtures
  Scenario:
    I try to create new category.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories
    """
    {
      "name": "new category",
      "parent": 4
    }
    """
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('CacheBundle:Category', 'category, feed_tree, id')
      .field('name', 'new category')
    """

  Scenario:
    I try to create new category but not provide name.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories
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
          "message": "This value should not be blank.",
          "transKey": "createCategoryNameEmpty",
          "type": "error",
          "parameters": {
              "current": null
          }
        }
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to create new category with already exists name.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories
    """
    {
      "name": "My Content",
      "parent": 4
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "User already have category with name \"My Content\".",
          "transKey": "createCategoryNameNotUnique",
          "type": "error",
          "parameters": {
              "current": "My Content"
          }
        }
      ]
    }
    """