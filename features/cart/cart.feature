@cart
Feature: Shopping cart
  In order to make a purchase in the shop
  As an anonymous user
  I want to interact with my shopping cart

  @create
  Scenario: Create a new card
    Given I send a PUT request to /cart/80e92311-6ab7-4653-90f0-93a85c63e4b3
    Then the response status code should be 201

  @create
  Scenario: Create a new card with an existing id
    Given I send a PUT request to /cart/243bc5c3-6eac-401f-980c-e68edb59ee19
    When I send a PUT request to /cart/243bc5c3-6eac-401f-980c-e68edb59ee19
    Then the response status code should be 400

  @get
  Scenario: Get a not existing cart
    Given I send a GET request to /cart/6279cbaf-4ac4-4a38-8c6a-9275dccf7c6c
    Then the response status code should be 404

  @get
  Scenario: Get an existing cart
    Given I send a PUT request to /cart/8e3a6cfc-cc86-4534-b364-ae2ea291a865
    When I send a GET request to /cart/8e3a6cfc-cc86-4534-b364-ae2ea291a865
    Then the response status code should be 200

  @add-product
  Scenario: Add a product to non existing cart
    Given I send a POST request to /cart/e644ac8e-6dc3-4d51-bd26-e0fdd31c1737/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
    Then the response status code should be 404

  @add-product
  Scenario: Add a not existing product to cart
    Given I send a PUT request to /cart/0eafaa15-38f7-490b-8c6d-b686d78b808d
    When I send a POST request to /cart/0eafaa15-38f7-490b-8c6d-b686d78b808d/products/ with body:
    """
    {
      "productId": "eccd46d1-f9ad-40e3-af9a-3fe168782f75",
      "quantity": 1
    }
    """
    Then the response status code should be 404

  @add-product
  Scenario: Add a product item to cart
    Given I send a PUT request to /cart/64fde081-0500-424d-8066-7e6285f753c1
    When I send a POST request to /cart/64fde081-0500-424d-8066-7e6285f753c1/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
    Then the response status code should be 201

  @update-product
  Scenario: Try to update a product from not existing cart
    Given I send a PATCH request to /cart/f4b861e3-1a7c-4313-8986-692bf8a7806e/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 4
    }
    """
    Then the response status code should be 404

  @update-product
  Scenario: Try to update a non existing product from the cart
    Given I send a PUT request to /cart/a094d968-c0ab-43e4-818c-a8ff0f4a19f4
    And I send a POST request to /cart/a094d968-c0ab-43e4-818c-a8ff0f4a19f4/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
    Given I send a PATCH request to /cart/a094d968-c0ab-43e4-818c-a8ff0f4a19f4/products/ with body:
    """
    {
      "productId": "7e888b32-88e9-4a51-a642-287dfabd12c4",
      "quantity": 4
    }
    """
    Then the response status code should be 404

  @update-product
  Scenario: Update a product from the cart
    Given I send a PUT request to /cart/64fde081-0500-424d-8066-7e6285f753c1
    And I send a POST request to /cart/64fde081-0500-424d-8066-7e6285f753c1/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
    When I send a PATCH request to /cart/64fde081-0500-424d-8066-7e6285f753c1/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 4
    }
    """
    Then the response status code should be 204

@delete-product
Scenario: Try to remove a product from not existing cart
  Given I send a DELETE request to /cart/be8c0fbc-7fc0-4cd6-a637-af99301d2f43/product/16091ab4-ed0d-4448-abb5-152c250931d6
  Then the response status code should be 404

@delete-product
Scenario: Try to remove a non existing product from the cart
  Given I send a PUT request to /cart/150870a7-bec0-4f79-80fc-e07bad022ed6
  And I send a POST request to /cart/150870a7-bec0-4f79-80fc-e07bad022ed6/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
  When I send a DELETE request to /cart/e6129283-7713-49ec-a082-75780f66e089/product/16091ab4-ed0d-4448-abb5-152c250931d6
  Then the response status code should be 404

@delete-product
Scenario: Remove a product from the cart
  Given I send a PUT request to /cart/e6129283-7713-49ec-a082-75780f66e089
  And I send a POST request to /cart/e6129283-7713-49ec-a082-75780f66e089/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
  When I send a DELETE request to /cart/e6129283-7713-49ec-a082-75780f66e089/product/3425007e-a3d9-4639-8e8c-8ff4b2d90fd0
  Then the response status code should be 204

@process-cart
Scenario: Process a empty cart
  Given I send a PUT request to /cart/64314341-6743-4c62-a105-31d186c9be85
  When I send a POST request to /cart/64314341-6743-4c62-a105-31d186c9be85/pay
  Then the response status code should be 400

@process-cart
Scenario: Process a cart
  Given I send a PUT request to /cart/7eac9790-b5e9-480d-98bc-792132a01ced
  And I send a POST request to /cart/7eac9790-b5e9-480d-98bc-792132a01ced/products/ with body:
    """
    {
      "productId": "3425007e-a3d9-4639-8e8c-8ff4b2d90fd0",
      "quantity": 1
    }
    """
  When I send a POST request to /cart/7eac9790-b5e9-480d-98bc-792132a01ced/pay
  Then the response status code should be 204
