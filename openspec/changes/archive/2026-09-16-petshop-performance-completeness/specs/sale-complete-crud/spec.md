# Sale Complete CRUD Specification

## Purpose

Extend the SaleController with full edit, update, and destroy capabilities to complete the resource lifecycle alongside existing index, create, show, and store actions.

## Requirements

### Requirement: Sale Edit

The SaleController MUST provide an edit action that returns a view with the sale and its associated client and details for modification.

#### Scenario: User accesses sale edit

- GIVEN a sale exists with client and details loaded
- WHEN the user navigates to the edit route for that sale
- THEN the edit view MUST display the sale data, client options, and current line items

### Requirement: Sale Update

The SaleController MUST provide an update action that validates and persists changes to an existing sale, including its details and stock adjustments.

#### Scenario: User updates a sale

- GIVEN a sale exists
- WHEN the user submits modified sale data and items
- THEN the sale, details, and stock levels MUST be updated atomically within a transaction
- AND the user MUST be redirected to the sale index with a success message

#### Scenario: Validation failure on update

- GIVEN invalid sale data is submitted
- WHEN the update request fails validation
- THEN the user MUST be redirected back with errors and input preserved

### Requirement: Sale Destroy

The SaleController MUST provide a destroy action that removes a sale and its associated details, restoring stock levels.

#### Scenario: User deletes a sale

- GIVEN a sale exists with associated details and stock impact
- WHEN the user requests deletion of that sale
- THEN the sale and its details MUST be removed within a transaction
- AND stock levels MUST be restored
- AND the user MUST be redirected to the sale index with a success message
