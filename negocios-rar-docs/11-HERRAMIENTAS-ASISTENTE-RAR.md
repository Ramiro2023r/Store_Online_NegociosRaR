# 11-HERRAMIENTAS-ASISTENTE-RAR.md

**Proyecto:** Negocios RaR  
**Módulo:** Asistente RaR  
**Versión:** 1.0.0  
**Estado:** Especificación funcional y técnica  
**Framework:** Laravel 12  
**Base de datos:** PostgreSQL  
**Proveedor IA inicial:** Groq  
**Frontend:** Blade + Tailwind CSS CDN + Alpine.js  

---

# 1. Propósito del documento

Este documento define el catálogo oficial de herramientas que podrá utilizar el **Asistente RaR**.

Una herramienta representa una capacidad controlada del sistema, por ejemplo:

- Buscar productos.
- Consultar ventas.
- Crear banners.
- Actualizar stock.
- Consultar pedidos.
- Agregar productos al carrito.
- Consultar puntos del cliente.

El modelo de inteligencia artificial no tendrá acceso directo a PostgreSQL, Eloquent, controladores, archivos, comandos del servidor ni servicios internos.

La IA solamente podrá:

1. Interpretar la intención del usuario.
2. Seleccionar una herramienta autorizada.
3. Proponer los parámetros necesarios.
4. Solicitar confirmación cuando corresponda.
5. Esperar que Laravel valide y ejecute la acción.
6. Presentar el resultado devuelto por Laravel.

---

# 2. Principios obligatorios

## 2.1. La IA no ejecuta lógica de negocio

El Asistente RaR no debe crear, modificar ni eliminar información por sí mismo.

Toda operación debe seguir este flujo:

```text
Usuario
   ↓
Asistente RaR
   ↓
Tool Registry
   ↓
Validación de permisos
   ↓
Validación de parámetros
   ↓
Confirmación, cuando corresponda
   ↓
Tool Executor
   ↓
Service de Laravel
   ↓
Modelo Eloquent / Repository
   ↓
PostgreSQL
```

## 2.2. No se permite SQL directo

Está prohibido que una herramienta:

- Ejecute consultas SQL construidas por la IA.
- Acepte fragmentos SQL como parámetros.
- Permita seleccionar tablas o columnas arbitrariamente.
- Ejecute comandos del sistema operativo.
- Ejecute código PHP arbitrario.
- Modifique migraciones.
- Modifique archivos del proyecto.
- Instale paquetes o dependencias.

## 2.3. Una herramienta tiene una responsabilidad

Cada herramienta debe realizar una sola tarea claramente definida.

Ejemplos correctos:

```text
product.search
product.create
product.update_price
banner.activate
order.update_status
```

Ejemplo incorrecto:

```text
admin.execute_anything
```

## 2.4. Permisos aplicados en Laravel

La lista de herramientas enviada a Groq debe estar filtrada previamente según:

- Usuario autenticado.
- Rol.
- Policies.
- Gates.
- Estado de la cuenta.
- Contexto actual.
- Propiedad del recurso.
- Reglas del negocio.

Aunque el modelo solicite una herramienta, Laravel debe volver a comprobar la autorización antes de ejecutarla.

## 2.5. Confirmación obligatoria

Las herramientas se clasifican en cuatro niveles:

| Nivel | Tipo | Confirmación |
|---|---|---|
| 0 | Navegación o ayuda | No |
| 1 | Consulta | No |
| 2 | Creación o modificación reversible | Sí |
| 3 | Acción crítica, masiva o destructiva | Doble confirmación |

## 2.6. Auditoría

Toda ejecución debe registrar:

- Usuario.
- Rol.
- Herramienta.
- Parámetros sanitizados.
- Recurso afectado.
- Resultado.
- Estado.
- IP.
- User-Agent.
- Fecha y hora.
- Conversación.
- Mensaje que originó la acción.
- Datos anteriores y posteriores cuando corresponda.

---

# 2.5. Estado de implementación (v1.0)

**Actualizado:** Julio 2026  
**Tools implementadas:** 96 de 156 (~62%)

## Implementadas (✅)

