# Manual de Usuario — Sistema PetShop

> **Sistema de Gestión de Inventario y Punto de Venta**
> **Versión:** 1.0
> **Fecha:** Mayo 2026

---

## Índice

1. [Introducción](#1-introducción)
2. [Requisitos del Sistema](#2-requisitos-del-sistema)
3. [Acceso al Sistema](#3-acceso-al-sistema)
   - 3.1 [Inicio de Sesión](#31-inicio-de-sesión)
   - 3.2 [Registro de Nuevo Usuario](#32-registro-de-nuevo-usuario)
   - 3.3 [Recuperación de Contraseña](#33-recuperación-de-contraseña)
4. [Navegación General](#4-navegación-general)
   - 4.1 [Barra Superior](#41-barra-superior)
   - 4.2 [Dock de Navegación](#42-dock-de-navegación)
   - 4.3 [Barra Lateral](#43-barra-lateral)
   - 4.4 [Atajos de Teclado](#44-atajos-de-teclado)
5. [Dashboard — Panel Principal](#5-dashboard--panel-principal)
6. [Gestión de Productos](#6-gestión-de-productos)
   - 6.1 [Listado de Productos](#61-listado-de-productos)
   - 6.2 [Registrar un Nuevo Producto](#62-registrar-un-nuevo-producto)
   - 6.3 [Editar un Producto](#63-editar-un-producto)
   - 6.4 [Eliminar un Producto](#64-eliminar-un-producto)
7. [Gestión de Proveedores](#7-gestión-de-proveedores)
   - 7.1 [Listado de Proveedores](#71-listado-de-proveedores)
   - 7.2 [Registrar un Nuevo Proveedor](#72-registrar-un-nuevo-proveedor)
   - 7.3 [Editar un Proveedor](#73-editar-un-proveedor)
   - 7.4 [Eliminar un Proveedor](#74-eliminar-un-proveedor)
8. [Gestión de Clientes](#8-gestión-de-clientes)
   - 8.1 [Listado de Clientes](#81-listado-de-clientes)
   - 8.2 [Registrar un Nuevo Cliente](#82-registrar-un-nuevo-cliente)
   - 8.3 [Editar un Cliente](#83-editar-un-cliente)
   - 8.4 [Eliminar un Cliente](#84-eliminar-un-cliente)
9. [Gestión de Compras](#9-gestión-de-compras)
   - 9.1 [Listado de Compras](#91-listado-de-compras)
   - 9.2 [Registrar una Nueva Compra](#92-registrar-una-nueva-compra)
10. [Gestión de Ventas](#10-gestión-de-ventas)
    - 10.1 [Listado de Ventas](#101-listado-de-ventas)
    - 10.2 [Registrar una Nueva Venta](#102-registrar-una-nueva-venta)
    - 10.3 [Ver Detalle de una Venta](#103-ver-detalle-de-una-venta)
11. [Perfil de Usuario](#11-perfil-de-usuario)
12. [Solución de Problemas Comunes](#12-solución-de-problemas-comunes)

---

## 1. Introducción

**PetShop** es un sistema de gestión diseñado para administrar las operaciones diarias de una tienda de artículos para mascotas, forraje y jardinería. Con esta herramienta podés:

- Registrar y consultar tu catálogo de productos con sus precios y stock.
- Administrar proveedores y clientes.
- Controlar las compras que le hacés a tus proveedores.
- Procesar ventas con validación de stock en tiempo real.
- Ver indicadores clave del negocio en el dashboard (ventas, ganancias, productos bajos de stock).
- Saber en todo momento cuánto stock tenés de cada producto sin hacer inventario manual.

El sistema funciona via web, así que lo usás desde cualquier computadora con un navegador, ya sea en el local o de forma remota.

---

## 2. Requisitos del Sistema

| Requisito | Detalle |
|-----------|---------|
| **Navegador** | Google Chrome (recomendado), Mozilla Firefox, Microsoft Edge o Safari (versiones actualizadas) |
| **Conexión** | Red local (LAN) del comercio o internet si está configurado acceso remoto |
| **Resolución de pantalla** | Mínimo 1024 × 768 píxeles |
| **Dispositivos opcionales** | Lector de código de barras, impresora de comprobantes |

---

## 3. Acceso al Sistema

### 3.1 Inicio de Sesión

Para ingresar al sistema:

1. Abrí el navegador y entrá a la dirección donde está instalado el sistema (ej: `http://localhost` o la IP del servidor).
2. En la pantalla de bienvenida, hacé clic en **"Log in"** o andá directamente a `/login`.
3. Ingresá tu **correo electrónico** y **contraseña**.
4. Si querés que el sistema recuerde tu sesión, marcá **"Remember me"**.
5. Hacé clic en **"Log in"**.

> **Agregar captura de pantalla: Pantalla de inicio de sesión aquí**

Si los datos son correctos, vas a entrar al **Dashboard** (panel principal).

**Usuario por defecto:** Si es la primera vez que usás el sistema, podés entrar con:
- **Email:** `admin@petshop.com`
- **Contraseña:** `password`

> ⚠️ **Importante:** Cambiá la contraseña apenas entres por primera vez desde tu perfil de usuario.

### 3.2 Registro de Nuevo Usuario

Si el administrador te dio acceso para registrarte:

1. En la pantalla de bienvenida, hacé clic en **"Register"**.
2. Completá los campos:
   - **Name** — Tu nombre completo.
   - **Email** — Tu correo electrónico.
   - **Password** — Una contraseña segura.
   - **Confirm Password** — Repetí la contraseña.
3. Hacé clic en **"Register"**.

> **Agregar captura de pantalla: Pantalla de registro aquí**

Te va a llegar un correo de verificación. Hacé clic en el enlace para activar tu cuenta. Si no lo recibís, podés solicitar que lo reenvíen desde la pantalla de verificación.

### 3.3 Recuperación de Contraseña

Si olvidaste tu contraseña:

1. En la pantalla de inicio de sesión, hacé clic en **"Forgot your password?"**.
2. Ingresá tu correo electrónico.
3. Hacé clic en **"Email Password Reset Link"**.

> **Agregar captura de pantalla: Pantalla de recuperación de contraseña aquí**

Te va a llegar un correo con un enlace para restablecer la contraseña. Seguí las instrucciones.

---

## 4. Navegación General

Una vez que iniciás sesión, el sistema tiene 3 elementos de navegación principales:

### 4.1 Barra Superior

La barra superior se ve en todas las pantallas y te muestra:

- **Indicador de sección:** El nombre de la página donde estás (ej: "Productos", "Dashboard").
- **Búsqueda contextual:** Un campo de búsqueda que cambia según la sección donde estés. Por ejemplo, en productos te permite buscar por nombre o SKU.
- **Botón de cambio de navegación:** Cambia entre el modo **dock** (barra inferior) y **sidebar** (barra lateral).
- **Icono de ayuda** y **configuración**.
- **Notificaciones:** Una campanita con indicador de novedades.
- **Tu usuario:** Tu nombre y avatar. Haciendo clic podés ir a tu perfil o cerrar sesión.

> **Agregar captura de pantalla: Barra superior aquí**

### 4.2 Dock de Navegación

Por defecto, el sistema muestra un **dock** en la parte inferior de la pantalla (similar al de macOS). Tiene 6 íconos principales:

| Ícono | Sección | Atajo |
|-------|---------|-------|
| 📊 Inicio | Dashboard | `Ctrl + 1` |
| 📦 Productos | Gestión de productos | `Ctrl + 2` |
| 🚚 Proveedores | Gestión de proveedores | `Ctrl + 3` |
| 🛒 Compras | Gestión de compras | `Ctrl + 4` |
| 💰 Ventas | Gestión de ventas | `Ctrl + 5` |
| 👥 Clientes | Gestión de clientes | `Ctrl + 6` |
| 🚪 Salir | Cerrar sesión | `Ctrl + Q` |

> **Agregar captura de pantalla: Dock de navegación aquí**

La sección activa se marca con un fondo más claro.

### 4.3 Barra Lateral

Si preferís una navegación más tradicional, hacé clic en el botón de cambio en la barra superior para activar la **barra lateral** (sidebar). Tiene las mismas opciones que el dock pero en el costado izquierdo de la pantalla.

Podés colapsarla para que ocupe menos espacio haciendo clic en el botón de menú (≡).

> **Agregar captura de pantalla: Barra lateral aquí**

### 4.4 Atajos de Teclado

Para moverte más rápido, usá estos atajos:

| Tecla | Acción |
|-------|--------|
| `Ctrl + 1` | Ir al Dashboard |
| `Ctrl + 2` | Ir a Productos |
| `Ctrl + 3` | Ir a Proveedores |
| `Ctrl + 4` | Ir a Compras |
| `Ctrl + 5` | Ir a Ventas |
| `Ctrl + 6` | Ir a Clientes |
| `Ctrl + Q` | Cerrar sesión |

---

## 5. Dashboard — Panel Principal

El dashboard es la primera pantalla que ves al entrar. Te da un resumen rápido del estado del negocio.

> **Agregar captura de pantalla: Dashboard completo aquí**

### Tarjetas de indicadores (KPIs)

Arriba de todo ves 4 tarjetas:

| Indicador | Qué muestra |
|-----------|-------------|
| **Inversión Total** | Cuánta plata llevás gastada en compras a proveedores (acumulado histórico). |
| **Ventas Totales** | Cuánto llevás vendido (ingresos generados). |
| **Utilidad Neta** | La diferencia entre lo que vendiste y lo que te costó. Si es negativa aparece en rojo. |
| **Stock Bajo** | La cantidad de productos que tienen 5 o menos unidades. Si hay alguno, aparece en rojo con "Atención inmediata". |

### Gráficos de tendencia

Debajo de las tarjetas hay dos gráficos de líneas:

- **Ventas Mensuales:** Muestra cómo vienen las ventas mes a mes en los últimos 12 meses.
- **Compras Mensuales:** Muestra cómo vienen las compras mes a mes.

> **Agregar captura de pantalla: Gráficos del dashboard aquí**

### Tablas de actividad reciente

Abajo se muestran:

- **Ventas Recientes:** Las últimas 5 ventas realizadas, con cliente, cantidad de productos, monto y estado.
- **Compras Recientes:** Las últimas 5 compras realizadas, con proveedor, productos, costo y estado.

Desde acá podés hacer clic en **"Ver Todo"** para ir al listado completo de ventas o compras.

---

## 6. Gestión de Productos

### 6.1 Listado de Productos

Entrá a **Productos** desde el dock o sidebar para ver el catálogo completo.

> **Agregar captura de pantalla: Listado de productos aquí**

Arriba del listado ves:

- **"Añadir Producto"** — Botón para cargar un producto nuevo.
- **"Filtros"** — Para filtrar por categoría u otros criterios.
- **Tarjetas resumen:**
  - **Total Productos** — Cuántos productos tenés registrados.
  - **Sin Stock** — Cuántos productos están agotados.
  - **Valor del Inventario** — Suma total del valor de tu stock (precio de compra × cantidad).

La tabla de productos muestra:

| Columna | Descripción |
|---------|-------------|
| **Info Producto** | Nombre del producto, código SKU y foto (o un ícono si no tiene imagen). |
| **Categoría** | Nutrición, Accesorios, Higiene, Salud, Juguetes, Acuarios, Exóticos. |
| **P. Compra** | Precio al que comprás el producto. |
| **P. Venta** | Precio al que lo vendés. |
| **Estado Stock** | **Con Stock** (verde), **Stock Bajo** (naranja, ≤ 5 unidades), **Agotado** (rojo). |
| **Acciones** | Botón de **Editar** (lápiz) y **Eliminar** (papelera). |

> **Agregar captura de pantalla: Fila de producto con stock bajo resaltado aquí**

### 6.2 Registrar un Nuevo Producto

1. En el listado de productos, hacé clic en **"Añadir Producto"**.
2. Se abre una ventana modal con el formulario de carga.

> **Agregar captura de pantalla: Modal de registro de producto aquí**

Completá los siguientes campos:

| Campo | Descripción | Obligatorio |
|-------|-------------|:-----------:|
| **Nombre del Producto** | El nombre tal como aparece en la góndola (ej: "Alimento Premium para Perros 15kg"). | ✅ |
| **SKU** | Código único para identificar el producto (ej: "ALI-PRE-001"). No puede repetirse. | ✅ |
| **Categoría** | Seleccioná la categoría del producto del menú desplegable. | ✅ |
| **Proveedor** | Seleccioná el proveedor que te vende este producto. Si no está en la lista, primero tenés que [registrarlo](#72-registrar-un-nuevo-proveedor). | ✅ |
| **Costo ($)** | El precio al que le comprás el producto al proveedor. | ✅ |
| **Precio Venta ($)** | El precio al que lo vendés al público. | ✅ |
| **Stock Inicial** | Cuántas unidades tenés en este momento (si ya tenés stock físico). Si es 0, el producto aparece como "Agotado". | |
| **Descripción** | Características, peso, marca, beneficios del producto. | |
| **Imagen** | Hacé clic en **"Subir Media"** para seleccionar una foto del producto (máximo 2MB). | |

3. Hacé clic en **"Guardar Producto"** para confirmar o **"Cancelar"** para volver atrás.

### 6.3 Editar un Producto

1. En el listado, hacé clic en el ícono de **lápiz** (Editar) del producto que querés modificar.
2. Se abre el mismo formulario que en el alta, pero con los datos ya cargados.
3. Modificá los campos que necesites.
4. Hacé clic en **"Guardar Cambios"**.

> **Agregar captura de pantalla: Modal de edición de producto aquí**

### 6.4 Eliminar un Producto

1. En el listado, hacé clic en el ícono de **papelera** (Eliminar) del producto.
2. El sistema te va a pedir confirmación. Hacé clic en **"Aceptar"** para confirmar o **"Cancelar"** para mantenerlo.

> ⚠️ **Importante:** Eliminar un producto borra todos sus datos. Si el producto ya tiene ventas o compras asociadas, el sistema puede impedir su eliminación.

---

## 7. Gestión de Proveedores

### 7.1 Listado de Proveedores

Entrá a **Proveedores** desde la navegación.

> **Agregar captura de pantalla: Listado de proveedores aquí**

La tabla muestra:

| Columna | Descripción |
|---------|-------------|
| **Info del Socio** | Nombre del proveedor, correo electrónico y logo (o inicial si no tiene logo). |
| **Contacto Principal** | Nombre de la persona de contacto y teléfono. |
| **Categoría** | Alimento, Accesorios, Higiene, Otros (cada una con un color distinto). |
| **Estado** | **Activo** (punto verde) o **Inactivo** (punto rojo). |
| **Acciones** | Editar y Eliminar. |

### 7.2 Registrar un Nuevo Proveedor

1. Hacé clic en **"Añadir Proveedor"**.

> **Agregar captura de pantalla: Modal de registro de proveedor aquí**

Completá:

| Campo | Descripción |
|-------|-------------|
| **Nombre Empresa** | Razón social o nombre del proveedor. |
| **Contacto** | Nombre de la persona con la que hablás para hacer pedidos. |
| **Categoría** | Alimento, Accesorios, Higiene u Otros. |
| **Teléfono** | Número de contacto del proveedor. |
| **Email** | Correo electrónico. |
| **Dirección** | Dirección del proveedor. |
| **Logo** | Podés subir el logo de la empresa (opcional). |

2. Hacé clic en **"Guardar Proveedor"**.

### 7.3 Editar un Proveedor

1. Hacé clic en **Editar** (lápiz) en el proveedor que querés modificar.
2. Actualizá los campos necesarios.
3. Hacé clic en **"Guardar Cambios"**.

### 7.4 Eliminar un Proveedor

1. Hacé clic en **Eliminar** (papelera).
2. Confirmá la operación.

---

## 8. Gestión de Clientes

### 8.1 Listado de Clientes

Entrá a **Clientes** desde la navegación.

> **Agregar captura de pantalla: Listado de clientes aquí**

La tabla de clientes muestra:

| Columna | Descripción |
|---------|-------------|
| **Customer** | Nombre del cliente con su código interno. |
| **Contact** | Correo y teléfono del cliente. |
| **Last Purchase** | Fecha de la última compra que hizo. |
| **Total Spent** | Cuánto gastó en total en el negocio. |
| **Status** | Estado del cliente. |
| **Acciones** | Editar y Eliminar. |

Además, el listado incluye:

- **Tarjetas de resumen:** Total de clientes, nuevos del mes, cliente principal.
- **Gráfico de tendencias:** Volumen de compras de los últimos 6 meses.
- **Distribución por nivel:** Clientes Gold, Silver y Standard.

### 8.2 Registrar un Nuevo Cliente

1. Hacé clic en **"Añadir Cliente"** (o **"Add Client"**).

> **Agregar captura de pantalla: Modal de registro de cliente aquí**

Completá:

| Campo | Descripción |
|-------|-------------|
| **Full Name** | Nombre completo del cliente. |
| **Email Address** | Correo electrónico. |
| **Phone Number** | Teléfono. |
| **Physical Address** | Dirección. |

2. Hacé clic en **"Create Client"** (o **"Guardar"** ).

### 8.3 Editar un Cliente

1. Hacé clic en **Editar** en el cliente que querés modificar.
2. Actualizá los datos.
3. Hacé clic en **"Save Changes"**.

### 8.4 Eliminar un Cliente

1. Hacé clic en **Eliminar**.
2. Confirmá la operación.

---

## 9. Gestión de Compras

### 9.1 Listado de Compras

Entrá a **Compras** desde la navegación.

> **Agregar captura de pantalla: Listado de compras aquí**

La tabla muestra:

| Columna | Descripción |
|---------|-------------|
| **ID Transacción** | Número de comprobante interno (ej: #COM-00001). |
| **Info del Proveedor** | Nombre del proveedor al que le compraste. |
| **Fecha de Orden** | Cuándo se hizo la compra. |
| **Monto Total** | Suma de todo lo comprado en esa transacción. |
| **Acciones** | Ver detalle. |

### 9.2 Registrar una Nueva Compra

1. Hacé clic en **"Registrar Compra"**.

> **Agregar captura de pantalla: Modal de registro de compra aquí**

**Paso 1 — Datos generales:**

| Campo | Descripción |
|-------|-------------|
| **Proveedor Global** | Seleccioná el proveedor al que le estás comprando. |
| **Fecha de Transacción** | La fecha de la compra (por defecto la fecha de hoy). |

**Paso 2 — Productos comprados:**

- Hacé clic en **"Añadir Entrada"** por cada producto diferente que hayas comprado.
- Por cada producto completá:
  - **Selección de Producto:** Elegí el producto del catálogo.
  - **Cant.:** Cuántas unidades compraste.
  - **Costo Unit.:** El precio unitario que te cobró el proveedor.
- El **Subtotal** de cada línea y la **Valuación Total** al pie se calculan automáticamente.

> **Agregar captura de pantalla: Línea de detalle de compra aquí**

**Para sacar un producto de la lista:** Hacé clic en el ícono de papelera en esa línea.

3. Revisá que todo esté correcto.
4. Hacé clic en **"Autorizar Compra"**.

> ✅ La compra se registra y el stock de los productos se actualiza automáticamente (se suman las cantidades compradas).

---

## 10. Gestión de Ventas

### 10.1 Listado de Ventas

Entrá a **Ventas** desde la navegación.

> **Agregar captura de pantalla: Listado de ventas aquí**

La tabla muestra:

| Columna | Descripción |
|---------|-------------|
| **ID Transacción** | Número de comprobante interno (ej: #VTA-00001). |
| **Info del Cliente** | Nombre del cliente que compró (o "Consumidor Final"). |
| **Fecha de Venta** | Cuándo se hizo la venta. |
| **Ingreso** | Monto total de la venta. |
| **Acciones** | Ver detalle (ojo). |

### 10.2 Registrar una Nueva Venta

1. Hacé clic en **"Registrar Venta"**.

> **Agregar captura de pantalla: Modal de registro de venta aquí**

**Paso 1 — Datos generales:**

| Campo | Descripción |
|-------|-------------|
| **Selección de Cliente** | Elegí el cliente del listado. Si el cliente es nuevo, primero registralo desde [Clientes](#8-gestión-de-clientes). Si no querés registrar un cliente en particular, seleccioná "Consumidor Final". |
| **Fecha de Venta** | La fecha de la venta (por defecto la fecha de hoy). |

**Paso 2 — Productos vendidos:**

- Hacé clic en **"Añadir Item"** por cada producto diferente que lleve el cliente.
- Por cada producto completá:
  - **Selección de Inventario:** Elegí el producto. El sistema te muestra el stock disponible entre paréntesis (ej: "Alimento Premium (Stock: 15)").
  - **Cant.:** Cuántas unidades lleva. No te va a dejar poner más del stock disponible.
  - **P. Unitario:** El precio de venta se carga automáticamente cuando seleccionás el producto. Lo podés modificar si necesitás hacer un descuento o ajuste.

> **Agregar captura de pantalla: Selección de producto con stock visible aquí**

El **Ingreso Bruto** al pie se actualiza solo con cada línea que agregás.

3. Revisá que todo esté correcto.
4. Hacé clic en **"Completar Venta"**.

> ✅ El sistema valida el stock antes de guardar. Si hay stock suficiente, la venta se registra, el stock se descuenta automáticamente y se calcula la ganancia de la operación.

### 10.3 Ver Detalle de una Venta

1. En el listado de ventas, hacé clic en el ícono de **ojo** de la venta que querés consultar.

> **Agregar captura de pantalla: Detalle de venta aquí**

Se muestra:

- **Fecha** de la venta.
- **Cliente** que compró.
- **Total** de la venta.

Y el detalle de cada producto vendido:

| Columna | Descripción |
|---------|-------------|
| **Producto** | Nombre del producto. |
| **Cantidad** | Unidades vendidas. |
| **Precio Unit.** | Precio de venta. |
| **Costo Compra** | Lo que costó comprarlo (al momento de la venta). |
| **Ganancia** | Diferencia entre precio de venta y costo. |
| **Subtotal** | Total por ese producto (precio × cantidad). |

---

## 11. Perfil de Usuario

Desde la barra superior, hacé clic en tu nombre/avatar y seleccioná **"Profile"** para ir a tu perfil.

> **Agregar captura de pantalla: Perfil de usuario aquí**

### Información de perfil

Podés actualizar:

- **Name** — Tu nombre.
- **Email** — Tu correo electrónico.

Si cambiás el email, te va a pedir que lo verifiques de nuevo.

Hacé clic en **"Save"** para guardar los cambios.

### Cambiar contraseña

1. En la sección **"Update Password"**, ingresá:
   - **Current Password** — Tu contraseña actual.
   - **New Password** — La nueva contraseña.
   - **Confirm Password** — Repetí la nueva contraseña.
2. Hacé clic en **"Save"**.

### Eliminar cuenta

En la sección **"Delete Account"** podés eliminar tu usuario. **Esta acción no se puede deshacer.**

1. Hacé clic en **"Delete Account"**.
2. En la ventana de confirmación, ingresá tu contraseña.
3. Hacé clic en **"Delete Account"** para confirmar.

---

## 12. Solución de Problemas Comunes

### No puedo iniciar sesión

**Posibles causas y soluciones:**

| Problema | Solución |
|----------|----------|
| Olvidé mi contraseña | Usá la opción **"Forgot your password?"** en la pantalla de login. |
| El usuario está bloqueado | Esperá unos minutos e intentá de nuevo (hay un límite de 5 intentos). |
| No verificaron mi email | Revisá tu bandeja de entrada y hacé clic en el enlace de verificación. |

### El sistema me dice "Stock insuficiente" al vender

El producto no tiene la cantidad suficiente en stock. Posibles soluciones:

1. **Registrá una compra** de ese producto para aumentar el stock.
2. **Vendé una cantidad menor** a la disponible.
3. Si el stock está mal, revisá que todas las compras y ventas de ese producto estén bien registradas.

### No encuentro un producto en el listado

1. Usá el campo de **búsqueda** en la barra superior o en la pantalla de productos.
2. Revisá si los filtros están ocultando algunos resultados.
3. Si el producto no existe, registralo como nuevo.

### El precio de venta no se carga automáticamente en la venta

Asegurate de que el producto tenga cargado el **precio de venta**. Si el campo está vacío, cargalo manualmente en la venta y después actualizá el producto desde **Editar Producto**.

### Necesito ayuda

Si tenés algún problema que no está cubierto acá, contactá al administrador del sistema.

---

> **Fin del manual de usuario — PetShop v1.0**
