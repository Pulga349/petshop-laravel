# Proposal: PetShop Performance & Completeness v2

## Intent

PetShop Laravel has foundational work complete but accumulated technical debt across query optimization, controller completeness, schema evolution, and test coverage.

## Scope

### In Scope
- Optimize `Product::getStock()` to eliminate N+1 queries
- Add `total` column to purchases table
- Migrate category fields to polymorphic categories table
- Add edit/update/destroy to PurchaseController and SaleController
- Add tier/status/total_spent columns to clients table
- Expand test coverage and optimize ProductController KPI queries
- Dashboard Chart.js trend visualization

### Out of Scope
- New features outside original scope
- User role/permissions beyond current Breeze setup
- Email notifications, invoice generation, or API endpoints

## Capabilities

### New Capabilities
- `category-polymorphic`: Category management as separate table with polymorphic relations
- `purchase-complete-crud`: Full CRUD for purchases including edit, update, destroy
- `sale-complete-crud`: Full CRUD for sales including edit, update, destroy
- `client-tiers`: Persistent client tier system with status and total_spent tracking
- `optimized-stock`: N+1-free stock calculation using database subqueries

### Modified Capabilities
- `product-index`: Query count reduced from O(N) to O(1) via eager loading
- `dashboard`: KPIs now include chart.js trend data and optimized queries

## Approach

Phase 1: Migrations + model updates. Phase 2: Controller completion + stock optimization. Phase 3: Tests + dashboard chart integration. Phase 4: Verification and cleanup.

## Affected Areas

| Area | Impact |
|------|--------|
| `database/migrations/` | New: purchases_total, categories, client_tier |
| `app/Models/Product.php`, `Purchase.php`, `Client.php` | Modified: getStock(), total accessor, tier fields |
| `app/Http/Controllers/PurchaseController.php`, `SaleController.php` | Modified: add edit/update/destroy |
| `app/Http/Controllers/ProductController.php`, `DashboardController.php` | Modified: KPI/chart optimization |
| `resources/views/`, `tests/Feature/`, `database/seeders/` | Modified: views, coverage, seeds |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Migration data loss adding purchases.total | Medium | Backfill, reversible |
| Category polymorphic migration breaks string fields | High | Data transformation with fallback |
| N+1 fix changes getStock() return behavior | Low | Same logic, different implementation |
| Client tier computation PHP→DB change | Medium | Backfill existing data |

## Rollback Plan

Each migration is reversible (down method). Controller changes are additive. Category migration can revert to string fields. If dashboard chart fails, remove Chart.js and revert KPI display.

## Dependencies

- `composer test` must pass before and after each phase
- ALTER TABLE support
- Chart.js available via Vite + TailwindCSS

## Success Criteria

- [ ] Product index loads in ≤25 queries
- [ ] Purchase and Sale controllers respond to all 7 resource routes
- [ ] Category fields reference categories table
- [ ] Client tier persists matching PHP calculation
- [ ] ≥80% coverage on modified controllers
- [ ] Dashboard shows 12-month trend chart
