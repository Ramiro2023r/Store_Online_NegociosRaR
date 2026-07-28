# TRD — Technical Requirements Document
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 1.3
**Última actualización:** julio 2026

---

## 1. Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Lenguaje backend | PHP | ^8.2 |
| Framework | Laravel | ^12.0 |
| Base de datos | PostgreSQL | ≥ 13 |
| Frontend | Blade + Tailwind CSS (vía CDN) + Alpine.js | — |
| Autenticación | Sistema propio (controllers custom sobre `Illuminate\Auth`), sin paquetes de scaffolding (no Breeze/Jetstream) | — |
| Almacenamiento de archivos | Filesystem local (`storage/app/public`, symlink a `public/storage`) | — |
| Gestión de dependencias | Composer (PHP) | — |
| Servidor de desarrollo | `php artisan serve` | — |

**Nota de decisión:** se optó por Tailwind vía CDN (sin pipeline de Vite/npm) para minimizar fricción de instalación. Si el proyecto escala, se recomienda migrar a Tailwind compilado localmente (purga de clases, mejor rendimiento en producción).

---

## 2. Arquitectura general

Aplicación **monolítica MVC** (patrón nativo de Laravel):

```
Cliente (navegador)
   │  HTTP
   ▼
routes/web.php  →  Controllers  →  Models (Eloquent)  →  PostgreSQL
   │
   ▼
Blade Views (resources/views) — Tailwind CDN + Alpine.js para interactividad ligera
```

No hay API REST/GraphQL separada en el MVP: todo se sirve mediante rutas web con Blade. Si en el futuro se requiere una app móvil, se recomienda exponer una capa API (`routes/api.php`) reutilizando los mismos Models y lógica de negocio (extraída a Services/Actions).

---

## 3. Modelo de datos

### 3.1 Entidades principales

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (clientes, trabajadores, admins) |
| `categories` | Categorías de producto |
| `products` | Catálogo de productos |
| `product_images` | Galería adicional por producto (imagen principal vive en `products.main_image`) |
| `reviews` | Reseñas y valoraciones de productos (1-5 estrellas) escritas por clientes verificados |
| `wishlists` | Lista de deseos de clientes — relación N:N entre users y products con índice único |
| `coupons` | Cupones de descuento promocionales con reglas de aplicación (tipo, valor, categoría, fechas, límites) |
| `coupon_uses` | Registro de uso de cupones por pedido (para control de límites por usuario) |
| `carts` / `cart_items` | Carrito de compras (asociado a `user_id` o `session_id` para invitados) |
| `orders` / `order_items` | Pedidos y sus líneas de detalle (snapshot de precio/nombre al momento de compra) |
| `conversations` / `messages` | Hilos de chat de soporte por cliente |

### 3.2 Campos clave por tabla

**users**
```
id, name, email, password, role (admin|trabajador|cliente, default: cliente),
phone, address, active (boolean, default true), accepted_terms_at (timestamp,
nullable — fecha de aceptación de términos), accepted_terms_version (string,
nullable — versión de términos aceptados), email_verified_at,
remember_token, timestamps
```

**categories**
```
id, name, slug (unique), icon, description, active, timestamps
```

**products**
```
id, category_id (FK), name, slug (unique), description, price (decimal 10,2),
compare_price (decimal 10,2, nullable — precio tachado), sku (unique, nullable),
stock (int), brand, attributes (json — pares clave-valor libres),
main_image, featured (bool), active (bool), rating (decimal 3,2), timestamps
```

**product_images**
```
id, product_id (FK), path, sort_order, timestamps
```

**reviews**
```
id, product_id (FK), user_id (FK), rating (integer, 1-5), comment (text nullable),
approved (boolean, default false), created_at, updated_at,
unique(product_id, user_id)
```

**wishlists**
```
id, user_id (FK), product_id (FK), created_at, updated_at,
unique(user_id, product_id)
```

