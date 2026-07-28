# Documento de Backend
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 1.5
**Última actualización:** julio 2026

Este documento detalla la implementación del backend a nivel de código: estructura de carpetas, controladores, lógica de negocio por módulo y convenciones usadas. Es el complemento operativo del TRD.

---

## 1. Estructura de carpetas relevante

```
app/
├── Models/
│   ├── User.php, Category.php, Product.php, ProductImage.php, Review.php, Wishlist.php
│   ├── Coupon.php, CouponUse.php
│   ├── Cart.php, CartItem.php
│   ├── Order.php, OrderItem.php
│   ├── Conversation.php, Message.php
│   ├── Setting.php, Banner.php, Benefit.php
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php          → Home + Acerca de
│   │   ├── ProductController.php       → Listado + ficha de producto (público)
│   │   ├── CartController.php          → Carrito
│   │   ├── CheckoutController.php      → Checkout + Mis pedidos
│   │   ├── ContactController.php       → Chat de soporte (lado cliente)
│   │   ├── ReviewController.php        → Reseñas de productos (lado cliente)
│   │   ├── WishlistController.php      → Lista de deseos (lado cliente)
│   │   ├── CouponController.php        → Cupones (AJAX: aplicar/quitar en checkout)
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   └── PasswordResetController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── ProductController.php
│   │       ├── CategoryController.php
│   │       ├── OrderController.php
│   │       ├── UserController.php
│   │       ├── CouponController.php    → CRUD de cupones
│   │       ├── ReviewController.php    → Moderación de reseñas
│   │       ├── MessageController.php   → Chat de soporte (lado staff)
│   │       ├── BannerController.php    → CRUD de banners del carrusel
│   │       ├── BenefitController.php   → CRUD de beneficios del home
│   │       └── SettingController.php   → Formulario de configuración global
│   └── Middleware/
│       └── EnsureUserHasRole.php       → alias 'role'
database/
├── migrations/                          → una tabla por archivo, orden cronológico
└── seeders/                             → UserSeeder, CategorySeeder, ProductSeeder, ReviewSeeder, SiteSeeder (banners, beneficios iniciales)
routes/
└── web.php                              → única fuente de rutas del sistema
resources/views/
├── layouts/app.blade.php                → layout tienda pública
├── layouts/admin.blade.php              → layout panel administrativo
├── partials/product-card.blade.php      → tarjeta de producto reutilizable
└── [home, products, cart, checkout, auth, admin, about, contact]/
```

---

## 2. Convenciones usadas

- **Nombres de rutas:** `recurso.accion` (ej. `products.index`, `admin.orders.show`). Los del panel admin siempre llevan prefijo `admin.`.
- **Controladores del admin:** viven en `App\Http\Controllers\Admin\*` y se importan con alias (`AdminProductController`, etc.) en `routes/web.php` para no chocar con los controladores públicos homónimos.
- **Validación:** se hace inline en cada método del controlador vía `$request->validate([...])`, no se usan Form Requests dedicados (oportunidad de refactor si el proyecto crece).
- **Fillable en modelos:** se usa `protected $fillable = [...]` clásico en todos los modelos. No usar atributos PHP 8 `#[Fillable]` — Laravel 12 no lo reconoce correctamente con esta configuración.
- **Autorización:** se resuelve exclusivamente vía middleware de ruta (`role:admin,trabajador`), no hay Policies de Laravel implementadas todavía.
- **Slugs:** se generan con `Str::slug($name)` + sufijo aleatorio corto en productos (para evitar colisiones entre productos con nombre igual); en categorías el slug es solo `Str::slug($name)` (se asume nombre de categoría único).

---

## 3. Lógica de negocio por módulo

### 3.1 Carrito (`CartController`)
- `currentCart()`: resuelve el carrito activo. Si hay usuario autenticado, usa `Cart::firstOrCreate(['user_id' => ...])`. Si es invitado, genera (o reutiliza) un `session_id` (UUID) guardado en la sesión de Laravel y busca/crea el carrito por ese id.
- **Cart badge:** el `AppServiceProvider` registra un View Composer global que comparte `$cartCount` con todas las vistas. Reutiliza la misma lógica de `currentCart()` y suma `items()->sum('quantity')`. Se muestra como badge circular (`bg-rar-600`) sobre el icono del carrito en el navbar.
- **Importante:** el carrito de invitado **no se fusiona automáticamente** con el carrito del usuario al iniciar sesión. Si un invitado agrega productos y luego se loguea, su carrito de invitado queda huérfano y el checkout usará/creará un carrito nuevo asociado a su `user_id`. Esto es una brecha funcional conocida (ver documento de contexto para IA).