| Herramienta | Roles | Service |
|------------|-------|---------|
| `product.search` | todos | `ProductService` |
| `product.create` | admin, trabajador | `ProductService` |
| `product.update` | admin, trabajador | `ProductService` |
| `product.delete` | admin | `ProductService` |
| `product.duplicate` | admin, trabajador | `ProductService` |
| `product.update_price` | admin, trabajador | `ProductService` |
| `product.update_stock` | admin, trabajador | `ProductService` |
| `product.change_status` | admin, trabajador | `ProductService` |
| `category.search` | todos | `CategoryService` |
| `category.create` | admin, trabajador | `CategoryService` |
| `category.update` | admin, trabajador | `CategoryService` |
| `category.delete` | admin | `CategoryService` |
| `category.change_status` | admin, trabajador | `CategoryService` |
| `brand.search` | todos | `BrandService` |
| `cart.get` | todos | `CartService` |
| `cart.add_item` | todos | `CartService` |
| `cart.update_quantity` | todos | `CartService` |
| `cart.remove_item` | todos | `CartService` |
| `cart.estimate_totals` | todos | `CartService` |
| `wishlist.get` | todos | `WishlistService` |
| `wishlist.add` | todos | `WishlistService` |
| `wishlist.remove` | todos | `WishlistService` |
| `coupon.search` | admin | `CouponService` |
| `coupon.create` | admin | `CouponService` |
| `coupon.update` | admin | `CouponService` |
| `coupon.activate` | admin | `CouponService` |
| `coupon.deactivate` | admin | `CouponService` |
| `coupon.delete` | admin | `CouponService` |
| `coupon.validate` | todos | `CouponService` |
| `review.search` | admin, trabajador | `ReviewService` |
| `review.approve` | admin, trabajador | `ReviewService` |
| `review.reject` | admin, trabajador | `ReviewService` |
| `review.delete` | admin | `ReviewService` |
| `review.summary` | admin, trabajador | `ReviewService` |
| `banner.search` | admin, trabajador | `BannerService` |
| `banner.get` | admin, trabajador | `BannerService` |
| `banner.create` | admin, trabajador | `BannerService` |
| `banner.update` | admin, trabajador | `BannerService` |
| `banner.activate` | admin, trabajador | `BannerService` |
| `banner.deactivate` | admin, trabajador | `BannerService` |
| `banner.delete` | admin | `BannerService` |
| `banner.reorder` | admin | `BannerService` |
| `faq.search` | todos | `FaqService` |
| `faq.create` | admin, trabajador | `FaqService` |
| `faq.update` | admin, trabajador | `FaqService` |
| `faq.delete` | admin | `FaqService` |
| `benefit.search` | todos | `BenefitService` |
| `benefit.create` | admin, trabajador | `BenefitService` |
| `benefit.update` | admin, trabajador | `BenefitService` |
| `benefit.delete` | admin | `BenefitService` |
| `inventory.low_stock` | admin, trabajador | `InventoryService` |
| `inventory.out_of_stock` | admin, trabajador | `InventoryService` |
| `inventory.movements` | admin, trabajador | `InventoryService` |
| `inventory.adjust` | admin, trabajador | `InventoryService` |
| `inventory.set_minimum_stock` | admin, trabajador | `InventoryService` |
| `order.search` | admin, trabajador, cliente | `OrderService` |
| `order.get` | admin, trabajador, cliente | `OrderService` |
| `order.update_status` | admin, trabajador | `OrderService` |
| `order.timeline` | admin, trabajador, cliente | `OrderService` |
| `user.search` | admin | `UserService` |
| `user.create_worker` | admin | `UserService` |
| `user.update` | admin | `UserService` |
| `user.change_role` | admin | `UserService` |
| `user.block` | admin | `UserService` |
| `user.unblock` | admin | `UserService` |
| `address.list` | todos | `AddressService` |
| `address.create` | todos | `AddressService` |
| `address.update` | todos | `AddressService` |
| `address.set_default` | todos | `AddressService` |
| `support.conversations` | admin, trabajador | `SupportService` |
| `support.get_conversation` | admin, trabajador | `SupportService` |
| `support.reply` | admin, trabajador | `SupportService` |
| `support.close_conversation` | admin, trabajador | `SupportService` |
| `support.reopen_conversation` | admin, trabajador | `SupportService` |
| `loyalty.get_balance` | todos | `LoyaltyService` |
| `loyalty.get_movements` | todos | `LoyaltyService` |
| `loyalty.adjust_balance` | admin | `LoyaltyService` |
| `newsletter.subscribers` | admin | `NewsletterService` |
| `newsletter.export` | admin | `NewsletterService` |
| `report.sales` | admin, trabajador | `ReportService` |
| `report.products` | admin, trabajador | `ReportService` |
| `report.inventory` | admin, trabajador | `ReportService` |
| `report.customers` | admin | `ReportService` |
| `report.orders` | admin | `ReportService` |
| `report.export_csv` | admin, trabajador | — (deriva al panel) |
| `setting.get_public` | todos | `SettingService` |
| `setting.get_admin` | admin | `SettingService` |
| `setting.update` | admin | `SettingService` |
| `variant.search` | admin, trabajador | `VariantService` |
| `variant.create` | admin, trabajador | `VariantService` |
| `variant.update` | admin, trabajador | `VariantService` |
| `variant.update_stock` | admin, trabajador | `VariantService` |
| `variant.delete` | admin | `VariantService` |
| `system.capabilities` | todos | `SystemTool` |

## No implementadas (❌) — funcionalidad no existe en Negocios RaR

| Herramientas | Motivo |
|-------------|--------|
| `product.related`, `product.compare` | No existe lógica de productos relacionados ni endpoint |
| `compare.*` | El comparador existe pero sin service; no hay `compare.get/add/remove` como tools |
| `promotion.*` | No existe modelo Promotion ni gestión de promociones |
| `recommendation.*` | No existe RecommendationService |
| `notification.*` | No existe sistema de notificaciones internas |
| `navigation.open` | Requiere integración frontend (Alpine.js store) |
| `assistant.help` | No existe contenido de ayuda del asistente |
| `order.add_internal_note` | No existe campo de notas internas en Order |
| `order.resend_confirmation` | No existe endpoint de reenvío |
| `order.export` | No existe exportación de pedidos |
| `newsletter.send_campaign` | No existe envío de campañas desde el sistema |
| `setting.test_email` | No existe envío de prueba |
| `customer.*` (order_history, loyalty_balance) | No existe CustomerService independiente |
| `dashboard.summary` | No existe DashboardService |
| `sales.*` (summary_today, by_category, etc.) | Parcialmente cubierto por `report.*` |
| `brand.create/update/delete/change_status` | Brand es solo campo texto en Product, no entidad |

# 3. Convención de nombres

Las herramientas utilizarán nombres en inglés técnico y formato `dominio.accion`.

Ejemplos:

```text
product.search
product.create
product.update
product.update_stock
order.search
order.update_status
banner.create
sales.summary_today
cart.add_item
```

Los nombres visibles para el usuario permanecerán en español.

---

# 4. Contrato común de una herramienta

Todas las herramientas deben implementar una interfaz común.

```php
interface AssistantToolInterface
{
    public function name(): string;

    public function description(): string;

    public function inputSchema(): array;

    public function requiredPermissions(): array;

    public function confirmationLevel(): int;

    public function supportsRole(string $role): bool;

    public function execute(
        AuthenticatedAssistantContext $context,
        array $arguments
    ): ToolResult;
}
```

## 4.1. Respuesta estándar

```php
final class ToolResult
{
    public bool $success;
    public string $message;
    public array $data;
    public ?string $errorCode;
    public array $metadata;
}
```

Ejemplo de respuesta exitosa:

```json
{
  "success": true,
  "message": "Se encontraron 3 productos.",
  "data": {
    "products": []
  },
  "errorCode": null,
  "metadata": {
    "tool": "product.search",
    "duration_ms": 120
  }
}
```

