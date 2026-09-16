# Tasks: PetShop Performance & Completeness v2

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~800-900 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 → DB/Migration + Model, PR 2 → Controllers + Views, PR 3 → Tests + Dashboard |
| Delivery strategy | auto-chain |
| Chain strategy | feature-branch-chain |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Focused test command | Runtime harness | Rollback boundary |
|------|------|-----------|---------------------|-----------------|-------------------|
| 1 | DB/Migration + Model updates | PR 1 | `php artisan migrate:fresh --seed && php artisan test --filter=MigrationTest` | Fresh SQLite DB with seeders | Each migration has reversible `down()` |
| 2 | Controller completion + views | PR 2 | `php artisan test --filter=PurchaseControllerTest` | Browser/POST requests via `actingAs()` | Additive controller methods only |
| 3 | Tests + Dashboard + optimization | PR 3 | `composer test` | Full test suite | Can remove Chart.js and revert KPI display |

PR #1 base = feature/tracker branch; PR #2 base = PR #1 branch; PR #3 base = PR #2 branch.

## Phase 1: DB Migrations + Model Updates

- [x] 1.1 Modify `database/migrations/2026_09_15_000003_add_category_id_to_products.php` to drop regular `category_id` FK and add polymorphic `category_id` + `category_type` columns with data backfill
- [x] 1.2 Modify `database/migrations/2026_09_15_000004_add_category_id_to_suppliers.php` to drop regular `category_id` FK and add polymorphic `category_id` + `category_type` columns with data backfill
- [x] 1.3 Update `app/Models/Category.php`: change `products()` and `suppliers()` from `HasMany` to `morphMany`
- [x] 1.4 Update `app/Models/Product.php`: change `category()` from `BelongsTo` to `morphTo`; add `category_type` to `$fillable`; verify `getStock()` and `scopeWithStock()` remain intact
- [x] 1.5 Update `app/Models/Supplier.php`: change `category()` from `BelongsTo` to `morphTo`; add `category_type` to `$fillable`
- [x] 1.6 Update `app/Models/Client.php`: add `tier`, `status`, `total_spent` to `$fillable`; add `recalculateTier()` method implementing Bronze/Silver/Gold/Platinum thresholds
- [x] 1.7 Update `app/Models/Sale.php`: add `restoreStock()` method that iterates details and increments each product's `initial_stock` by detail quantity
- [x] 1.8 Update `app/Models/Purchase.php`: add `total` to `$fillable`; ensure `store()` logic sets `total` on purchase creation
- [x] 1.9 Write RED test for `Client::recalculateTier()` (unit test: assert tier/status match thresholds) — then implement

## Phase 2: Controller Completion

- [ ] 2.1 Add `PurchaseController::edit()`, `update()`, `destroy()` with DB::transaction, including purchase detail management and `total` column update
- [ ] 2.2 Add `SaleController::edit()`, `update()`, `destroy()` with DB::transaction, including stock restoration via `restoreStock()` on delete and `recalculateTier()` on update
- [ ] 2.3 Create `resources/views/purchases/edit.blade.php` with form for purchase date, supplier, and line items
- [ ] 2.4 Create `resources/views/sales/edit.blade.php` with form for sale date, client, and line items
- [ ] 2.5 Optimize `ProductController::index()`: replace `Product::all()` + `getStock()` loop with `scopeWithStock` for O(1) KPI computation
- [ ] 2.6 Optimize `DashboardController::index()`: replace `Product::all()` + `getStock()` filter loop with `scopeWithStock` subquery for `lowStockProducts`; group monthly sales/purchases into single `GROUP BY` queries

## Phase 3: Tests + Dashboard Chart

- [ ] 3.1 Create `tests/Feature/PurchaseControllerTest.php` with RED tests for edit/update/destroy (assert redirects, DB state, transaction rollback) — then implement
- [ ] 3.2 Modify `tests/Feature/SaleControllerTest.php` to add RED tests for edit/update/destroy including stock restoration assertion — then implement
- [ ] 3.3 Create `tests/Feature/DashboardTest.php` with RED tests: query count ≤25 for index, Chart.js data structure validation, lowStockProducts computed in O(1)
- [ ] 3.4 Create `resources/views/dashboard` Chart.js 12-month trend line chart using `$salesData`/`$purchasesData`/`$months` variables
- [ ] 3.5 Update `database/seeders` to ensure category polymorphic data and client tier data are seeded correctly
- [ ] 3.6 Write RED test for `Sale::restoreStock()` (unit test: assert product stock increments) — then implement

## Phase 4: Verification

- [ ] 4.1 Run `composer test` full suite; verify ≥80% coverage on modified controllers
- [ ] 4.2 Verify Product index query count ≤25 with `DB::enableQueryLog()` assertion
- [ ] 4.3 Verify all 7 resource routes respond for PurchaseController and SaleController
- [ ] 4.4 Run `php artisan migrate:rollback` then `migrate:fresh` to confirm reversible migrations
- [ ] 4.5 Remove any temporary debug code; finalize Chart.js view
