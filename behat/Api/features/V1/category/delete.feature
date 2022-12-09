Feature: Delete category
  As an authenticated user
  I should be able to delete my category

  @db-fixtures
  Scenario:
    I try to delete 'Test' category.

    Given I authenticated as test@email.com with password test
    When I make DELETE request to /api/v1/categories/6
    Then I got response with code 204
    And it's empty
    And database don't has entity CacheBundle:Category
      | id | 6 |

  @db-fixtures
  Scenario:
    I try to delete 'Sub main sub 3' category which have subdirectories.

    Given I authenticated as test@email.com with password test
    When I make DELETE request to /api/v1/categories/5
    Then I got response with code 204
    And it's empty
    And database don't has entity CacheBundle:Category
      | id | 5 |
    And don't has entity CacheBundle:Category
      | id | 6 |

  @db-fixtures
  Scenario:
    I try to delete category with unknown id.

    Given I authenticated as test@email.com with password test
    When I make DELETE request to /api/v1/categories/1000
    Then I got response with code 404
    And it's contains
    """
    {
      "errors": [
        "Can't find category with id 1000."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to delete category 'My Content' category.

    Given I authenticated as test@email.com with password test
    When I make DELETE request to /api/v1/categories/1
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't delete internal category."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to delete category for another user.

    Given I authenticated as test@email.com with password test
    When I make DELETE request to /api/v1/categories/10
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't delete category owned by other user."
      ]
    }
    """