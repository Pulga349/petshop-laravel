# Archive Report: PetShop Performance & Completeness v2

## Final State

- **Change**: `petshop-performance-completeness`
- **Artifact store**: OpenSpec
- **Archived on**: 2026-09-16
- **Source**: `openspec/changes/petshop-performance-completeness/`
- **Archive destination**: `openspec/changes/archive/2026-09-16-petshop-performance-completeness/`
- **Archive readiness**: `dependencies.archive: ready`; `nextRecommended: archive`
- **Action context**: repo-local; operations stayed within `/home/mrtin/petshop-laravel`

## Task Completion Gate

The persisted `tasks.md` contains 26 completed implementation and verification tasks out of 26 total. No unchecked task entries (`- [ ]`) remain in the archived task artifact. No archive-time checkbox reconciliation was performed.

## Verification Evidence

Final independent verification passed with 19/19 requirements compliant, 23/23 scenarios compliant, and zero validator blockers. Earlier intermediate failure claims are superseded by the latest persisted verification state and the final-state facts supplied at archive launch.

- `composer test`: 99 tests, 924 assertions, exit code 0.
- `npm run build`: exit code 0.
- Product query bound passed.
- Purchase resource routes: 7.
- Sale resource routes: 7.
- Isolated `migrate:fresh` → `migrate:rollback` → `migrate:fresh`: passed.
- Cumulative TDD apply-progress evidence contains a truthful 26-row table.
- Migration fixes include 000006 rollback ownership and legacy premium-field `dropUnique` before dropping `sku`.
- Final scenario remediation added stock-value, purchase atomic-rollback, and legacy-category-preservation tests.

## Non-Blocking Warnings

- Coverage, lint, and type-check commands are not configured.
- Three assertion-quality warnings remain in migration and seeder tests.
- Product KPI totals still materialize scoped products and sum in PHP; the required constant-query behavior and stock exposure pass.
- Legacy supplier `category` compatibility remains alongside polymorphic fields.

No critical verification findings remain.

## Specs Synced

No canonical specs existed under `openspec/specs/` for these domains. Each full change spec was mechanically copied to the source-of-truth location:

| Domain | Action | Details |
|--------|--------|---------|
| `dashboard` | Created | Full spec copied; `diff -r` empty |
| `product-index` | Created | Full spec copied; `diff -r` empty |
| `optimized-stock` | Created | Full spec copied; `diff -r` empty |
| `client-tiers` | Created | Full spec copied; `diff -r` empty |
| `sale-complete-crud` | Created | Full spec copied; `diff -r` empty |
| `purchase-complete-crud` | Created | Full spec copied; `diff -r` empty |
| `category-polymorphic` | Created | Full spec copied; `diff -r` empty |

## Mechanical Copy Evidence

Every copy readback produced empty output. The command invocations below are recorded verbatim:

```text
diff -r openspec/changes/petshop-performance-completeness/specs/dashboard/spec.md openspec/specs/dashboard/.spec.md.4Vub4g
diff -r openspec/changes/petshop-performance-completeness/specs/dashboard/spec.md openspec/specs/dashboard/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/product-index/spec.md openspec/specs/product-index/.spec.md.zOgWn9
diff -r openspec/changes/petshop-performance-completeness/specs/product-index/spec.md openspec/specs/product-index/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/optimized-stock/spec.md openspec/specs/optimized-stock/.spec.md.27V9qT
diff -r openspec/changes/petshop-performance-completeness/specs/optimized-stock/spec.md openspec/specs/optimized-stock/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/client-tiers/spec.md openspec/specs/client-tiers/.spec.md.i1DIq8
diff -r openspec/changes/petshop-performance-completeness/specs/client-tiers/spec.md openspec/specs/client-tiers/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/sale-complete-crud/spec.md openspec/specs/sale-complete-crud/.spec.md.gTjD1p
diff -r openspec/changes/petshop-performance-completeness/specs/sale-complete-crud/spec.md openspec/specs/sale-complete-crud/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/purchase-complete-crud/spec.md openspec/specs/purchase-complete-crud/.spec.md.L1HIyO
diff -r openspec/changes/petshop-performance-completeness/specs/purchase-complete-crud/spec.md openspec/specs/purchase-complete-crud/spec.md
diff -r openspec/changes/petshop-performance-completeness/specs/category-polymorphic/spec.md openspec/specs/category-polymorphic/.spec.md.5KmeN9
diff -r openspec/changes/petshop-performance-completeness/specs/category-polymorphic/spec.md openspec/specs/category-polymorphic/spec.md
```

## Mechanical Archive Move Evidence

The entire change folder was snapshotted before the move, moved with `git mv`, confirmed absent at the source path, and compared against the pre-move snapshot. The mandatory archive readback produced empty output:

```text
diff -r /tmp/sdd-archive.otLFKq/source openspec/changes/archive/2026-09-16-petshop-performance-completeness
```

The archive contains the complete proposal, seven specs, design, tasks, verify report, and this additive archive report. The active change directory is absent.
