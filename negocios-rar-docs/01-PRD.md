# PRD — Product Requirements Document
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 1.4
**Estado:** En desarrollo (MVP)
**Última actualización:** julio 2026

---

## 1. Resumen ejecutivo

Negocios RaR es una plataforma de e-commerce multicategoría (similar en concepto a Ripley o Falabella, a menor escala) que permite a un negocio vender cualquier tipo de producto en línea, con dos módulos principales:

1. **Tienda online (storefront):** cara pública donde los clientes navegan, buscan, filtran y compran productos.
2. **Panel administrativo (back office):** donde el equipo del negocio gestiona catálogo, pedidos, usuarios y soporte al cliente.

El objetivo del MVP es tener un flujo de compra completo y funcional (descubrimiento → carrito → checkout → seguimiento de pedido) junto con las herramientas mínimas para que el negocio opere sin depender de otra plataforma.

---

## 2. Problema a resolver

- El negocio no tiene presencia de venta online propia; depende de redes sociales o marketplaces de terceros (con comisiones y sin control de marca).
- No existe un sistema centralizado para gestionar catálogo, stock, pedidos y atención al cliente.
- Se necesita diferenciar roles internos (quién administra el negocio vs. quién solo opera el día a día) sin dar acceso total a todo el personal.

---

## 3. Objetivos del producto

| Objetivo | Métrica de éxito (referencial) |
|---|---|
| Permitir la venta online de cualquier producto | Catálogo activo con categorías ilimitadas |
| Reducir fricción en el proceso de compra | Checkout en ≤ 3 pasos |
| Dar autonomía operativa al negocio | Admin puede publicar un producto sin ayuda técnica |
| Habilitar atención al cliente directa | Chat de soporte funcional post-login |
| Separar responsabilidades internas | Roles admin/trabajador con permisos distintos |

---

## 4. Usuarios y roles

### 4.1 Cliente (público / registrado)
Persona que navega la tienda, compra productos y da seguimiento a sus pedidos.
- Puede navegar sin cuenta (modo invitado) y agregar productos al carrito.
- Debe registrarse/iniciar sesión para: finalizar compra, ver sus pedidos, usar el chat de soporte.

### 4.2 Trabajador
Personal operativo del negocio con acceso al panel administrativo.
- Gestiona productos, categorías, pedidos y mensajes de soporte.
- **No** puede gestionar usuarios ni cambiar roles de otras personas.

### 4.3 Administrador
Dueño(a) o encargado(a) principal del negocio.
- Tiene todos los permisos de "Trabajador".
- Además gestiona usuarios: crear cuentas de trabajadores, cambiar roles, activar/desactivar cuentas.

---

## 5. Alcance funcional (MVP)

### 5.1 Módulo Tienda Online (público)
- **Home:** carrusel de promociones/novedades, categorías destacadas, productos destacados y recién llegados, sección de beneficios (envío, pago seguro, devoluciones, soporte).
- **Navegación:** menú fijo con Inicio / Productos / Acerca de / Contáctanos / Iniciar sesión.
- **Catálogo de productos:** listado con filtros por categoría, marca y rango de precio; ordenamiento por precio y nombre; búsqueda por texto.
- **Ficha de producto:** galería de imágenes, precio (con precio tachado si hay descuento), stock disponible, atributos (marca, garantía, origen, etc.), reseñas y valoraciones de clientes, productos relacionados.
- **Cupones y descuentos:** aplicación de cupones promocionales directamente desde el checkout con validación en vivo vía AJAX; soporta descuento porcentual o monto fijo, con o sin categoría específica, tope máximo, compra mínima y límite de usos.
- **Lista de deseos (wishlist):** disponible para usuarios logueados; agregar/quitar productos con un solo clic (ícono corazón en tarjetas y ficha de producto), página dedicada con acceso rápido a agregar al carrito.
- **Carrito de compras:** disponible para invitados y usuarios logueados; agregar, actualizar cantidad, eliminar ítems; cálculo de envío gratis sobre monto mínimo.
- **Checkout:** requiere sesión iniciada; captura dirección de envío, teléfono, notas y método de pago; genera número de pedido único.
- **Mis pedidos:** historial de compras del cliente con estado actual.
- **Autenticación:** registro, inicio de sesión, recuperación de contraseña por correo. Durante el registro, se solicita consentimiento explícito para el tratamiento de datos personales (checkbox obligatorio que acepta la Política de Privacidad y Términos y Condiciones), registrando la fecha y versión aceptada en la base de datos.
- **Contáctanos / Chat de soporte:** solo visible y funcional si el usuario inició sesión; si no, se le invita a registrarse/loguearse.
- **Páginas legales:** `/politica-de-privacidad` y `/terminos-y-condiciones` (públicas, sin login).
- **Acerca de:** información institucional del negocio (misión, visión, valores, cifras).

