@health-check
Feature: Api status
    In order to know the server is up and running
    As a health checker
    I want to check the api status

    Scenario: Check the api status
        Given I send a GET request to /inner/health-check
        Then the response status code should be 200
