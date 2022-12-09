Feature: Update category
  As an authenticated user
  I should be able to update any available properties of my category

  @db-fixtures
  Scenario:
    I try to rename 'Test' category.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
    """
    {
      "name": "Awesome Category",
      "parent": 5
    }
    """
    Then I got response with code 200
    And it's contains
    """
    @object@
      .entity('CacheBundle:Category', 'category, feed_tree, id')
      .field('id', 6)
      .field('name', 'Awesome Category')
    """
    And database has entity CacheBundle:Category
     | id     | 6                |
     | name   | Awesome Category |
     | user   | 1                |
     | parent | 5                |

  @db-fixtures
  Scenario:
    I try to move 'Test' category to another category.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
    """
    {
      "name": "Test",
      "parent": 4
    }
    """
    Then I got response with code 200
    And it's contains
    """
    @object@
      .entity('CacheBundle:Category', 'category, feed_tree, id')
      .field('id', 6)
      .field('name', 'Test')
    """
    And database has entity CacheBundle:Category
      | id     | 6    |
      | name   | Test |
      | user   | 1    |
      | parent | 4    |

  Scenario:
    I try to update 'Test' but not provide necessary information.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
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
          "transKey": "updateCategoryNameEmpty",
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
    I try to rename category and set already exists name

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
    """
    {
      "name": "My Content",
      "parent": 5
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "User already have category with name \"My Content\".",
          "transKey": "updateCategoryNameNotUnique",
          "type": "error",
          "parameters": {
              "current": "My Content"
          }
        }
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to update category with unknown id.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/1000
    """
    {
      "name": "Awesome Category",
      "parent": 5
    }
    """
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
    I try to update 'My Content' category which is internal.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/1
    """
    {
      "name": "Awesome category"
    }
    """
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't update internal category."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to update category for another user.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/10
    """
    {
      "name": "Awesome category",
      "parent": 4
    }
    """
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't update category owned by other user."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to move 'Test' category inside it self.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
    """
    {
      "name": "Test",
      "parent": 6
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "Try to place category inside itself.",
          "transKey": "updateCategoryParent",
          "type": "error",
          "parameters": []
        }
      ]
    }
    """
    And database has entity CacheBundle:Category
      | id     | 6    |
      | name   | Test |
      | user   | 1    |
      | parent | 5    |

  @db-fixtures
  Scenario:
    I try to move 'Test' category inside unknown category.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/6
    """
    {
      "name": "Test",
      "parent": 1000
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "This value is not valid.",
          "transKey": "updateCategoryParentInvalid",
          "type": "error",
          "parameters": {
              "current": "1000",
              "available": null
          }
        }
      ]
    }
    """
    And database has entity CacheBundle:Category
      | id     | 6    |
      | name   | Test |
      | user   | 1    |
      | parent | 5    |

  @db-fixtures
  Scenario:
    I try to move 'Sub main sub 3' category inside one of child.

    Given I authenticated as test@email.com with password test
    When I make PUT request to /api/v1/categories/5
    """
    {
      "name": "Sub main sub 3",
      "parent": 6
    }
    """
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        {
          "message": "Try to place category inside it child.",
          "transKey": "updateCategoryParent",
          "type": "error",
          "parameters": []
        }
      ]
    }
    """
    And database has entity CacheBundle:Category
      | id     | 5              |
      | name   | Sub main sub 3 |
      | user   | 1              |
      | parent | 2              |