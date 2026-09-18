# Design: PetShop Performance & Completeness v2

## Technical Approach

Phase-based rollout: (1) migrations + model updates, (2) controller completion + stock optimization, (3) tests + dashboard chart integration, (4) verification. The project uses Laravel 13, PHP 8.3, Vite/Tailwind/Alpine, and PHPUnit with `RefreshDatabase`. Existing `Route::resource()` declarations already wire edit/update/destroy routes — only controller methods and views are missing.

## Architecture Decisions

### Decision: Polymorphic Category Relations Over Regular FKs

**Choice**: Convert `Product` and `Supplier` category references from `BelongsTo` with plain `category_id` FK to `morphTo` polymorphic relations, with `category_id` + `category_type` columns on both tables.
**Alternatives considered**: Keep existing regular FK columns (migrations 2026_09_15_000003/000004 already created these).
**Rationale**: The spec explicitly requires polymorphic relations enabling unified category management across products and suppliers. Regular FKs cannot support a single `categories` table serving multiple entity types. Existing migrations must be replaced to add `category_type` and migrate the FK column.

### Decision: DB-Persisted Client Tiers Over Runtime PHP Computation

**Choice**: Persist `tier`, `status`, `total_spent` on every sale completion via a `recalculateTier()` method on the `Client` model, triggered from the SaleController store/update flow.
**Alternatives considered**: Keep PHP runtime calculation (current state) or use an observer.
**Rationale**: Spec requires efficient querying by tier without iterating all records. DB-persisted values enable `WHERE tier = ?` queries with existing indexes. The `recalculateTier()` method centralizes tier logic and is testable.

### Decision: `scopeWithStock` for All KPI Aggregation

**Choice**: Replace `Product::all()` + `getStock()` loops in `ProductController::index()` and `DashboardController::index()` with the existing `scopeWithStock` subquery approach.
**Alternatives considered**: Add a `withStock()` eager-loading relationship or use Laravel's `addSelect` with raw expressions.
**Rationale**: `scopeWithStock` already exists on the Product model and produces O(1) stock computation via `selectSub`. Reusing it avoids introducing a new pattern and keeps the existing test (`test_product_scope_with_stock`) valid.

### Decision: Atomic Transactions with Stock Restoration for Sale Deletion

**Choice**: Implement `Sale::destroy()` with a transaction that deletes details, restores stock via `restoreStock()` on Product, and deletes the sale.
**Alternatives considered**: Soft deletes or cascading without stock restoration.
**Rationale**: The spec requires stock levels to be restored on sale deletion. `restoreStock()` is referenced in `ModelTest::test_sale_restore_stock` but not yet implemented — it must be added to the Sale model.

## Data Flow

```
Purchase/Sale Store → DB::transaction → Create details + update stock + recalculate client tier
Purchase/Sale Update → DB::transaction → Validate → Update details + adjust stock → recalculate tier
Purchase/Sale Destroy → DB::transaction → Delete details → restore stock (sales) → delete record
ProductController index → scopeWithStock → single query with stock subqueries → paginate + KPIs
DashboardController → aggregated queries (SUM GROUP BY month) + scopeWithStock for low stock count
```

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `database/migrations/2026_09_15_000003_add_category_id_to_products.php` | Modify | Replace regular FK with polymorphic `category_id` + `category_type` columns |
| `database/migrations/2026_09_15_000004_add_category_id_to_suppliers.php` | Modify | Replace regular FK with polymorphic `category_id` + `category_type` columns |
| `app/Models/Category.php` | Modify | Change `products()`/`suppliers()` from `HasMany` to `morphMany` |
| `app/Models/Product.php` | Modify | Change `category()` from `BelongsTo` to `morphTo`; add `category_type` to fillable |
| `app/Models/Supplier.php` | Modify | Change `category()` from `BelongsTo` to `morphTo`; add `category_type` to fillable |
| `app/Models/Client.php` | Modify | Add `tier`, `status`, `total_spent` to fillable; add `recalculateTier()` method |
| `app/Models/Sale.php` | Modify | Add `restoreStock()` method for stock restoration on delete |
| `app/Http/Controllers/PurchaseController.php` | Modify | Add `edit()`, `update()`, `destroy()` methods |
| `app/Http/Controllers/SaleController.php` | Modify | Add `edit()`, `update()`, `destroy()` methods with stock restoration |
| `app/Http/Controllers/ProductController.php` | Modify | Replace `Product::all()` loop with `scopeWithStock` in `index()` |
| `app/Http/Controllers/DashboardController.php` | Modify | Replace `getStock()` loop with DB-level aggregation; optimize trend queries |
| `resources/views/purchases/edit.blade.php` | Create | Edit form for purchases |
| `resources/views/sales/edit.blade.php` | Create | Edit form for sales |
| `tests/Feature/PurchaseControllerTest.php` | Create | Tests for edit/update/destroy |
| `tests/Feature/SaleControllerTest.php` | Modify | Add edit/update/destroy tests |
| `tests/Feature/DashboardTest.php` | Create | Tests for optimized queries and chart data |