**coupons**
```
id, code (string unique — siempre mayúsculas),
type (percentage|fixed), value (decimal 10,2),
category_id (FK nullable — si no null, solo aplica a productos de esa categoría),
min_purchase (decimal 10,2, default 0),
max_discount (decimal 10,2 nullable — tope para percentage),
usage_limit (int nullable), usage_count (int default 0),
usage_limit_per_user (int nullable default 1),
starts_at (timestamp nullable), expires_at (timestamp nullable),
active (bool default true), timestamps
```

**coupon_uses**
```
id, coupon_id (FK), user_id (FK), order_id (FK),
discount_amount (decimal 10,2 — descuento real aplicado en ese pedido), timestamps
```

**carts / cart_items**
```
carts: id, user_id (FK nullable), session_id (nullable, para invitados), timestamps
cart_items: id, cart_id (FK), product_id (FK), quantity, unit_price, timestamps
```

**orders / order_items**
```
orders: id, order_number (unique, formato RAR-XXXXXXXX), user_id (FK),
subtotal, shipping_cost, total, status (pendiente|pagado|enviado|entregado|cancelado),
payment_method (tarjeta|transferencia|contraentrega), shipping_address,
shipping_city, shipping_phone, notes, culqi_charge_id (nullable — ID en Culqi),
payment_status (pendiente|pagado|fallido), paid_at (timestamp nullable), timestamps

order_items: id, order_id (FK), product_id (FK nullable — se conserva el pedido
aunque el producto se elimine), product_name (snapshot), unit_price (snapshot),
quantity, total, timestamps
```

**conversations / messages**
```
conversations: id, user_id (FK), subject, status (abierta|cerrada), timestamps
messages: id, conversation_id (FK), user_id (FK — quien envía), is_staff (bool),
body, read (bool), timestamps
```

### 3.3 Relaciones (resumen Eloquent)

- `User` 1—N `Order`, 1—N `Conversation`, 1—N `Wishlist`
- `Category` 1—N `Product`
- `Product` 1—N `ProductImage`; `Product` 1—N `Review`; `User` 1—N `Review`
- `Cart` 1—N `CartItem`; `CartItem` N—1 `Product`
- `Wishlist` N—1 `User` y N—1 `Product` (tabla pivot con user_id + product_id unique)
- `Coupon` N—1 `Category` (nullable); `Coupon` 1—N `CouponUse`
- `CouponUse` N—1 `Coupon`, N—1 `User`, N—1 `Order`
- `Order` 1—N `OrderItem`; `OrderItem` N—1 `Product` (nullable)
- `Conversation` 1—N `Message`

### 3.4 Motor de base de datos

PostgreSQL. Puntos específicos del driver usados en el código:
- Búsquedas insensibles a mayúsculas con `ilike` (no `like`, que es case-sensitive en Postgres).
- `search_path` por defecto en `public`.

---

## 4. Control de acceso y seguridad

### 4.1 Roles y middleware
- Middleware custom `role:admin,trabajador` protege todo el prefijo `/admin`.
- Sub-grupo `role:admin` protege exclusivamente la gestión de usuarios (`/admin/users/*`).
- Lógica de verificación centralizada en `User::isAdmin()`, `User::isTrabajador()`, `User::isStaff()`.

### 4.2 Autenticación
- Login/registro/logout implementados con `Illuminate\Support\Facades\Auth` (sin paquete externo).
- Recuperación de contraseña usa el `Password` broker nativo de Laravel (tabla `password_reset_tokens`, ya incluida en el esqueleto base).
- Contraseñas hasheadas automáticamente vía cast `'password' => 'hashed'` en el modelo `User`.

### 4.3 Protección de formularios
- Todos los formularios POST/PUT/PATCH/DELETE usan `@csrf` (y `@method` para verbos no soportados nativamente por HTML).

### 4.4 Pendiente de reforzar (no incluido en MVP)
- Rate limiting explícito en login/registro (Laravel trae throttle básico, pero no está afinado).
- Verificación de email obligatoria antes de comprar.
- Sanitización adicional de `attributes` (JSON libre) si se expone a inputs no controlados.
- Auditoría/logs de cambios administrativos (quién cambió el estado de un pedido, etc.).

