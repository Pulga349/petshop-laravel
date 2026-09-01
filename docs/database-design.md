# Diseño de Base de Datos — PetShop Laravel

Base de datos: `sistema_gestion` (MySQL) / `:memory:` (SQLite en tests)

---

## Diagrama Entidad-Relación

```
┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│   clients    │     ┌▼─   products      │     │   suppliers      │
│──────────────│     │───────────────────│     │──────────────────│
│ PK id        │     │ PK id             │     │ PK id            │
│    name      │     │    name           │     │    name          │
│    email     │◄────│    sku            │     │    contact_person│
│    phone     │     │    category       │     │    category      │◄── enum
│    address   │     │    description    │     │    email         │
│    created_at│     │    image          │     │    phone         │
│    updated_at│     │    sale_price     │     │    address       │
└──────┬───────┘     │    purchase_price │     │    logo          │
       │             │    initial_stock  │     │    status        │◄── enum
       │             │    supplier_id ───┼─────┤    created_at    │
       │             │    created_at     │     │    updated_at    │
       │             │    updated_at     │     └──────────────────┘
       │             └───────────────────┘
       │                       │
       │                       │
       ▼                       ▼
┌──────────────┐     ┌──────────────────┐     ┌──────────────────┐
│    sales     │     │  sale_details    │     │  purchase_details│
│──────────────│     │──────────────────│     │──────────────────│
│ PK id        │     │ PK id            │     │ PK id            │
│    date      │     │ FK sale_id       │     │ FK purchase_id   │
│ FK client_id │     │ FK product_id    │     │ FK product_id    │
│    total     │     │    quantity      │     │    quantity      │
│    created_at│     │    unit_price    │     │    unit_price    │
│    updated_at│     │ purchase_cost_   │     │    subtotal      │
└──────┬───────┘     │    at_sale       │     │    created_at    │
       │             │    subtotal      │     │    updated_at    │
       │             │    created_at    │     └────────┬─────────┘
       │             │    updated_at    │              │
       │             └──────────────────┘              │
       │                                               │
       ▼                                               ▼
┌──────────────────┐                          ┌──────────────────┐
│   purchases      │                          │     users        │
│──────────────────│                          │──────────────────│
│ PK id            │                          │ PK id            │
│    date          │                          │    name          │
│ FK supplier_id   │                          │    email         │
│    created_at    │                          │    password      │
│    updated_at    │                          │    email_verif.. │
└──────────────────┘                          │    remember_token│
                                              │    created_at    │
                                              │    updated_at    │
                                              └──────────────────┘
```

---

## Tablas

### `users`

Autenticación Breeze. Estándar de Laravel.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NOT NULL |
| email_verified_at | TIMESTAMP | NULLABLE |
| password | VARCHAR(255) | NOT NULL |
| remember_token | VARCHAR(100) | NULLABLE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

---

### `clients`

Personas o empresas que compran productos.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| name | VARCHAR(255) | NOT NULL |
| email | VARCHAR(255) | UNIQUE, NULLABLE |
| phone | VARCHAR(255) | NULLABLE |
| address | VARCHAR(255) | NULLABLE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Nota**: No tiene `tier`, `status` ni `total_spent` — se calculan dinámicamente desde `sales` en el controlador.

**Relaciones**: `Client N───N Sale` (1 cliente → N ventas)

---

### `suppliers`

Proveedores de productos. Tabla evolucionada con campos de fidelidad.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| name | VARCHAR(255) | NOT NULL |
| contact_person | VARCHAR(255) | NULLABLE |
| category | ENUM('Alimento','Accesorios','Higiene','Otros') | DEFAULT 'Otros', NOT NULL |
| logo | VARCHAR(255) | NULLABLE |
| email | VARCHAR(255) | UNIQUE, NULLABLE |
| phone | VARCHAR(255) | NULLABLE |
| address | VARCHAR(255) | NULLABLE |
| status | ENUM('Active','Inactive') | DEFAULT 'Active', NOT NULL |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Nota**: `category` es ENUM fijo. Para flexibilidad futura, migrar a tabla `categories` polimórfica.

**Relaciones**: `Supplier 1───N Product` · `Supplier 1───N Purchase`

---

### `products`

Catálogo de productos. Cada uno pertenece a un proveedor.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| name | VARCHAR(255) | NOT NULL |
| sku | VARCHAR(255) | UNIQUE, NULLABLE |
| category | VARCHAR(255) | NULLABLE |
| description | TEXT | NULLABLE |
| image | VARCHAR(255) | NULLABLE |
| sale_price | DECIMAL(10,2) | NOT NULL |
| purchase_price | DECIMAL(10,2) | NOT NULL |
| initial_stock | INTEGER | DEFAULT 0, NOT NULL |
| supplier_id | BIGINT UNSIGNED | FK → suppliers.id, ON DELETE CASCADE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Stock dinámico**: `initial_stock + SUM(purchase_details.quantity) - SUM(sale_details.quantity)`
Se calcula en `Product::getStock()` (3 queries). _N+1 pendiente de optimizar._

