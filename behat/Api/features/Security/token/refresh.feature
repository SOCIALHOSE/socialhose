Feature: Refresh authentication token
  As an authenticated user
  I should be able to obtain authentication token by using my refresh token

  @db-fixtures
  Scenario:
    I try to refresh authentication token.

    Given I make POST request to /security/token/refresh
    """
    {
      "refreshToken": "user1_token"
    }
    """
    And I got response with code 200
    And it's contains
    """
    {
      "user": "@object@
        .entity('UserBundle:User', 'user, id, recipient, restrictions')
        .field('firstName', 'John')
        .field('lastName', 'Smith')
      ",
      "token": "@string@",
      "refreshToken": "@string@"
    }
    """


  Scenario:
    I try to refresh authentication token without refresh token provided.

    Given I make POST request to /security/token/refresh
    """
    {
    }
    """
    And I got response with code 400
    And it's contains
    """
    {
      "errors": [
        "refreshToken: This value should not be null."
      ]
    }
    """

  @db-fixtures
  Scenario:
    I try to refresh authentication token by invalid refresh token.

    Given I make POST request to /security/token/refresh
    """
    {
      "refreshToken": "some token"
    }
    """
    And I got response with code 401
    And it's contains
    """
    {
      "errors": [
        "Refresh token \"some token\" does not exist."
      ]
    }
    """