## Interfaces / Contracts

**Client::recalculateTier()**: Recomputes `tier` and `status` from `total_spent` using the existing tier thresholds (Bronze <1000, Silver ≥1000, Gold ≥5000, Platinum ≥10000). Sets `status` to `'active'` if total_spent > 0, otherwise `'inactive'`.

**Sale::restoreStock()**: Iterates `details`, increments each product's `initial_stock` by the detail quantity, and saves. Called within `destroy()` transaction.

**Product::scopeWithStock()**: Existing scope — already returns `stock` as a selected subquery. Used by controller index and dashboard for O(1) stock aggregation.

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Unit | `getStock()`, `scopeWithStock()`, `recalculateTier()`, `restoreStock()` | Instantiate models in `setUp()`, assert return values match expected arithmetic |
| Integration | Purchase/Sale CRUD edit/update/destroy | Use `RefreshDatabase`, assert redirects, database state, and stock restoration |
| Integration | ProductController index query count | `DB::enableQueryLog()` + `assertLessThanOrEqual(25, count(DB::getQueryLog()))` (existing pattern) |
| Integration | Dashboard query count | Assert lowStockProducts computed in O(1) with `assertLessThanOrEqual()` on query log |
| Migration | Schema columns and indexes | Existing `MigrationTest` pattern — verify columns exist via `PRAGMA table_info()` |
| E2E | Full sale flow: create → edit → delete → stock restored | `actingAs($user)`, post data, assert redirect + DB state + product stock |

## Threat Matrix

N/A — no routing, shell, subprocess, VCS/PR automation, executable-file classification, or process-integration boundary.

## Migration / Rollout

1. **Phase 1**: Run category polymorphic migration updates. Since `2026_09_15_000003`/`000004` already ran, new migration must drop and recreate `category_id` as polymorphic. Backfill existing data by mapping string category values to category IDs (pattern from existing migrations).
2. **Phase 2**: Deploy controller changes and model methods. `Route::resource()` already wires edit/update/destroy — no route changes needed.
3. **Phase 3**: Add views and run tests. `composer test` must pass before/after each phase per proposal.
4. **Rollback**: Each migration has `down()`. Controller changes are additive (new methods don't break existing routes). Category polymorphic migration can revert to regular FKs. Dashboard Chart.js can be removed without data loss.

## Open Questions

- [ ] Polymorphic category migration requires dropping existing FK columns — need to verify data integrity during the transition in SQLite (test environment) vs MySQL (production)
- [ ] `Sale::restoreStock()` and `Client::recalculateTier()` are referenced in ModelTest but not yet implemented — must confirm these are in scope for this change
- [ ] The `total` column on purchases is already added by migration but `Purchase::store()` doesn't set it — needs backfill or store logic update
