# Optimized Stock Specification

## Delta for Optimized Stock

## MODIFIED Requirements

### Requirement: Stock Calculation for KPI Queries

ProductController index KPI queries MUST use database subqueries or aggregated queries to calculate stock levels without N+1 patterns, replacing the current loop-based getStock() calls.

(Previously: index() called Product::all() and iterated each product calling getStock(), resulting in O(N) queries)

#### Scenario: KPI calculation without N+1

- GIVEN products exist in the database
- WHEN the index action computes KPIs
- THEN outOfStockCount and totalInventoryValue MUST be calculated using a single aggregated query or scopeWithStock
- AND the number of queries MUST remain O(1) regardless of product count

#### Scenario: ScopeWithStock is utilized

- GIVEN the scopeWithStock scope exists on the Product model
- WHEN KPI calculations need stock data
- THEN the scope MUST be used to fetch stock data in the initial query
- AND no additional per-product queries MUST be executed