Ejemplo de error:

```json
{
  "success": false,
  "message": "No tienes permiso para realizar esta acción.",
  "data": {},
  "errorCode": "FORBIDDEN",
  "metadata": {
    "tool": "user.change_role"
  }
}
```

---

# 5. Estados de ejecución

Toda ejecución debe utilizar uno de estos estados:

```text
requested
awaiting_confirmation
confirmed
executing
completed
failed
cancelled
expired
denied
```

---

# 6. Roles y alcance general

## 6.1. Cliente

Puede usar herramientas relacionadas con:

- Productos.
- Categorías y marcas públicas.
- Comparación.
- Carrito.
- Lista de deseos.
- Pedidos propios.
- Direcciones propias.
- Puntos propios.
- Cupones públicos o aplicables.
- Reseñas propias.
- Información pública de la tienda.
- Ayuda durante la compra.

No puede consultar:

- Ventas.
- Ganancias.
- Clientes.
- Inventario interno.
- Configuración administrativa.
- Pedidos de otros usuarios.
- Historial de auditoría.
- Datos privados de trabajadores o administradores.

## 6.2. Trabajador

Puede usar herramientas relacionadas con:

- Productos.
- Categorías.
- Marcas.
- Inventario.
- Pedidos.
- Mensajes.
- Reseñas.
- Reportes autorizados.
- Banners, si su permiso lo permite.

No puede:

- Administrar usuarios.
- Crear administradores.
- Cambiar roles.
- Modificar configuraciones críticas.
- Acceder a secretos.
- Gestionar permisos globales.

## 6.3. Administrador

Puede utilizar todas las herramientas registradas, sujeto a:

- Policies.
- Confirmaciones.
- Auditoría.
- Validaciones.
- Restricciones de seguridad.

---

# 7. Herramientas de sistema y navegación

## 7.1. `system.capabilities`

**Objetivo:** informar qué puede hacer el Asistente RaR para el usuario actual.

**Roles:** cliente, trabajador, administrador.  
**Nivel:** 0.  
**Parámetros:** ninguno.

**Respuesta esperada:**

- Módulos disponibles.
- Acciones permitidas.
- Acciones no permitidas.
- Limitaciones relevantes.

---

## 7.2. `system.current_context`

**Objetivo:** devolver el contexto actual del usuario.

**Roles:** todos.  
**Nivel:** 0.

**Datos permitidos:**

- Nombre.
- Rol.
- Página actual.
- Identificador del recurso visible.
- Conversación actual.
- Idioma.
- Zona horaria.

No debe devolver secretos, tokens ni permisos internos sensibles.

---

## 7.3. `navigation.open`

**Objetivo:** navegar a una ruta interna permitida.

**Roles:** todos.  
**Nivel:** 0.

**Parámetros:**

```json
{
  "destination": "admin.products.index",
  "filters": {
    "status": "active"
  }
}
```

**Reglas:**

- Solo rutas incluidas en una lista blanca.
- No aceptar URL arbitraria.
- No permitir redirecciones externas.
- Verificar acceso a la ruta.

---

## 7.4. `assistant.help`

**Objetivo:** explicar cómo realizar una tarea dentro de la plataforma.

**Roles:** todos.  
**Nivel:** 0.

Ejemplos:

- Cómo agregar un producto.
- Cómo realizar una compra.
- Cómo consultar un pedido.
- Cómo usar un cupón.

---

# 8. Herramientas de productos

## 8.1. `product.search`

**Objetivo:** buscar productos utilizando filtros seguros.

**Roles:** todos.  
**Nivel:** 1.

**Parámetros:**

```json
{
  "query": "Samsung",
  "category_id": 3,
  "brand_id": 2,
  "min_price": 500,
  "max_price": 2000,
  "status": "active",
  "stock_status": "available",
  "limit": 10
}
```

**Reglas por rol:**

- Cliente: solo productos publicados y activos.
- Trabajador: según permisos asignados.
- Administrador: puede consultar todos los estados.

**Límite máximo:** 50 resultados por solicitud.

---

## 8.2. `product.get`

**Objetivo:** consultar el detalle de un producto.

**Roles:** todos.  
**Nivel:** 1.

**Parámetros:**

```json
{
  "product_id": 25
}
```

**Respuesta:**

- Nombre.
- Descripción.
- Precio.
- Precio anterior.
- Marca.
- Categoría.
- Variantes.
- Stock visible según rol.
- Imágenes.
- Video.
- Reseñas aprobadas.
- Estado.

---

## 8.3. `product.create`

**Objetivo:** crear un producto.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Parámetros mínimos:**

```json
{
  "name": "Samsung Galaxy A56",
  "slug": "samsung-galaxy-a56",
  "description": "Descripción del producto",
  "category_id": 3,
  "brand_id": 2,
  "price": 1899,
  "stock": 20,
  "status": "draft"
}
```

**Reglas:**

- Crear por defecto como borrador, salvo confirmación explícita para publicarlo.
- Validar categoría y marca existentes.
- No permitir precios negativos.
- No permitir stock negativo.
- No inventar imágenes.
- Solicitar datos faltantes.
- Registrar creador.

---

## 8.4. `product.update`

**Objetivo:** modificar campos permitidos de un producto.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Parámetros:**

```json
{
  "product_id": 25,
  "changes": {
    "name": "Nuevo nombre",
    "description": "Nueva descripción"
  }
}
```

**Reglas:**

- Lista blanca de campos editables.
- Mostrar resumen de cambios antes de confirmar.
- Registrar valores anteriores y posteriores.

---

## 8.5. `product.update_price`

**Objetivo:** actualizar el precio de un producto o variante.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Parámetros:**

```json
{
  "product_id": 25,
  "variant_id": null,
  "new_price": 1799,
  "reason": "Promoción de campaña"
}
```

**Reglas:**

- Mostrar precio actual y nuevo.
- Validar monto mayor o igual a cero.
- Registrar motivo.
- No aplicar cambios masivos mediante esta herramienta.

---

## 8.6. `product.update_stock`