### 3.2 Checkout (`CheckoutController`)
- Solo opera sobre el carrito del usuario autenticado (`Cart::where('user_id', Auth::id())`).
- **Direcciones guardadas:** carga las direcciones del usuario (`Address::where('user_id', Auth::id())`) y las pasa a la vista. El usuario puede seleccionar una existente (radio button) o ingresar una nueva. Si selecciona "Guardar dirección para futuras compras", se crea un registro en `addresses` automáticamente al confirmar el pedido.
- Si `payment_method=tarjeta` y llega `culqi_token`, **antes de la transacción** se hace un cargo a la API de Culqi (`POST /v2/charges`). Si el cargo falla, se devuelve error y **no** se crea el Order ni se vacía el carrito. Si es exitoso, se preparan los campos `culqi_charge_id`, `payment_status=pagado`, `paid_at=now()` para crear el Order con `status=pagado`.
- `store()` ejecuta dentro de una transacción de base de datos (`DB::transaction`):
  1. Calcula subtotal desde los `cart_items`.
   2. Calcula envío: `0` si subtotal ≥ `shipping_min_amount`, si no `shipping_cost` (valores leídos dinámicamente desde la tabla `settings`, editables desde el panel admin → Configuración general).
  3. Crea el `Order` con número único `RAR-` + 8 caracteres aleatorios. Si el pago fue por Culqi, el estado inicial es `pagado`.
  4. Por cada `cart_item`, crea un `OrderItem` (snapshot de nombre y precio) y descuenta `stock` del `Product`.
  5. Vacía el carrito (`$cart->items()->delete()`).
- **No hay verificación de stock disponible antes de descontar** — si dos personas compran el último ítem casi simultáneamente, el stock podría quedar negativo. Ver contexto para IA.

**Configuración:** las llaves de Culqi se definen en `config/services.php` (`culqi.public_key` / `culqi.secret_key`) y se leen de las variables de entorno `CULQI_PUBLIC_KEY` y `CULQI_SECRET_KEY`. En desarrollo se usan llaves TEST (empiezan con `pk_test_`/`sk_test_`). En producción, reemplazar por llaves LIVE tras verificar el negocio en el panel de Culqi.

**Frontend (checkout/index.blade.php):** incluye el script CDN de Culqi Checkout v4. Al seleccionar "Tarjeta de crédito/débito", el botón de submit abre el widget de Culqi. Tras obtener el token, se inyecta en un campo oculto `culqi_token` y se hace submit del formulario. Los métodos transferencia/contraentrega envían el formulario directamente sin pasar por Culqi.

**Campos nuevos en tabla `orders`:** `culqi_charge_id` (string nullable), `payment_status` (string default 'pendiente'), `paid_at` (timestamp nullable).

### 3.3 Catálogo público + Búsqueda (`ProductController` + `SearchController`)
- Filtros combinables vía query string: `q` (búsqueda por nombre con tolerancia a errores), `category` (por slug), `brand`, `min_price`, `max_price`.
- Ordenamiento (`sort`): `price_asc`, `price_desc`, `name`, o por defecto `latest()`.
- Paginación: 12 productos por página, con `withQueryString()` para conservar filtros al cambiar de página.
- Solo se listan productos con `active = true`.

#### Búsqueda con tolerancia a errores de tipeo
- Se usa la extensión `pg_trgm` de PostgreSQL (trigramas) para búsqueda por similitud.
- Migración `2026_07_28_000011_create_search_indexes`: habilita `pg_trgm` y crea índices GIN en `products.name` y `products.brand`.
- En `ProductController@index`, cuando se filtra por `q`:
  1. Primero busca coincidencias con `ilike '%query%'` (coincidencia exacta parcial).
  2. Luego aplica `similarity(name, 'query') > 0.25` para errores de tipeo (ej. "celuar" → "celular").
  3. También busca por marca con `similarity(brand, 'query') > 0.3`.
  4. Ordena por: primero los que empiezan con la query exacta, luego por similitud descendente.
- El umbral de similitud (0.25) permite errores de 1-2 caracteres en palabras cortas.

#### Autocompletado con sugerencias (`SearchController`)
- Endpoint AJAX público: `GET /buscar/sugerencias?q=...` retorna JSON agrupado en 3 secciones:
  - **Productos** (hasta 6): nombre, precio, imagen, marca, slug. Usa `similarity()` para tolerancia.
  - **Categorías** (hasta 3): nombre con ícono, slug.
  - **Marcas** (hasta 3): nombre de marca.
