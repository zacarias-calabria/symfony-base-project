@authorization
Feature: SSO sign up
    In order to be able to work with the system
    As a client application
    I want to identify a user

    @not-follow-redirects
    Scenario: Get an URL to authorize a user through Google SSO
        Given I send a GET request to /inner/authorization/signup/google
        Then the response status code should be 302
        And the response should the header Location with the payload
        """
        https://accounts.google.com/o/oauth2/v2/auth?client_id=google-client-id&redirect_uri=http%3A%2F%2Flocalhost%3A3000%2Fsignup%2Fgoogle%2Fcallback&response_type=code&scope=openid%20email%20profile&state=google-state
        """
#        And the response header "Location" should be "https://accounts.google.com/o/oauth2/v2/auth?client_id=google-client-id&redirect_uri=http%3A%2F%2Flocalhost%3A3000%2Fsignup%2Fgoogle%2Fcallback&response_type=code&scope=openid%20email%20profile&state=google-state"
