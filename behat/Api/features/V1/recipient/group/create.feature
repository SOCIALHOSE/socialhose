Feature: Create recipient group
  As an master
  I should be able to create group of recipients

  @db-fixtures
  Scenario:
    I try to create recipient group.

    Given I authenticated as test@email.com with password test
    When I make POST request to /api/v1/recipients/groups
    """
    {
      "name": "Test Group",
      "description": "some group",
      "active": true,
      "recipients": [],
      "notifications": []
    }
    """
    Then I got successful response
    And it's contains
    """
    @object@
      .entity('UserBundle:GroupRecipient', 'recipient, id')
      .field('name', 'Test Group')
      .field('description', 'some group')
      .field('active', true)
      .field('recipients', [])
      .field('notifications', [])
    """
    And database has entity UserBundle:GroupRecipient
      | name        | Test Group |
      | description | some group |
      | active      | true       |