**Relaciones**: `Product N───1 Supplier` · `Product 1───N SaleDetail` · `Product 1───N PurchaseDetail`

---

### `purchases`

Compras realizadas a proveedores. No tiene columna `total` — se calcula de detalles.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| date | DATE | NOT NULL |
| supplier_id | BIGINT UNSIGNED | FK → suppliers.id, ON DELETE CASCADE |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Relaciones**: `Purchase N───1 Supplier` · `Purchase 1───N PurchaseDetail`

---

### `purchase_details`

Líneas de cada compra.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| purchase_id | BIGINT UNSIGNED | FK → purchases.id, ON DELETE CASCADE |
| product_id | BIGINT UNSIGNED | FK → products.id, ON DELETE CASCADE |
| quantity | INTEGER | NOT NULL |
| unit_price | DECIMAL(10,2) | NOT NULL |
| subtotal | DECIMAL(10,2) | NOT NULL (quantity × unit_price) |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Índice compuesto**: `(purchase_id, product_id)` implícito por FK.

---

### `sales`

Ventas a clientes. `total` se inicializa en 0 y se actualiza post-insert.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| date | DATETIME | NOT NULL |
| client_id | BIGINT UNSIGNED | FK → clients.id, ON DELETE CASCADE |
| total | DECIMAL(10,2) | DEFAULT 0, NOT NULL |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Relaciones**: `Sale N───1 Client` · `Sale 1───N SaleDetail`

---

### `sale_details`

Líneas de cada venta. Incluye `purchase_cost_at_sale` para márgenes históricos.

| Columna | Tipo | Restricciones |
|---------|------|---------------|
| id | BIGINT UNSIGNED | PK, auto_increment |
| sale_id | BIGINT UNSIGNED | FK → sales.id, ON DELETE CASCADE |
| product_id | BIGINT UNSIGNED | FK → products.id, ON DELETE CASCADE |
| quantity | INTEGER | NOT NULL |
| unit_price | DECIMAL(10,2) | NOT NULL |
| purchase_cost_at_sale | DECIMAL(10,2) | NOT NULL (costo del producto al momento de vender) |
| subtotal | DECIMAL(10,2) | NOT NULL (quantity × unit_price) |
| created_at | TIMESTAMP | NULLABLE |
| updated_at | TIMESTAMP | NULLABLE |

**Índice compuesto**: `(sale_id, product_id)` implícito por FK.

---

## Resumen de Relaciones

| De | Hacia | Tipo | FK |
|----|-------|------|----|
| products | suppliers | N:1 | `products.supplier_id` |
| purchases | suppliers | N:1 | `purchases.supplier_id` |
| purchase_details | purchases | N:1 | `purchase_details.purchase_id` |
| purchase_details | products | N:1 | `purchase_details.product_id` |
| sales | clients | N:1 | `sales.client_id` |
| sale_details | sales | N:1 | `sale_details.sale_id` |
| sale_details | products | N:1 | `sale_details.product_id` |

Todas las FK usan `ON DELETE CASCADE`.

---

## Normalización

### Forma Normal

- **1NF**: Todas las columnas son atómicas. No hay grupos repetitivos.
- **2NF**: Todos los atributos no clave dependen de la clave primaria completa. Las tablas detalle (`purchase_details`, `sale_details`) usan su propio `id` como PK, no compuestas.
- **3NF**: No hay dependencias transitivas. `purchase_details.subtotal` es derivable (`quantity × unit_price`) pero se almacena por conveniencia (rendimiento en informes). Misma lógica para `sales.total`.

### Desnormalización Intencional

| Columna | Razón |
|---------|-------|
| `sale_details.subtotal` | Evita recalcular en cada consulta de reporte |
| `sales.total` | Evita SUM() sobre detalles en listados y KPIs |
| `sale_details.purchase_cost_at_sale` | Preserva el costo histórico aunque el precio de compra del producto cambie |

---

## Pendientes / Mejoras

- [ ] Migrar `products.category` y `suppliers.category` de string/enum a tabla `categories` polimórfica
- [ ] Agregar columna `total` a `purchases` para evitar subquery en index
- [ ] Agregar `tier`, `status`, `total_spent` a `clients` si se necesita rendimiento sobre frescura
- [ ] Optimizar `Product::getStock()` con scope/subquery para eliminar N+1