- Frontend implementado con Alpine.js en el navbar (`layouts/app.blade.php`):
  - Input con `x-model` y `@input.debounce.250ms` para evitar llamadas excesivas.
  - Dropdown con 3 secciones (Productos, Categorías, Marcas) con íconos, precios e imágenes.
  - Se cierra al hacer clic fuera (`@click.outside`).
  - Requiere mínimo 2 caracteres para disparar la búsqueda.

### 3.4 Panel administrativo — Productos (`Admin\ProductController`)
- `validateData()` centraliza las reglas de validación para `store()` y `update()`.
- Manejo de imágenes: `main_image` (una sola) + `gallery[]` (múltiples, se guardan como registros `ProductImage` con `sort_order` según el índice de subida).
- Los checkboxes `featured` y `active` se procesan con `$request->boolean(...)` para manejar correctamente su ausencia en el request (HTML no envía checkboxes desmarcados).

### 3.5 Panel administrativo — Dashboard (`Admin\DashboardController`)
- Ventas totales: suma de `orders.total` **solo** para estados `pagado`, `enviado`, `entregado` (pedidos `pendiente`/`cancelado` no cuentan como venta real).
- "Stock bajo": productos activos con `stock <= 5` (umbral hardcodeado).
- "Más vendidos": agregación sobre `order_items` agrupando por `product_name` (usa el snapshot del nombre, no el producto actual — así el dato histórico no se corrompe si el producto se elimina o renombra después).

### 3.6 Mensajes / Chat (`ContactController` + `Admin\MessageController`)
- Un `Conversation` por cliente con estado `abierta` (no hay lógica actual para cerrar conversaciones desde ningún lado de la UI, aunque el campo `status` existe).
- `firstOrCreate(['user_id' => ..., 'status' => 'abierta'])`: si el cliente ya tiene una conversación abierta, se reutiliza; si no, se crea una nueva.
- Al abrir una conversación desde el panel admin, se marcan como leídos (`read = true`) todos los mensajes del cliente (`is_staff = false`).
- **No hay tiempo real:** ni polling ni WebSockets. El staff debe recargar la bandeja para ver mensajes nuevos, y el cliente debe recargar el chat para ver respuestas.

### 3.7 Roles (`EnsureUserHasRole` middleware)
```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (! $request->user() || ! in_array($request->user()->role, $roles)) {
        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
    return $next($request);
}
```
Se usa como `role:admin,trabajador` o `role:admin` en las rutas. Acepta múltiples roles separados por coma.

---

### 3.8 Páginas legales y de ayuda (`LegalController`)
- Métodos: `privacyPolicy()`, `termsConditions()`, `shippingReturns()`.
- `shippingReturns()`: carga las FAQs activas desde la tabla `faqs` y pasa a la vista `legal.shipping-returns`.
- Vista con 3 secciones: información de envío (desde `settings.shipping_info`), política de devoluciones (desde `settings.returns_policy`) y acordeón de preguntas frecuentes (desde tabla `faqs`), con Alpine.js para el comportamiento colapsable.
- Rutas: `/politica-de-privacidad`, `/terminos-y-condiciones`, `/envio-y-devoluciones` (públicas, sin middleware).
- Enlace "Envío y Devoluciones" en navbar y footer de la tienda pública.

**Modelo `Faq` (`app/Models/Faq.php`):**
- `fillable`: question, answer, category, sort_order, active.
- `casts`: active → boolean.
- Scopes: `active()`, `ordered()`.
- Admin CRUD vía `Admin\FaqController`: vista con tabla + formulario inline de creación + modal Alpine.js para edición (mismo patrón que `BenefitController`).
- 6 FAQs demo insertadas en la migración (envío, pago, devolución, general).
- Las FAQs se administran desde el panel admin → Configuración de tienda → ❓ FAQ / Ayuda.
- Controlador liviano con dos métodos: `privacyPolicy()` y `termsConditions()`, cada uno retorna su vista correspondiente.
- Rutas públicas (sin middleware): `/politica-de-privacidad` y `/terminos-y-condiciones`.
- Vistas en `resources/views/legal/`: `privacy-policy.blade.php` y `terms-conditions.blade.php`.
- Contenido redactado para cumplimiento peruano (Ley N° 29733 y Ley N° 29571), con marcadores `[COMPLETAR]` para datos del negocio.

### 3.9 Consentimiento de términos en registro
- Migración `2026_07_28_*` agrega `accepted_terms_at` (timestamp) y `accepted_terms_version` (string) a `users`.
- `RegisterController::store()` valida `accept_terms => 'accepted'` y guarda `accepted_terms_at = now()`, `accepted_terms_version = '2026-07-28'`.
- Vista `auth/register.blade.php` incluye checkbox obligatorio con enlaces a las páginas legales (se abren en pestaña nueva).
- El modelo `User` incluye `accepted_terms_at` en el cast a `datetime`.

