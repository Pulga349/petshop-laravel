# Client Tiers Specification

## Purpose

Persist client tier, status, and total_spent fields to replace runtime PHP computation, enabling efficient querying and consistent client categorization.

## Requirements

### Requirement: Client Tier Fields

The clients table MUST include `tier`, `status`, and `total_spent` columns to store client classification and lifetime purchase value.

#### Scenario: Client record with tier data

- GIVEN a client exists
- WHEN the client is created or updated
- THEN the tier, status, and total_spent fields MUST be persisted in the database

### Requirement: Tier Computation Persistence

The system MUST compute and persist client tier and total_spent values upon purchase completion, replacing PHP-based runtime calculation.

#### Scenario: Purchase triggers tier update

- GIVEN a sale is completed
- WHEN the transaction commits
- THEN the associated client's total_spent MUST be updated by the sale amount
- AND the client's tier and status MUST be recalculated and persisted

### Requirement: Client Tier Querying

The system MUST support efficient querying of clients by tier and status without iterating over all records.

#### Scenario: Filter clients by tier

- GIVEN clients with various tier values
- WHEN querying clients of a specific tier
- THEN only clients matching that tier MUST be returned
- AND the query MUST execute without loading all client records into PHP memory