**Objetivo:** ajustar el stock.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Parámetros:**

```json
{
  "product_id": 25,
  "variant_id": null,
  "operation": "increase",
  "quantity": 10,
  "reason": "Reabastecimiento"
}
```

**Operaciones permitidas:**

```text
increase
decrease
set
```

**Reglas:**

- Registrar movimiento de stock.
- Evitar stock negativo.
- Mostrar stock anterior y posterior.
- Requerir razón.

---

## 8.7. `product.change_status`

**Objetivo:** cambiar el estado de publicación.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Estados permitidos:**

```text
draft
active
inactive
archived
```

---

## 8.8. `product.duplicate`

**Objetivo:** duplicar un producto como borrador.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

**Reglas:**

- No duplicar ventas, reseñas ni movimientos.
- Crear un slug nuevo.
- Mantener el producto duplicado como borrador.

---

## 8.9. `product.delete`

**Objetivo:** eliminar o archivar un producto.

**Roles:** administrador.  
**Nivel:** 3.

**Reglas:**

- Preferir desactivación o borrado lógico.
- No eliminar si afecta pedidos históricos.
- Mostrar dependencias.
- Exigir doble confirmación.

---

## 8.10. `product.related`

**Objetivo:** buscar productos similares o complementarios.

**Roles:** todos.  
**Nivel:** 1.

**Uso principal:** Asistente RaR para clientes.

---

## 8.11. `product.compare`

**Objetivo:** comparar hasta cuatro productos de la misma categoría.

**Roles:** todos.  
**Nivel:** 1.

**Parámetros:**

```json
{
  "product_ids": [12, 15, 20]
}
```

---

# 9. Herramientas de variantes

## 9.1. `variant.search`

Consulta variantes por producto, talla, color, stock o estado.

## 9.2. `variant.create`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

Parámetros:

```json
{
  "product_id": 25,
  "size": "M",
  "color": "Negro",
  "sku": "RAR-25-M-NEGRO",
  "price": 1899,
  "stock": 10,
  "status": "active"
}
```

## 9.3. `variant.update`

Modifica talla, color, SKU, precio, stock, imagen o estado.

## 9.4. `variant.update_stock`

Actualiza stock de una variante y genera movimiento.

## 9.5. `variant.delete`

**Roles:** administrador.  
**Nivel:** 3.

No eliminar si está asociada a pedidos históricos.

---

# 10. Herramientas de categorías

## 10.1. `category.search`

**Roles:** todos.  
**Nivel:** 1.

## 10.2. `category.get`

Consulta detalle, jerarquía y cantidad de productos.

## 10.3. `category.create`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

Parámetros:

```json
{
  "name": "Celulares",
  "parent_id": null,
  "description": "Smartphones y accesorios",
  "status": "active"
}
```

## 10.4. `category.update`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

## 10.5. `category.change_status`

Activa o desactiva una categoría.

## 10.6. `category.delete`

**Roles:** administrador.  
**Nivel:** 3.

Debe comprobar productos y subcategorías asociadas.

---

# 11. Herramientas de marcas

## 11.1. `brand.search`

**Roles:** todos.  
**Nivel:** 1.

## 11.2. `brand.create`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

## 11.3. `brand.update`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

## 11.4. `brand.change_status`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

## 11.5. `brand.delete`

**Roles:** administrador.  
**Nivel:** 3.

---

# 12. Herramientas de inventario

## 12.1. `inventory.low_stock`

**Objetivo:** listar productos o variantes con stock bajo.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 1.

Parámetros:

```json
{
  "threshold": 5,
  "category_id": null,
  "limit": 50
}
```

## 12.2. `inventory.out_of_stock`

Lista productos agotados.

## 12.3. `inventory.movements`

Consulta historial de movimientos.

**Filtros:**

- Producto.
- Variante.
- Usuario.
- Tipo de movimiento.
- Fecha inicial.
- Fecha final.

## 12.4. `inventory.adjust`

Ajusta stock de un producto o variante.

**Nivel:** 2.

## 12.5. `inventory.set_minimum_stock`

Configura stock mínimo.

**Nivel:** 2.

## 12.6. `inventory.restock_suggestions`

Genera sugerencias basadas en reglas del sistema.

**Nivel:** 1.

La herramienta no debe inventar demanda futura. Debe mostrar claramente la base de la sugerencia.

---

# 13. Herramientas de pedidos

## 13.1. `order.search`

**Roles:**

- Cliente: solo pedidos propios.
- Trabajador: pedidos autorizados.
- Administrador: todos.

**Nivel:** 1.

**Filtros:**

```json
{
  "order_number": "RAR-000125",
  "status": "pending",
  "customer_id": null,
  "date_from": null,
  "date_to": null,
  "limit": 20
}
```

## 13.2. `order.get`

Consulta detalle del pedido.

**Seguridad:** validar propiedad o permiso.

## 13.3. `order.get_status`

Uso simplificado para clientes.

## 13.4. `order.update_status`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

Estados permitidos según flujo real del proyecto.

Ejemplo:

```text
pending
paid
processing
shipped
delivered
cancelled
```

**Reglas:**

- Respetar transiciones válidas.
- Registrar timeline.
- Enviar notificación si corresponde.
- No saltar estados prohibidos.

## 13.5. `order.cancel`

**Roles:**

- Cliente: solo pedidos propios y cancelables.
- Trabajador o administrador: según permisos.

**Nivel:** 2 o 3 según estado.

## 13.6. `order.add_internal_note`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

Las notas internas nunca deben mostrarse al cliente.

## 13.7. `order.timeline`

Devuelve la cronología visual del pedido.

## 13.8. `order.resend_confirmation`

Reenvía el correo de confirmación.

**Nivel:** 2.

## 13.9. `order.export`

Exporta pedidos autorizados a CSV.

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

---

# 14. Herramientas de ventas y dashboard

## 14.1. `sales.summary_today`

**Objetivo:** responder preguntas como “¿cuánto vendimos hoy?”.

**Roles:** trabajador con permiso de reportes, administrador.  
**Nivel:** 1.