### 3.10 Notificaciones por correo (colas)

Se usan dos Mailable classes que implementan `ShouldQueue` para envío asíncrono:

| Mailable | Disparo | Vista |
|---|---|---|
| `App\Mail\OrderConfirmationMail` | `CheckoutController::store()` después de la transacción | `emails/order-confirmation.blade.php` |
| `App\Mail\OrderStatusUpdatedMail` | `Admin\OrderController::updateStatus()` | `emails/order-status-updated.blade.php` |

Ambos se envían con `Mail::to(...)->queue(...)` dentro de un bloque `try/catch` que registra el error con `Log::error()` sin interrumpir el flujo. Las vistas usan tablas HTML con estilos inline (compatibles con clientes de correo) y un layout compartido en `emails/layout.blade.php`.

**Requisito de infraestructura:** la cola (`QUEUE_CONNECTION=database`) requiere un worker corriendo (`php artisan queue:work`) para procesar los correos. Ver README para configuración de producción.

### 3.11 View Composer global — cart badge y wishlist badge (`AppServiceProvider`)
- El `AppServiceProvider::boot()` registra un View Composer para todas las vistas (`*`).
- La lógica replica a `CartController::currentCart()`:
  - Si el usuario está autenticado: `Cart::where('user_id', Auth::id())->first()`
  - Si es invitado con `cart_session_id` en sesión: `Cart::where('session_id', session('cart_session_id'))->whereNull('user_id')->first()`
- Comparte `$cartCount` con todas las vistas (0 si no hay carrito, o la suma de `items()->sum('quantity')`).
- El badge se renderiza en `layouts/app.blade.php` como un `span` posicionado absolutamente sobre el icono del carrito, visible solo cuando `$cartCount > 0`.

Además, cuando el usuario está autenticado, comparte `$wishlistCount` con todas las vistas (conteo de registros en `wishlists` para ese usuario, 0 si no tiene ninguno). El badge de la wishlist se renderiza en el navbar con el mismo patrón visual que el carrito pero con color `bg-cobre-500`.

### 3.12 Reseñas (`ReviewController` + `Admin\ReviewController`)

**Modelo `Review` (`app/Models/Review.php`):**
- `fillable`: `product_id`, `user_id`, `rating`, `comment`.
- `casts`: `rating` → `integer`, `approved` → `boolean`.
- Relaciones: `product()` (BelongsTo `Product`), `user()` (BelongsTo `User`).

**Modelo `Product` — métodos agregados:**
- `reviews()`: `hasMany(Review::class)` — todas las reseñas del producto.
- `approvedReviews()`: `hasMany(Review::class)->where('approved', true)` — solo aprobadas.
- `averageRating()`: promedio de `rating` sobre reseñas aprobadas (retorna 0 si no hay reseñas).
- `reviewsCount()`: conteo de reseñas aprobadas.

**Cliente — `ReviewController::store()`:**
- Validación: `rating` required|integer|min:1|max:5, `comment` nullable|string|max:1000.
- Guardias antes de crear:
  1. Email verificado (`auth()->user()->hasVerifiedEmail()`).
  2. El usuario compró el producto (existe `OrderItem` vinculado a una `Order` del usuario con `product_id`).
  3. El pedido está en estado `entregado`.
- Previene duplicados: verifica que no exista `Review` con el mismo `product_id` + `user_id`.
- La reseña se crea con `approved = false`.
- `ReviewController::destroy()`: solo el dueño de la reseña puede eliminarla (`$review->user_id === Auth::id()`).

**Admin — `Admin\ReviewController`:**
- `index()`: paginación de todas las reseñas + conteo de pendientes (`where('approved', false)->count()`).
- `approve()` (PATCH): establece `approved = true` en la reseña.
- `destroy()` (DELETE): elimina la reseña.

**Frontend — ficha de producto (`products/show.blade.php`):**
- Formulario de reseña con selector de estrellas (implementado con Alpine.js) y textarea para comentario.
- Listado de reseñas aprobadas debajo del formulario, con estrellas visuales, nombre del usuario, comentario y fecha.

**Frontend — tarjeta de producto (`partials/product-card.blade.php`):**
- Muestra el promedio dinámico (`$product->averageRating()`) y conteo de reseñas (`$product->reviewsCount()`) solo si hay reseñas aprobadas.

**Panel admin — reseñas:**
- Enlace "⭐ Reseñas" en la sidebar del layout `admin.blade.php` apuntando a `route('admin.reviews.index')`.
- Vista con tabla de reseñas (producto, usuario, rating, comentario, fecha, estado) y acciones: aprobar (solo si `approved=false`) y eliminar.
- Dashboard (`Admin\DashboardController`): tarjeta con conteo de reseñas pendientes de aprobación.

