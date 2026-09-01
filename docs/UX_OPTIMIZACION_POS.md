# Registro de Cambios UI/UX - Optimización POS

## Cambios en el Flujo de Ventas (Sales)

### 1. Buscador Predictivo de Productos
- **Problema**: El uso de un `<select>` estándar dificultaba la selección en inventarios grandes.
- **Solución**: Se integró un sistema de búsqueda sobre el select (vía JS) para permitir filtrado rápido por nombre.

### 2. Feedback de Stock Proactivo
- **Problema**: El usuario solo sabía si había stock al intentar guardar o leyendo un texto pequeño.
- **Solución**: Se agregó un indicador visual (badge) que cambia de color y previene la carga si la cantidad excede el stock disponible en tiempo real.

### 3. Mejora Estética de Filas
- **Problema**: El diseño de las filas de productos era tosco y ocupaba mucho espacio vertical.
- **Solución**: Se compactó el diseño de las filas (item-row) usando una grilla más eficiente y estilos de "tarjeta" dentro del formulario.

## Notas Técnicas
- Se mantiene la lógica de `Product::getStock()` para consistencia de datos.
- No se alteró la estructura de la base de datos, solo la capa de presentación y la interacción cliente (JS).