**Respuesta:**

- Cantidad de pedidos.
- Pedidos pagados.
- Total bruto.
- Descuentos.
- Total neto.
- Ticket promedio.
- Comparación disponible con el período anterior.

## 14.2. `sales.summary_period`

Parámetros:

```json
{
  "date_from": "2026-07-01",
  "date_to": "2026-07-31",
  "group_by": "day"
}
```

## 14.3. `sales.top_products`

Lista productos más vendidos.

## 14.4. `sales.by_category`

Agrupa ventas por categoría.

## 14.5. `sales.by_product`

Detalle por producto.

## 14.6. `sales.pending_orders`

Resume pedidos pendientes.

## 14.7. `sales.average_ticket`

Calcula ticket promedio con datos del sistema.

## 14.8. `dashboard.summary`

Devuelve indicadores principales para el usuario autorizado.

---

# 15. Herramientas de clientes y usuarios

## 15.1. `customer.search`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 1.

**Datos sensibles:** devolver solo lo necesario.

## 15.2. `customer.get`

Consulta:

- Datos básicos.
- Pedidos.
- Puntos.
- Estado.
- Direcciones, solo si el permiso lo permite.

## 15.3. `customer.order_history`

Consulta historial de compras.

## 15.4. `customer.loyalty_balance`

- Cliente: saldo propio.
- Personal: cliente autorizado.

## 15.5. `user.search`

**Roles:** administrador.  
**Nivel:** 1.

## 15.6. `user.create_worker`

**Roles:** administrador.  
**Nivel:** 2.

## 15.7. `user.update`

**Roles:** administrador.  
**Nivel:** 2.

## 15.8. `user.change_role`

**Roles:** administrador.  
**Nivel:** 3.

No permitir que un administrador elimine accidentalmente su propio acceso sin una regla específica.

## 15.9. `user.block`

**Roles:** administrador.  
**Nivel:** 3.

## 15.10. `user.unblock`

**Roles:** administrador.  
**Nivel:** 2.

## 15.11. `user.reset_password_link`

Envía un enlace de restablecimiento. No genera ni revela contraseñas.

---

# 16. Herramientas de carrito

## 16.1. `cart.get`

Consulta el carrito del usuario actual.

## 16.2. `cart.add_item`

**Roles:** cliente autenticado o invitado con sesión.  
**Nivel:** 2.

Parámetros:

```json
{
  "product_id": 25,
  "variant_id": 81,
  "quantity": 2
}
```

**Reglas:**

- Validar stock.
- Validar estado.
- Usar precio calculado por el sistema.
- La IA no puede fijar el precio.

## 16.3. `cart.update_quantity`

Actualiza cantidad.

## 16.4. `cart.remove_item`

Elimina un artículo del carrito.

## 16.5. `cart.clear`

**Nivel:** 3.

Debe pedir doble confirmación.

## 16.6. `cart.apply_coupon`

Valida y aplica cupón.

## 16.7. `cart.remove_coupon`

Retira cupón.

## 16.8. `cart.estimate_totals`

Calcula subtotal, descuentos, envío y total.

---

# 17. Herramientas de lista de deseos

## 17.1. `wishlist.get`

Consulta la lista del usuario.

## 17.2. `wishlist.add`

Agrega producto.

## 17.3. `wishlist.remove`

Retira producto.

## 17.4. `wishlist.move_to_cart`

Mueve o copia un producto al carrito, previa validación de stock.

---

# 18. Herramientas de comparación

## 18.1. `compare.get`

Consulta el comparador actual.

## 18.2. `compare.add`

Agrega un producto si pertenece a la misma categoría y no supera el límite.

## 18.3. `compare.remove`

Elimina un producto.

## 18.4. `compare.clear`

Limpia el comparador.

---

# 19. Herramientas de direcciones

## 19.1. `address.list`

Cliente: solo direcciones propias.

## 19.2. `address.create`

**Nivel:** 2.

No debe exponer la dirección completa en logs.

## 19.3. `address.update`

Solo propietario o administrador autorizado.

## 19.4. `address.set_default`

Configura dirección predeterminada.

## 19.5. `address.delete`

**Nivel:** 2.

No eliminar una dirección necesaria para un pedido activo.

---

# 20. Herramientas de puntos de fidelización

## 20.1. `loyalty.get_balance`

Consulta saldo.

## 20.2. `loyalty.get_movements`

Consulta acumulaciones y canjes.

## 20.3. `loyalty.estimate_redemption`

Calcula cuánto puede canjear el cliente.

## 20.4. `loyalty.apply_redemption`

Aplica puntos al checkout.

**Nivel:** 2.

## 20.5. `loyalty.adjust_balance`

**Roles:** administrador.  
**Nivel:** 3.

Requiere motivo y auditoría completa.

---

# 21. Herramientas de cupones

## 21.1. `coupon.validate`

**Roles:** todos.  
**Nivel:** 1.

## 21.2. `coupon.search`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 1.

## 21.3. `coupon.create`

**Nivel:** 2.

Parámetros:

```json
{
  "code": "CYBER20",
  "type": "percentage",
  "value": 20,
  "minimum_amount": 100,
  "starts_at": "2026-07-28 00:00:00",
  "expires_at": "2026-07-31 23:59:59",
  "usage_limit": 100,
  "status": "active"
}
```

## 21.4. `coupon.update`

**Nivel:** 2.

## 21.5. `coupon.activate`

**Nivel:** 2.

## 21.6. `coupon.deactivate`

**Nivel:** 2.

## 21.7. `coupon.delete`

**Roles:** administrador.  
**Nivel:** 3.

Preferir desactivación.

---

# 22. Herramientas de banners

## 22.1. `banner.search`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 1.

## 22.2. `banner.get`

Consulta título, posición, imagen, enlace, fechas y estado.

## 22.3. `banner.create`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

Parámetros:

```json
{
  "title": "Cyber Days",
  "subtitle": "Hasta 20% de descuento",
  "image_path": null,
  "link": "/ofertas",
  "position": 1,
  "starts_at": "2026-07-28 00:00:00",
  "ends_at": "2026-07-31 23:59:59",
  "status": "draft"
}
```