### 3.13 Lista de deseos (`WishlistController`)

**Modelo `Wishlist` (`app/Models/Wishlist.php`):**
- `fillable`: `user_id`, `product_id`.
- Relaciones: `user()` (BelongsTo `User`), `product()` (BelongsTo `Product`).

**Modelo `User` — métodos agregados:**
- `wishlists()`: `hasMany(Wishlist::class)`.
- `hasInWishlist(Product $product): bool`: consulta si el usuario ya tiene ese producto en su wishlist (`$this->wishlists()->where('product_id', $product->id)->exists()`).

**Controlador `WishlistController`:**
- `index()`: lista los `Wishlist` del usuario autenticado con `with('product.category')`, ordenados por `latest()`.
- `toggle(Product $product)`: busca si ya existe un registro con `user_id + product_id`. Si existe, lo elimina (`→ mensaje: "Eliminado..."`). Si no, lo crea (`→ mensaje: "Agregado..."`). Siempre responde con `back()` (funciona desde cualquier página).
- `destroy(Wishlist $wishlist)`: elimina un ítem puntual verificando que `$wishlist->user_id === Auth::id()` (abort 403 si no coincide).

**Frontend:**
- **Tarjeta de producto (`product-card.blade.php`):** botón corazón en SVG (relleno=currentColor si está en wishlist, fill=none si no), posicionado en la esquina superior derecha de la imagen. El formulario incluye `@click.stop` vía Alpine.js para evitar que el clic se propague al `<a>` que envuelve la tarjeta.
- **Ficha de producto (`products/show.blade.php`):** botón corazón junto al botón "Agregar al carrito", mismo patrón visual de SVG.
- **Página wishlist (`resources/views/wishlist/index.blade.php`):** grid de tarjetas reutilizando `product-card.blade.php`, más botón directo "Agregar al carrito" por cada producto y botón "Quitar" con confirmación. Estado vacío con emoji 💔 + CTA a "Ver productos".
- **Badge en navbar:** ícono corazón en el header, visible solo si el usuario está logueado, con badge numérico (`wishlistCount`) de color `cobre-500`.

**Admin dashboard:**
- `Admin\DashboardController`: query `$productosMasDeseados` que agrupa `wishlists` por `product_id` y ordena por conteo descendente (top 5). Se muestra en una tarjeta junto a "Productos más vendidos" con el nombre del producto y "X en lista(s)".

---

## 4. Integridad referencial (a nivel de migraciones)

| Relación | Comportamiento al eliminar el padre |
|---|---|
| `products.category_id → categories` | `cascadeOnDelete` — elimina productos si se borra la categoría (⚠️ riesgo: borrar una categoría borra todo su catálogo) |
| `product_images.product_id → products` | `cascadeOnDelete` |
| `reviews.product_id → products` | `cascadeOnDelete` — al eliminar un producto se eliminan sus reseñas |
| `reviews.user_id → users` | `cascadeOnDelete` — al eliminar un usuario se eliminan sus reseñas |
| `wishlists.product_id → products` | `cascadeOnDelete` |
| `wishlists.user_id → users` | `cascadeOnDelete` |
| `coupons.category_id → categories` | `cascadeOnDelete` |
| `coupon_uses.coupon_id → coupons` | `cascadeOnDelete` |
| `coupon_uses.user_id → users` | `cascadeOnDelete` |
| `coupon_uses.order_id → orders` | `cascadeOnDelete` |
| `orders.coupon_id → coupons` | `nullOnDelete` — el pedido conserva el código snapshot aunque el cupón se borre |
| `cart_items.product_id → products` | `cascadeOnDelete` |
| `order_items.product_id → products` | `nullOnDelete` — el pedido conserva el snapshot aunque el producto se borre |
| `orders.user_id → users` | `cascadeOnDelete` (⚠️ riesgo: borrar un cliente borra su historial de pedidos) |
| `conversations.user_id → users` | `cascadeOnDelete` |

**Recomendación para evolución futura:** cambiar `products.category_id` y `orders.user_id` a `restrictOnDelete` o soft-deletes, para no perder historial de ventas ni catálogo por error operativo.

---

### 3.14 Configuración de tienda (banners, beneficios, settings)

Se implementaron tres nuevas entidades para hacer administrable todo el contenido del storefront desde el panel:

**Modelo `Setting` (`app/Models/Setting.php`):**
- Tabla clave-valor (`key` único, `value` texto).
- Métodos estáticos: `getValue(key, default)` y `setValue(key, value)` para acceso rápido desde cualquier parte del sistema (controladores y vistas).
- Valores almacenados: top_bar_text, footer_description/address/phone/email, shipping_min_amount, shipping_cost, about_mission/vision/values/about_subtitle, about_clients_count/products_count/regions_count/rating, home_title_categories/featured/newest, store_logo/logo_icon (rutas de imagen), store_ruc/business_name/address/phone/email.
- Los valores por defecto se insertan en la migración. Si una key no existe en BD, los helpers en las vistas usan `Setting::getValue('key', 'default literal')` para no romper el frontend.
- Admin: `SettingController@index` (formulario único con todas las secciones) + `SettingController@update` (recorre las keys validadas y ejecuta `Setting::setValue()`). Soporta subida de imágenes para `store_logo` y `store_logo_icon` (se guardan en `storage/app/public/store/`).
- Settings adicionales: `shipping_info` y `returns_policy` (texto largo para la página de Envío y Devoluciones).
- Las imágenes de logo se renderizan con fallback a `asset('images/...')` si el valor comienza con `images/` (archivos default del proyecto), o a `asset('storage/...')` si fueron subidas por el admin.

**Modelo `Banner` (`app/Models/Banner.php`):**
- Campos: title, subtitle, button_text, button_url, image (ruta de storage), gradient_from, gradient_to, text_color, sort_order, active.
- Scopes: `active()` (filtra `active = true`), `ordered()` (ordena por `sort_order`).
- CRUD completo via `Admin\BannerController` con subida de imagen a `storage/app/public/banners/`.
- Vista admin con tabla + formularios create/edit con preview de imagen actual.
- Frontend: `home.blade.php` consume `$banners` y renderiza cada slide dinámicamente con Alpine.js. Si no hay banners, muestra un placeholder.

**Modelo `Benefit` (`app/Models/Benefit.php`):**
- Campos: icon (emoji), title, sort_order, active.
- Scopes: `active()`, `ordered()`.
- CRUD via `Admin\BenefitController`: index con tabla + formulario inline de creación + modal Alpine.js para edición.
- Frontend: `home.blade.php` consume `$benefits` y renderiza la grilla dinámicamente.

**Sidebar admin:**
Se agregó una sección "Configuración de tienda" en el admin layout como dropdown colapsable (Alpine.js) con 3 sub-enlaces: Banners/Carrusel, Beneficios, Configuración general.

**Rutas admin nuevas:**
```
GET|POST  admin/banners/*           → Admin\BannerController (resource completo)
GET|POST  admin/benefits/*          → Admin\BenefitController (index, store, update, destroy)
GET       admin/settings            → Admin\SettingController@index
POST      admin/settings            → Admin\SettingController@update
```

### 3.15 Cupones de descuento (`CouponController` + `Admin\CouponController`)

**Modelo `Coupon` (`app/Models/Coupon.php`):**
- `fillable`: todos los campos editables (code, type, value, category_id, min_purchase, max_discount, usage_limit, usage_count, usage_limit_per_user, starts_at, expires_at, active).
- `casts`: active (boolean), starts_at/expires_at (datetime), value/min_purchase/max_discount (decimal:2).
- Relaciones: `category()` (BelongsTo nullable), `uses()` (hasMany CouponUse).
- `isValid(): bool`: evalúa active=true, starts_at ≤ now() (si definido), expires_at ≥ now() (si definido), usage_count < usage_limit (si definido).
- `isValidForUser(User): bool`: además de isValid(), verifica que el usuario no haya superado usage_limit_per_user contando sus registros en `coupon_uses`.
- `findByCode(string): ?self`: busca por `code` en mayúsculas.
- `calculateDiscount(float $subtotal, $cartItems): float`:
  - Si `type='percentage'`: `$applicableSubtotal * value / 100`, topado por max_discount si existe.
  - Si `type='fixed'`: el valor fijo, nunca mayor al subtotal aplicable.
  - Si `category_id` no es null, el subtotal aplicable es solo la suma de ítems del carrito cuyo `product_id` pertenece a esa categoría.

**Modelo `CouponUse` (`app/Models/CouponUse.php`):**
- `fillable`: coupon_id, user_id, order_id, discount_amount.
- Relaciones: belongsTo Coupon, User, Order.

**Modelo `Order`:**
- Campos nuevos en `fillable`: `coupon_id`, `coupon_code`, `discount_amount`.
- Relación `coupon()`: `belongsTo(Coupon::class)`.

**Cliente — `CouponController`:**
- `apply()` (POST /checkout/aplicar-cupon): recibe `code` vía JSON, busca el cupón, valida con `isValidForUser()`, verifica `min_purchase` contra el subtotal real del carrito en BD. Si todo ok, guarda `coupon_id` en sesión y responde JSON con descuento calculado y nuevo total. Si no, responde JSON 422 con mensaje específico.
- `remove()` (POST /checkout/quitar-cupon): limpia `applied_coupon` de la sesión, responde JSON con total recalculado sin descuento.