### 5.2 Módulo Administrativo (privado, roles admin/trabajador)
- **Dashboard:** ventas totales, pedidos del día, total de productos, total de clientes, cupones activos vigentes, alerta de stock bajo, últimos pedidos, productos más vendidos.
- **Cupones:** CRUD completo de cupones promocionales con código, tipo (porcentaje/monto fijo), categoría opcional, compra mínima, tope de descuento, límites de uso, fechas de vigencia y estado activo/inactivo.
- **Productos:** CRUD completo (crear, editar, eliminar, activar/desactivar, marcar como destacado), carga de imagen principal y galería.
- **Categorías:** CRUD, activar/desactivar.
- **Pedidos / Ventas:** listado filtrable por estado, detalle de pedido, cambio de estado (pendiente → pagado → enviado → entregado / cancelado).
- **Mensajes:** bandeja de conversaciones de clientes, respuesta desde el panel.
- **Usuarios** (solo Admin): crear cuentas, asignar rol (admin/trabajador/cliente), activar/desactivar, eliminar.

### 5.3 Fuera de alcance (MVP)
- ~~Pasarela de pago real~~ ✅ Implementado (Culqi integrado en modo test, listo para producción cambiando llaves).
- ~~Notificaciones automáticas por correo/SMS sobre cambios de estado de pedido.~~ ✅ Implementado (correo). SMS pendiente.
- ~~Reseñas y calificaciones de productos por clientes.~~ ✅ Implementado (sistema de reseñas con valoración 1-5, comentario opcional, moderación desde admin).
- ~~Lista de deseos (wishlist).~~ ✅ Implementado (guardar productos con un clic, página dedicada, contador en navbar).
- Reportes exportables (Excel/PDF) de ventas.
- Multi-idioma / multi-moneda.
- App móvil nativa.

---

## 6. Requisitos no funcionales

| Categoría | Requisito |
|---|---|
| Rendimiento | Listado de productos con paginación (12 por página) para no degradar tiempos de carga |
| Seguridad | Contraseñas hasheadas, protección CSRF en formularios, control de acceso por rol vía middleware |
| Usabilidad | Diseño responsive (mobile-first en catálogo y carrito) |
| Disponibilidad | Aplicación monolítica desplegable en un solo servidor (sin dependencias externas obligatorias más allá de PostgreSQL) |
| Mantenibilidad | Código organizado por convención MVC de Laravel, nombres de rutas y vistas descriptivos |
| Extensibilidad | Estructura preparada para agregar pasarela de pago, notificaciones y reportes sin reescribir el core |

---

## 7. Supuestos y restricciones

- El negocio empieza con catálogo y operación simple (un solo almacén/stock, sin variantes complejas tipo talla+color combinadas en SKUs separados; los atributos son campos libres clave-valor).
- El pago se gestiona inicialmente fuera de línea (contraentrega/transferencia) o se integrará una pasarela en una fase posterior.
- No hay app multi-tienda ni multi-tenant: es una sola tienda, una sola base de datos.

---

## 8. Roadmap sugerido (post-MVP)

1. Integración de pasarela de pago real (Culqi / Mercado Pago / Stripe).
2. Notificaciones automáticas (correo/WhatsApp) por cambio de estado de pedido.
3. ~~Reseñas y calificaciones de productos.~~ ✅ Implementado
4. ~~Lista de deseos (wishlist).~~ ✅ Implementado
5. Reportes de ventas exportables.
6. ~~Programa de cupones/descuentos.~~ ✅ Implementado
7. Panel de analítica más avanzado (embudo de conversión, abandono de carrito).

---

## 9. Glosario

- **SKU:** código único de un producto.
- **Storefront:** cara pública de la tienda.
- **Back office:** panel administrativo interno.
- **Checkout:** proceso de finalizar una compra.
