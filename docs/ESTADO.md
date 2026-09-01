# Petshop Laravel - Estado del Proyecto

> Fecha: 25/04/2026
> Estado: **COMPLETADO**

## Lo que tenemos

### Stack
- Laravel 11+
- Breeze (auth completo)
- Vite + TailwindCSS

### Base de datos (migraciones)

| Tabla | Columnas |
|------|---------|
| `users` | id, name, email, password, role, timestamps |
| `suppliers` | id, name, email, phone, address, timestamps |
| `clients` | id, name, email, phone, address, timestamps |
| `products` | id, name, description, sale_price, purchase_price, supplier_id, timestamps |
| `purchases` | id, supplier_id, date, timestamps |
| `purchase_details` | id, purchase_id, product_id, quantity, unit_price, subtotal, timestamps |
| `sales` | id, client_id, date, total, timestamps |
| `sale_details` | id, sale_id, product_id, quantity, unit_price, purchase_cost_at_sale, subtotal, timestamps |

### Modelos Eloquent (8)

- User, Product (con getStock() dinámico)
- Supplier, Client
- Purchase + PurchaseDetail
- Sale + SaleDetail

### Controllers (5)

- ProductController (CRUD)
- SupplierController (CRUD)
- ClientController (CRUD)
- PurchaseController (create + list + store)
- SaleController (create + list + store + show)

### Requests (7)

- StoreProductRequest, UpdateProductRequest
- StoreSupplierRequest, UpdateSupplierRequest
- StoreClientRequest, UpdateClientRequest
- StoreSaleRequest

### Vistas Blade (15+)

- Dashboard con métricas en tiempo real
- products/, suppliers/, clients/ (index, create, edit)
- purchases/ (index, create)
- sales/ (index, create, show)

### Seeders & Factories

- **Factories**: Supplier, Client, Product, Purchase, PurchaseDetail, Sale, SaleDetail
- **Seeders**: SupplierSeeder (5), ClientSeeder (10), ProductSeeder (12), PurchaseSeeder (8), SaleSeeder (15)

### Sidebar

- Inicio, Productos, Proveedores, Compras, Ventas, Clientes, Cerrar Sesión

## Decisiones arquitectónicas

### Stock dinámico
- Sin campo `stock` en tabla products
- `Product::getStock()` calcula: SUM(purchases) - SUM(sales)

### Validaciones
- Stock validado ANTES de crear venta
- Costo de compra guardado al momento de la venta

## Cómo correrlo

```bash
cd /home/martin/petshop-laravel

# Instalar
composer install
npm install

# Migrar + seedear
php artisan migrate:fresh --seed

# Correr
php artisan serve
```

**Login:** admin@petshop.com / password