**Reglas:**

- La IA no puede inventar ni generar una ruta de archivo.
- Si falta una imagen, debe solicitar que el usuario la cargue.
- Validar enlace interno o dominio permitido.
- Crear como borrador por defecto.

## 22.4. `banner.update`

**Nivel:** 2.

## 22.5. `banner.activate`

**Nivel:** 2.

## 22.6. `banner.deactivate`

**Nivel:** 2.

## 22.7. `banner.reorder`

**Nivel:** 2.

## 22.8. `banner.delete`

**Roles:** administrador.  
**Nivel:** 3.

---

# 23. Herramientas de reseñas

## 23.1. `review.search`

- Cliente: propias o aprobadas.
- Personal: según permisos.

## 23.2. `review.create`

Cliente autenticado y elegible.

## 23.3. `review.approve`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 2.

## 23.4. `review.reject`

**Nivel:** 2.

Debe registrar razón.

## 23.5. `review.delete`

**Roles:** administrador.  
**Nivel:** 3.

## 23.6. `review.summary`

Resume calificaciones y opiniones aprobadas sin inventar conclusiones.

---

# 24. Herramientas de newsletter

## 24.1. `newsletter.subscribe`

Cliente o invitado.

## 24.2. `newsletter.unsubscribe`

Valida identidad o token.

## 24.3. `newsletter.subscribers`

**Roles:** trabajador autorizado, administrador.  
**Nivel:** 1.

## 24.4. `newsletter.export`

**Nivel:** 2.

## 24.5. `newsletter.send_campaign`

**Roles:** administrador.  
**Nivel:** 3.

Debe mostrar:

- Asunto.
- Audiencia.
- Cantidad estimada.
- Contenido.
- Fecha de envío.

No permitir envío sin doble confirmación.

---

# 25. Herramientas de chat de soporte

## 25.1. `support.conversations`

Personal autorizado.

## 25.2. `support.get_conversation`

Consulta mensajes permitidos.

## 25.3. `support.reply`

**Nivel:** 2.

## 25.4. `support.close_conversation`

**Nivel:** 2.

## 25.5. `support.reopen_conversation`

**Nivel:** 2.

## 25.6. `support.escalate`

Escala a personal humano.

El Asistente RaR debe recomendar escalamiento cuando no pueda resolver una solicitud.

---

# 26. Herramientas de FAQs y beneficios

## 26.1. `faq.search`

Pública.

## 26.2. `faq.create`

Personal autorizado. Nivel 2.

## 26.3. `faq.update`

Nivel 2.

## 26.4. `faq.delete`

Administrador. Nivel 3.

## 26.5. `benefit.search`

Pública.

## 26.6. `benefit.create`

Nivel 2.

## 26.7. `benefit.update`

Nivel 2.

## 26.8. `benefit.delete`

Administrador. Nivel 3.

---

# 27. Herramientas de configuración

## 27.1. `setting.get_public`

Consulta configuración pública:

- Envíos.
- Redes sociales.
- Horarios.
- Políticas.
- Métodos de pago visibles.

## 27.2. `setting.get_admin`

**Roles:** administrador.  
**Nivel:** 1.

No devolver:

- API keys.
- Contraseñas.
- Tokens.
- Secretos.
- Credenciales SMTP.

## 27.3. `setting.update`

**Roles:** administrador.  
**Nivel:** 2 o 3.

Solo claves incluidas en una lista blanca.

Ejemplos:

```text
shipping.default_cost
loyalty.points_per_currency
social.facebook_url
store.contact_phone
store.business_hours
```

## 27.4. `setting.test_email`

Envía un correo de prueba sin revelar credenciales.

---

# 28. Herramientas de reportes

## 28.1. `report.sales`

Genera reporte de ventas por período.

## 28.2. `report.products`

Reporte de productos más vendidos o sin ventas.

## 28.3. `report.inventory`

Reporte de stock y movimientos.

## 28.4. `report.customers`

Reporte agregado de clientes.

## 28.5. `report.orders`

Reporte de pedidos.

## 28.6. `report.newsletter`

Reporte de suscriptores.

## 28.7. `report.export_csv`

**Nivel:** 2.

Solo exporta un reporte previamente autorizado.

## 28.8. `report.generate_summary`

Genera resumen textual basado exclusivamente en datos del reporte.

---

# 29. Herramientas de recomendaciones para clientes

## 29.1. `recommendation.by_budget`

Parámetros:

```json
{
  "max_budget": 2000,
  "category_id": 3,
  "preferences": ["batería", "cámara"]
}
```

## 29.2. `recommendation.by_need`

Busca según necesidad:

- Programación.
- Estudios.
- Fotografía.
- Gaming.
- Trabajo.
- Uso diario.

La recomendación debe basarse en atributos reales del catálogo.

## 29.3. `recommendation.complementary`

Sugiere accesorios compatibles.

## 29.4. `recommendation.similar`

Sugiere productos similares.

## 29.5. `recommendation.recently_viewed`

Consulta historial autorizado del usuario.

## 29.6. `recommendation.personalized`

Usa únicamente:

- Compras del usuario.
- Favoritos.
- Vistas.
- Preferencias explícitas.
- Disponibilidad real.

No debe inferir atributos sensibles.

---

# 30. Herramientas de promociones

## 30.1. `promotion.active`

Lista promociones vigentes.

## 30.2. `promotion.create`

**Roles:** administrador.  
**Nivel:** 2.

## 30.3. `promotion.update`

Nivel 2.

## 30.4. `promotion.activate`

Nivel 2.

## 30.5. `promotion.deactivate`

Nivel 2.

## 30.6. `promotion.delete`

Nivel 3.

---

# 31. Herramientas de notificaciones

## 31.1. `notification.list`

Usuario actual.

## 31.2. `notification.mark_read`

Usuario actual.

## 31.3. `notification.mark_all_read`

Nivel 2.

## 31.4. `notification.send_user`

Personal autorizado. Nivel 2.

