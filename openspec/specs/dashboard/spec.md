# Dashboard Specification

## Delta for Dashboard

## ADDED Requirements

### Requirement: Chart.js Trend Visualization

The dashboard MUST render a 12-month trend chart using Chart.js, displaying sales and purchase data over time for visual performance analysis.

#### Scenario: Dashboard displays trend chart

- GIVEN sales and purchase data exists for the last 12 months
- WHEN the dashboard index page loads
- THEN a Chart.js line chart MUST be rendered showing monthly sales and purchases
- AND the chart MUST include labels for each month and two data series

### Requirement: Optimized Low Stock Query

The low stock product count MUST be computed using a database-level condition rather than iterating all products and calling getStock() in PHP.

(Previously: Product::all()->filter(fn($p) => $p->getStock() <= 0) caused N+1 queries)

#### Scenario: Low stock count without N+1

- GIVEN products exist in the database
- WHEN the dashboard computes lowStockProducts
- THEN the count MUST be derived from a single query using the scopeWithStock or a raw SQL condition
- AND the query count MUST be O(1)

## MODIFIED Requirements

### Requirement: Dashboard KPI Data

The dashboard MUST provide optimized KPI data with reduced query count for all metrics.

(Previously: Individual queries for each month's sales/purchases and getStock() calls in loops)

#### Scenario: Monthly trend data optimized

- GIVEN 12 months of sales and purchase data
- WHEN the dashboard computes trend data
- THEN monthly sales and purchase sums MUST be fetched using grouped queries
- AND the query count MUST be minimized rather than issuing 12+ individual queries

## REMOVED Requirements

### Requirement: Loop-based Low Stock Calculation

The pattern of iterating all products and calling getStock() for low stock counting is removed.

(Reason: This pattern causes N+1 queries and is replaced by database-level aggregation)
(Migration: Replaced by scopeWithStock-based query or raw SQL condition)
