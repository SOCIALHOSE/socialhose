Feature: Create authentication token
  As an anonymous user
  I should be able to obtain authentication token in order to make request
  to api

  @db-fixtures
  Scenario:
    I try to create token with proper data

    Given I make POST request to /security/token/create
    """
    {
      "email": "test@email.com",
      "password": "test"
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


  Scenario Outline:
    I try to create token without providing any data .

    Given I make POST request to /security/token/create
    """
    {
      <payload>
    }
    """
    And I got response with code 400
    And it's contains
    """
    {
      "errors": [
        "Credentials not provided."
      ]
    }
    """

  Examples:
    | payload                   |
    | "email": "test@email.com" |
    | "password": "test"        |
    |                           |


  @db-fixtures
  Scenario Outline:
    I try to create token with invalid data.

    Given I make POST request to /security/token/create
    """
    {
      "email": "<email>",
      "password": "<password>"
    }
    """
    And I got response with code 401
    And it's contains
    """
    {
      "errors": [
        "Bad credentials."
      ]
    }
    """

    Examples:
      | email             | password |
      | test@email.com    | invalid  |
      | unknown@mail1.dev | test     |