## 31.5. `notification.send_segment`

Administrador. Nivel 3.

---

# 32. Herramientas del propio Asistente RaR

## 32.1. `assistant.conversation.create`

Crea una conversación para el usuario actual.

## 32.2. `assistant.conversation.list`

Lista solo conversaciones propias.

## 32.3. `assistant.conversation.rename`

Renombra una conversación propia.

## 32.4. `assistant.conversation.delete`

Nivel 3.

Debe explicar que eliminará mensajes y memoria asociada según política.

## 32.5. `assistant.memory.list`

Muestra recuerdos permitidos del usuario.

## 32.6. `assistant.memory.save`

Guarda una preferencia no sensible cuando el usuario lo solicita o lo autoriza.

## 32.7. `assistant.memory.delete`

Elimina un recuerdo propio.

## 32.8. `assistant.feedback.create`

Registra valoración positiva o negativa.

## 32.9. `assistant.cancel_pending_action`

Cancela una acción pendiente de confirmación.

---

# 33. Herramientas prohibidas

No deben existir herramientas con capacidades como:

```text
database.query
database.execute
sql.run
php.eval
shell.execute
artisan.run
filesystem.write
filesystem.delete
composer.install
npm.install
migration.create_dynamic
route.create_dynamic
controller.modify
permission.bypass
impersonate.user
secret.read
env.read
api_key.show
```

La IA tampoco debe poder:

- Leer `.env`.
- Mostrar claves de Groq.
- Acceder a tokens de Socialite.
- Acceder a credenciales de Culqi.
- Acceder a contraseñas.
- Descargar toda la base de datos.
- Consultar información de otros usuarios sin autorización.
- Cambiar su propia lista de herramientas.
- Cambiar Policies o Gates.
- Crear herramientas nuevas durante la ejecución.

---

# 34. Confirmaciones

## 34.1. Confirmación simple

Ejemplo:

```text
Se modificará el precio del producto Samsung Galaxy A56.

Precio actual: S/ 1,899
Nuevo precio: S/ 1,799

Motivo: Promoción de campaña

[Cancelar] [Confirmar cambio]
```

## 34.2. Doble confirmación

Ejemplo:

```text
Esta acción eliminará el banner “Cyber Days”.

La eliminación puede afectar el carrusel público.

Para continuar, escribe: ELIMINAR BANNER
```

## 34.3. Expiración

Una confirmación debe expirar después de un período configurable, por ejemplo diez minutos.

Los parámetros confirmados deben firmarse o almacenarse en servidor. No se debe confiar en parámetros reenviados libremente por el navegador.

---

# 35. Manejo de datos faltantes

Cuando falten parámetros, el Asistente RaR debe preguntar únicamente por los datos necesarios.

Ejemplo:

```text
Para crear el producto necesito:

1. Categoría.
2. Marca.
3. Precio.
4. Stock inicial.

Puedes enviarme esos datos en un solo mensaje.
```

No debe ejecutar la herramienta con datos inventados.

---

# 36. Resolución de referencias ambiguas

Cuando el usuario diga:

```text
Cambia el precio del Samsung.
```

Y existan varios productos, el asistente debe usar `product.search` y pedir selección.

Ejemplo:

```text
Encontré tres productos:

1. Samsung Galaxy A36.
2. Samsung Galaxy A56.
3. Samsung Galaxy S25 Ultra.

¿Cuál deseas modificar?
```

No debe seleccionar uno automáticamente.

---

# 37. Tool Registry

El registro debe almacenar como mínimo:

```php
[
    'name' => 'product.search',
    'description' => 'Busca productos usando filtros autorizados.',
    'handler' => SearchProductTool::class,
    'roles' => ['cliente', 'trabajador', 'admin'],
    'permissions' => ['products.view'],
    'confirmation_level' => 1,
    'enabled' => true,
]
```

## 37.1. Filtrado

Antes de enviar herramientas a Groq:

```text
Todas las herramientas registradas
           ↓
Herramientas habilitadas
           ↓
Herramientas compatibles con el rol
           ↓
Herramientas autorizadas por permisos
           ↓
Herramientas válidas en el contexto actual
           ↓
Herramientas enviadas al modelo
```

---

# 38. Tool Executor

El ejecutor debe:

1. Verificar autenticación.
2. Verificar estado del usuario.
3. Resolver la herramienta.
4. Validar que esté habilitada.
5. Validar rol.
6. Validar permiso.
7. Validar contexto.
8. Validar esquema.
9. Sanitizar parámetros.
10. Evaluar confirmación.
11. Ejecutar dentro de transacción cuando corresponda.
12. Registrar auditoría.
13. Manejar errores.
14. Devolver `ToolResult`.

---

# 39. Códigos de error estándar

```text
UNAUTHENTICATED
FORBIDDEN
TOOL_NOT_FOUND
TOOL_DISABLED
INVALID_ARGUMENTS
MISSING_ARGUMENTS
RESOURCE_NOT_FOUND
AMBIGUOUS_RESOURCE
CONFIRMATION_REQUIRED
CONFIRMATION_EXPIRED
CONFIRMATION_INVALID
BUSINESS_RULE_VIOLATION
OUT_OF_STOCK
RATE_LIMITED
PROVIDER_UNAVAILABLE
TIMEOUT
CONFLICT
INTERNAL_ERROR
```

---

# 40. Rate limiting

Aplicar límites independientes para:

- Mensajes al asistente.
- Consultas.
- Acciones de escritura.
- Exportaciones.
- Campañas.
- Confirmaciones fallidas.
- Uso por IP.
- Uso por usuario.

Las herramientas destructivas deben tener límites más estrictos.

---

# 41. Transacciones e idempotencia

Las acciones de escritura deben utilizar transacciones cuando afecten varios recursos.

Herramientas que puedan repetirse por errores de red deben aceptar una clave de idempotencia.

Ejemplos:

- Crear pedido.
- Aplicar puntos.
- Enviar campaña.
- Ajustar stock.
- Crear producto.
- Actualizar estado con notificación.

---

# 42. Registro de auditoría

