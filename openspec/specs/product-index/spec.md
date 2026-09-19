# Product Index Specification

## Delta for Product Index

## MODIFIED Requirements

### Requirement: Product Index Query Efficiency

The ProductController index action MUST reduce query count from O(N) to O(1) by using eager loading and the scopeWithStock scope, eliminating N+1 queries caused by iterating all products for stock calculation.

(Previously: Product::with('supplier')->paginate(15) followed by Product::all() with getStock() calls in a loop)

#### Scenario: Paginated products with stock data

- GIVEN products exist with suppliers and stock details
- WHEN the index action executes
- THEN products MUST be paginated with supplier data eagerly loaded
- AND stock quantities MUST be available via scopeWithStock in the initial query
- AND the total query count MUST be constant regardless of product count

#### Scenario: KPI aggregation from paginated query

- GIVEN the index action needs totalProducts, outOfStockCount, and totalInventoryValue
- WHEN KPI values are computed
- THEN outOfStockCount and totalInventoryValue MUST be derived from the scoped query results
- AND no separate Product::all() call MUST be made

### Requirement: Eager Loaded Relationships

The index action MUST eager-load all relationships needed for display and KPI computation in a single query.

#### Scenario: Supplier data eager loaded

- GIVEN products with supplier relationships
- WHEN the index action loads products
- THEN supplier data MUST be included in the paginated result
- AND no lazy loading MUST occur during view rendering