**CheckoutController:**
- `index()`: si hay `applied_coupon` en sesión, carga el `Coupon`, revalida con `isValidForUser()`, calcula descuento con `calculateDiscount()` y lo pasa a la vista para mostrar en el resumen.
- `store()`: dentro de `DB::transaction`:
  1. Revalida el cupón (isValidForUser + calculateDiscount).
  2. Si es válido y discount > 0: guarda `coupon_id`, `coupon_code`, `discount_amount` en el Order.
  3. Crea un registro `CouponUse`.
  4. Incrementa `coupon.usage_count` en 1.
  5. Limpia `applied_coupon` de la sesión tras la transacción.
  - Si el cupón dejó de ser válido entre la aplicación y la confirmación: ignora el cupón, avisa al cliente, continúa sin descuento (no bloquea la compra).

**Importante — doble validación:** el descuento NUNCA se recibe del frontend. Siempre se recalcula en el servidor al confirmar el pedido, incluso si ya se calculó antes al aplicar el cupón vía AJAX. El valor en sesión es solo para mostrar el preview en la UI, no para cobrar.

**Admin — `Admin\CouponController`:**
- CRUD completo siguiendo el patrón de `Admin\ProductController`: `validateData()` centralizado, `strtoupper()` en el código al guardar.
- Vistas: `admin/coupons/index.blade.php` (tabla con código, tipo, valor, categoría, usos, expiración, estado), `create.blade.php`, `edit.blade.php`, `_form.blade.php` (campos: código auto-mayúscula, tipo select, valor, categoría opcional, compra mínima, descuento máximo, límites, fechas, active checkbox).

**Dashboard:**
- `Admin\DashboardController`: consulta `Coupon::where('active', true)->where('expires_at', '>=', now()...)` para contar cupones activos vigentes. Se muestra como una tarjeta más en el grid de estadísticas.
- Sidebar del admin incluye enlace "🏷️ Cupones" apuntando a `route('admin.coupons.index')`.

### 3.16 Recuperación de carrito abandonado

**Tabla `carts`:**
- Nuevo campo `last_active_at` (timestamp, nullable) — se actualiza cada vez que el usuario interactúa con su carrito (desde `CartController` y desde el View Composer en `AppServiceProvider`).
- Método `Cart::touchLastActive()`: `$this->update(['last_active_at' => now()])`.

**Mailable `AbandonedCartMail` (`app/Mail/AbandonedCartMail.php`):**
- Implementa `ShouldQueue` para envío asíncrono.
- Usa el layout compartido `emails/layout.blade.php`.
- Incluye tabla con productos del carrito, cantidades, precios unitarios y total.
- Botón CTA "Recuperar mi carrito" que apunta a `route('cart.index')`.
- Asunto configurable desde el panel admin (`abandoned_cart_subject`).

**Command `rar:send-abandoned-carts` (`app/Console/Commands/SendAbandonedCartReminders.php`):**
- Lee `abandoned_delay_hours` de `settings` (default 24h).
- Busca carritos con `user_id` no nulo, con ítems, con `last_active_at` anterior al corte.
- Excluye carritos donde algún producto esté inactivo.
- Por cada carrito, envía `AbandonedCartMail` a la cola y actualiza `last_active_at` a `now()` para evitar reenvíos.
- Se ejecuta cada hora vía scheduler (`routes/console.php`):
  ```php
  Schedule::command('rar:send-abandoned-carts')->hourly();
  ```

**Settings nuevas:**
| Key | Default | Descripción |
|---|---|---|
| `abandoned_delay_hours` | `24` | Horas sin actividad para considerar carrito abandonado |
| `abandoned_cart_subject` | `¡No olvides tu carrito! Tienes productos esperándote` | Asunto del correo de recuperación |

**Configuración en el panel admin:**
Sección "🛒 Recuperación de carrito abandonado" dentro de Configuración general, con campos para horas de abandono y asunto del correo.

