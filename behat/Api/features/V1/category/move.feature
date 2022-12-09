Feature: Move category
  As an authenticated user
  I should be able to move my category from one place to another

  @db-fixtures
  Scenario:
    I try to move 'Sub main sub 3' category to another category.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/5/move_to/4
    Then I got successful response
    And it's contains
    """
    {
      "data": "@array@
        .every(entity('CacheBundle:Category', 'category_tree, feed_tree, id'))
        .one(
          field('name', 'My Content'),
          field('childes',
            one(
              field('id', 2),
              field('childes',
                one(
                  field('id', 4),
                  field('childes', one(
                    field('id', 5),
                    field('childes', one(field('id', 6)))
                  ))
                )
              )
            )
          )
        )
      ",
      "count": "@integer@.greaterThan(1)",
      "totalCount": "@integer@.greaterThan(1)",
      "page": 1,
      "limit": "@integer@.greaterThan(1)"
    }
    """
    And database has entity CacheBundle:Category
      | id     | 5              |
      | name   | Sub main sub 3 |
      | user   | 1              |
      | parent | 4              |
    And database has entity CacheBundle:Category
      | id     | 6              |
      | name   | Test           |
      | user   | 1              |
      | parent | 5              |

  Scenario:
    I try to move unknown category.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/1000/move_to/7
    Then I got response with code 404
    And it's contains
    """
    {
      "errors": [
        "Can't find category with id 1000."
      ]
    }
    """

  Scenario:
    I try to move my category into unknown.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/5/move_to/1000
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
    I try to move 'My Content' category which is internal.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/1/move_to/7
    Then I got response with code 403
    And it's contains
    """
    {
      "errors": [
        "Can't move internal category."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to move another user category.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/10/move_to/1
    Then I got response with code 404
    And it's contains
    """
    {
      "errors": [
        "Can't find category with id 10."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to move 'Sub main sub 3' category inside it self.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/5/move_to/5
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        "Try to place category inside itself."
      ]
    }
    """
    And database has entity CacheBundle:Category
      | id     | 5              |
      | name   | Sub main sub 3 |
      | user   | 1              |
      | parent | 2              |

  @db-fixtures
  Scenario:
    I try to move 'Sub main sub 3' category inside one of child.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/categories/5/move_to/6
    Then I got response with code 400
    And it's contains
    """
    {
      "errors": [
        "Try to place category inside it child."
      ]
    }
    """
    And database has entity CacheBundle:Category
      | id     | 5              |
      | name   | Sub main sub 3 |
      | user   | 1              |
      | parent | 2              |