Cada registro debe incluir:

```text
id
user_id
conversation_id
message_id
tool_name
confirmation_level
arguments_hash
safe_arguments
resource_type
resource_id
previous_values
new_values
status
error_code
ip_address
user_agent
started_at
completed_at
duration_ms
created_at
```

Los secretos y datos personales sensibles deben ocultarse.

---

# 43. Privacidad entre usuarios

Las herramientas deben recibir el usuario autenticado desde el servidor.

Nunca aceptar `user_id` libremente para acciones del cliente.

Ejemplo correcto:

```php
$context->user()->orders()
```

Ejemplo incorrecto:

```php
Order::where('user_id', $arguments['user_id'])
```

salvo herramientas administrativas explícitas y autorizadas.

---

# 44. Integración con Groq

Groq recibe únicamente:

- Prompt del sistema.
- Contexto mínimo necesario.
- Historial resumido.
- Esquemas de herramientas autorizadas.
- Mensaje actual.

Groq no debe recibir:

- Claves API.
- Contraseñas.
- Datos completos de todos los usuarios.
- Base de datos completa.
- Información no relacionada.
- Herramientas que el usuario no puede usar.

---

# 45. Orden recomendado de implementación

## Etapa 1: consultas seguras

1. `system.capabilities`
2. `product.search`
3. `product.get`
4. `category.search`
5. `brand.search`
6. `order.get_status`
7. `loyalty.get_balance`
8. `setting.get_public`

## Etapa 2: cliente

9. `cart.get`
10. `cart.add_item`
11. `cart.update_quantity`
12. `cart.remove_item`
13. `wishlist.get`
14. `wishlist.add`
15. `wishlist.remove`
16. `product.compare`
17. `recommendation.by_budget`
18. `recommendation.by_need`

## Etapa 3: administración de lectura

19. `dashboard.summary`
20. `sales.summary_today`
21. `sales.summary_period`
22. `inventory.low_stock`
23. `inventory.movements`
24. `order.search`
25. `customer.search`

## Etapa 4: escritura controlada

26. `product.create`
27. `product.update`
28. `product.update_price`
29. `product.update_stock`
30. `order.update_status`
31. `banner.create`
32. `banner.update`
33. `coupon.create`

## Etapa 5: acciones críticas

34. Eliminaciones.
35. Acciones masivas.
36. Campañas de newsletter.
37. Cambio de roles.
38. Ajustes manuales de puntos.
39. Exportaciones sensibles.

---

# 46. Pruebas mínimas por herramienta

Cada herramienta debe incluir:

- Prueba de usuario no autenticado.
- Prueba de rol no autorizado.
- Prueba de permiso faltante.
- Prueba de parámetros válidos.
- Prueba de parámetros inválidos.
- Prueba de recurso inexistente.
- Prueba de confirmación.
- Prueba de auditoría.
- Prueba de aislamiento entre usuarios.
- Prueba de regla de negocio.
- Prueba de error inesperado.
- Prueba de respuesta estructurada.

---

# 47. Criterios de aceptación

Una herramienta se considera terminada cuando:

- Tiene nombre único.
- Tiene descripción clara.
- Tiene esquema validado.
- Tiene Policy o Gate.
- Tiene rol definido.
- Tiene nivel de confirmación.
- Usa un Service existente.
- No ejecuta SQL directo.
- Tiene logs.
- Tiene auditoría.
- Oculta datos sensibles.
- Devuelve `ToolResult`.
- Tiene pruebas.
- Está documentada.
- Puede deshabilitarse por configuración.

---

# 48. Ejemplo completo de ejecución

Usuario administrador:

```text
Cambia el precio del Samsung Galaxy A56 a S/ 1,799.
```

Flujo:

```text
1. El modelo identifica product.update_price.
2. Laravel busca el producto.
3. Laravel valida permisos.
4. Laravel obtiene el precio actual.
5. Asistente muestra:
   - Producto.
   - Precio actual.
   - Nuevo precio.
6. El usuario confirma.
7. Laravel verifica que la confirmación no haya expirado.
8. ToolExecutor llama ProductService.
9. ProductService actualiza el precio.
10. Se registra auditoría.
11. Se devuelve el resultado.
12. El asistente informa el cambio.
```

Respuesta:

```text
Precio actualizado correctamente.

Producto: Samsung Galaxy A56
Precio anterior: S/ 1,899
Precio nuevo: S/ 1,799
Responsable: Ramiro
```

---

# 49. Ejemplo de denegación

Usuario cliente:

```text
¿Cuánto vendieron hoy?
```

Respuesta:

```text
Esa información es exclusiva del personal autorizado de Negocios RaR.

Puedo ayudarte a buscar productos, revisar tus pedidos, consultar tus puntos o conocer las promociones disponibles.
```

La herramienta `sales.summary_today` no debe enviarse al modelo para ese usuario.

---

# 50. Ejemplo de funcionalidad inexistente

Usuario:

```text
Crea una aplicación móvil para la tienda.
```

Respuesta:

```text
Esa funcionalidad no forma parte de las herramientas disponibles del Asistente RaR.

El asistente puede operar únicamente sobre los módulos ya implementados en Negocios RaR.
```

---

# 51. Backlog futuro

Estas herramientas no forman parte de la primera versión:

- Facturación electrónica.
- Gestión de proveedores.
- Órdenes de compra.
- Multiempresa.
- Multisucursal.
- Marketplace.
- Automatizaciones autónomas.
- Predicción avanzada de demanda.
- Generación automática de imágenes.
- Control por voz.
- Aplicación móvil.

Solo deben agregarse cuando el módulo correspondiente exista oficialmente en Negocios RaR.

---

# 52. Regla final

> El Asistente RaR nunca debe tener más poder que el usuario autenticado, nunca debe conocer más información de la necesaria y nunca debe ejecutar una acción que Laravel no haya validado explícitamente.

Este documento constituye el catálogo oficial inicial de herramientas del Asistente RaR. Toda herramienta nueva deberá documentarse aquí antes de incorporarse al sistema.