**Requisito de infraestructura:**
Requiere un worker de cola corriendo (`php artisan queue:work`) y el scheduler configurado en el cron del servidor:
```
* * * * * cd /ruta/del/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

---

## 5. Puntos de extensión previstos (dónde engancharía cada feature futura)

| Feature futura | Dónde se conecta |
|---|---|
| Pasarela de pago real (Culqi) | `CheckoutController::store()` — integrado vía HTTP a API de Culqi. Si `payment_method=tarjeta`, hace cargo antes de crear el `Order` y establece `payment_status=pagado`. Ya dispara `OrderConfirmationMail` tras la transacción |
| Notificaciones de cambio de estado | `Admin\OrderController::updateStatus()` — ya dispara `OrderStatusUpdatedMail` (implementado) |
| Reportes exportables | Nuevo controlador `Admin\ReportController`, reutilizando las queries ya existentes en `DashboardController` |
| Reseñas de productos | Tabla `reviews` implementada — ver sección 3.12 y migración `create_reviews_table` |
| Lista de deseos (wishlist) | Tabla `wishlists` implementada — ver sección 3.13. Además, esta tabla es la fuente de datos recomendada para una futura campaña de remarketing por correo ("productos que dejaste en tu lista") |
| Cupones/descuentos | Tabla `coupons` implementada — ver sección 3.15. Incluye CRUD admin, aplicación AJAX en checkout, doble validación server-side y registro de usos (`coupon_uses`) |
| Chat en tiempo real | Sustituir el modelo actual de `Message` por broadcasting de Laravel (Reverb/Pusher) sobre los mismos modelos `Conversation`/`Message` |
| Contenido dinámico del storefront | Banners, beneficios, config de envío, footer, acerca de y títulos del home ahora son administrables desde el panel (tablas `settings`, `banners`, `benefits`) |
| Múltiples direcciones por cliente | Tabla `addresses` implementada. Los clientes gestionan sus direcciones desde `/mis-direcciones` y pueden seleccionar una guardada en el checkout |
| Timeline visual de pedidos | Tabla `order_statuses` inmutable. Cada cambio de estado (`pendiente`/`pagado`/`enviado`/`entregado`/`cancelado`) se registra con timestamp automáticamente desde `CheckoutController@store` y `Admin\OrderController@updateStatus`. La vista `partials/order-timeline` renderiza una línea de tiempo vertical con íconos. Disponible en el detalle del pedido (`/mis-pedidos/{order}`) y en el admin (`admin/orders/{order}`) |
| Comparador de productos | `CompareController` — sesión (`comparison`), máx 4 productos de la misma categoría. Vista `compare/index.blade.php` con tabla lado a lado de specs (`attributes` JSON), precio, stock, rating y botón "Agregar al carrito". Botón "Comparar" en cada `product-card` y en ficha de producto. Enlace en navbar |
| Chat en tiempo real | ContactController y Admin\MessageController exponen endpoints JSON (`/contactanos/mensajes`, `/admin/messages/{conv}/messages`) para polling cada 3s vía Alpine.js. Envío de mensajes AJAX sin recarga. Marca mensajes como leídos automáticamente |
| SEO técnico | Meta tags dinámicos OG/Twitter por producto, JSON-LD Schema.org Product con precio, disponibilidad y rating, sitemap.xml dinámico en `/sitemap.xml`, campos `meta_title`/`meta_description` editables desde el panel de productos |
| Newsletter | Tabla `newsletters` con email único + nombre opcional + active. Formulario de suscripción en el footer con Alpine.js AJAX. Admin: listado, eliminación y exportación de emails a .txt |
| Programa de puntos fidelización | Columnas `loyalty_points`/`lifetime_points` en `users` + tabla `loyalty_transactions`. Los puntos se acreditan automáticamente al marcar pedido como "Entregado" (tasa configurable: `points_earning_rate`). El cliente puede canjear en checkout (tasa: `points_redeem_rate`, mínimo: `min_points_to_redeem`). Vista `/mis-puntos` con saldo e historial. Badge en navbar |
| Social Login | Laravel Socialite (v5.x) — login/registro con Google y Facebook. Botones en login y registro. Tabla `social_accounts` (user_id, provider, provider_id, avatar). Redirige al home post-login |
| Recuperación de carrito abandonado | Comando `rar:send-abandoned-carts` + `AbandonedCartMail` implementado — ver sección 3.16. Envía correo automático con productos del carrito y CTA a recuperar compra |
| Búsqueda con tolerancia a errores | `pg_trgm` + autocompletado Alpine.js implementado — ver sección 3.3. Búsqueda por similitud con índices GIN |

---

## 6. Comandos de referencia rápida

```bash
# Instalación
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Desarrollo
php artisan serve
php artisan migrate:fresh --seed   # reiniciar BD desde cero con datos demo
php artisan rar:send-abandoned-carts  # probar recuperación de carritos abandonados

# Cola de correos (requerido para OrderConfirmationMail, OrderStatusUpdatedMail y AbandonedCartMail)
php artisan queue:work

# Producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Programar en cron del servidor:
# * * * * * cd /ruta/proyecto && php artisan schedule:run >> /dev/null 2>&1
```
