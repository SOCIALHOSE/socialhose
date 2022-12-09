Feature: Create recipient
  As an master
  I must be able to create new person recipient

  @db-fixtures
  Scenario:
    I try to create new recipient.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/recipients
    """
    {
      "firstName": "Test",
      "lastName": "User",
      "email": "test.user@email.com",
      "active": true,
      "notifications": [],
      "groups": []
    }
    """
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('UserBundle:PersonRecipient', 'recipient, id')
      .field('firstName', 'Test')
      .field('lastName', 'User')
      .field('email', 'test.user@email.com')
      .field('active', true)
      .field('groups', [])
    """
    And database has entity UserBundle:PersonRecipient
      | firstName | Test                |
      | lastName  | User                |
      | email     | test.user@email.com |
      | active    | true                |
