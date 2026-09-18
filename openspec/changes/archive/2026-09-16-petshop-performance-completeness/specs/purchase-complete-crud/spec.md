# Purchase Complete CRUD Specification

## Purpose

Extend the PurchaseController with full edit, update, and destroy capabilities to complete the resource lifecycle alongside existing index, create, show, and store actions.

## Requirements

### Requirement: Purchase Edit

The PurchaseController MUST provide an edit action that returns a view with the purchase and its associated supplier and details for modification.

#### Scenario: User accesses purchase edit

- GIVEN a purchase exists with supplier and details loaded
- WHEN the user navigates to the edit route for that purchase
- THEN the edit view MUST display the purchase data, supplier options, and current line items

### Requirement: Purchase Update

The PurchaseController MUST provide an update action that validates and persists changes to an existing purchase, including its details.

#### Scenario: User updates a purchase

- GIVEN a purchase exists
- WHEN the user submits modified purchase data and items
- THEN the purchase and its details MUST be updated atomically within a transaction
- AND the user MUST be redirected to the purchase index with a success message

#### Scenario: Validation failure on update

- GIVEN invalid purchase data is submitted
- WHEN the update request fails validation
- THEN the user MUST be redirected back with errors and input preserved

### Requirement: Purchase Destroy

The PurchaseController MUST provide a destroy action that removes a purchase and its associated details from the database.

#### Scenario: User deletes a purchase

- GIVEN a purchase exists with associated details
- WHEN the user requests deletion of that purchase
- THEN the purchase and its details MUST be removed within a transaction
- AND the user MUST be redirected to the purchase index with a success message