---

## 5. Rutas (resumen por bloque)

| Bloque | Prefijo | Middleware | Ejemplos |
|---|---|---|---|
| Público | `/` | — | `/`, `/productos`, `/productos/{slug}`, `/acerca-de` |
| Carrito | `/carrito` | — (funciona con invitados vía `session_id`) | agregar, actualizar, eliminar ítem |
| Auth | `/login`, `/registro`, `/olvide-password`, `/reset-password` | `guest` | — |
| Cliente autenticado | `/checkout`, `/mis-pedidos`, `/contactanos`, `/productos/*/resenas`, `/mi-lista-de-deseos`, `/lista-de-deseos/*`, `/checkout/aplicar-cupon`, `/checkout/quitar-cupon` | `auth` | crear/eliminar reseña, toggle/ver wishlist, aplicar/quitar cupón (AJAX) |
| Admin | `/admin/*` | `auth`, `role:admin,trabajador` | dashboard, productos, categorías, pedidos, mensajes, reseñas, cupones |
| Admin — usuarios | `/admin/users/*` | `auth`, `role:admin` | CRUD de usuarios y roles |

Ver `routes/web.php` como fuente de verdad; este documento resume, no reemplaza el código.

---

## 6. Almacenamiento de archivos

- Imágenes de producto se guardan en `storage/app/public/products` vía `Storage::disk('public')`.
- Requiere `php artisan storage:link` para exponer `public/storage` → `storage/app/public`.
- Límite actual de validación: 4 MB por imagen, formatos estándar de imagen (`image` rule de Laravel).
- **Riesgo conocido:** almacenamiento local no escala horizontalmente (si se despliega en múltiples instancias). Para producción con más de un servidor, migrar a un disco S3-compatible.

---

## 7. Configuración de entorno

Variables clave en `.env`:

```
APP_NAME="Negocios RaR"
DB_CONNECTION=pgsql
DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD
SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log   (cambiar a smtp real para envío de correos de recuperación de contraseña)
```

---

## 8. Datos semilla (seeders)

`php artisan migrate --seed` ejecuta:
- `UserSeeder`: 3 cuentas demo (admin, trabajador, cliente).
- `CategorySeeder`: 6 categorías base.
- `ProductSeeder`: 15 productos de ejemplo distribuidos en esas categorías.
- `ReviewSeeder`: reseñas demo (1-3 reseñas aprobadas por producto, generadas con `ReviewFactory`).
- `WishlistSeeder`: — no incluida en MVP (los registros se crean dinámicamente cuando los clientes guardan productos). El dashboard admin expone "Productos más deseados" consultando la tabla `wishlists` directamente.
- `CouponSeeder`: — no incluido en MVP (los cupones se crean desde el panel admin). Se recomienda crear un seeder para datos demo si se requiere población inicial de prueba.

---

## 9. Testing (estado actual)

El MVP **no incluye suite de tests automatizados** más allá del scaffolding por defecto de Laravel (`tests/`). Se recomienda antes de escalar el equipo:
- Tests de feature para: flujo de checkout, control de acceso por rol, CRUD de productos.
- Tests unitarios para lógica de negocio sensible (cálculo de envío, descuentos, generación de `order_number`).

---

## 10. Despliegue (lineamientos generales)

- Requiere PHP 8.2+, extensión `pdo_pgsql`, Composer, PostgreSQL accesible.
- Pasos estándar: `composer install --no-dev`, configurar `.env` de producción, `php artisan key:generate`, `php artisan migrate --force`, `php artisan storage:link`, `php artisan config:cache` / `route:cache` / `view:cache`.
- Servir con Nginx/Apache + PHP-FPM (no usar `artisan serve` en producción).
- Configurar `APP_ENV=production`, `APP_DEBUG=false`.

---

## 11. Deuda técnica conocida (a comunicar a cualquier IA o desarrollador que continúe el proyecto)

Ver documento **06-CONTEXTO-PARA-IA.md** — contiene las reglas obligatorias a seguir antes de modificar este sistema.
