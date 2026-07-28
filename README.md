# 🛍️ Negocios RaR — Tienda Virtual (Laravel + PostgreSQL)

Plataforma de e-commerce completa con panel administrativo, construida en Laravel 12 con PostgreSQL.

## ✅ Features implementadas

### Tienda online (pública)
- Home con carrusel de banners, categorías destacadas, productos destacados y nuevos
- Catálogo con filtros por categoría, marca, precio y búsqueda con tolerancia a errores (`pg_trgm`)
- Ficha de producto con galería clickable, **zoom al pasar el mouse**, video (YouTube/Vimeo/MP4), selectores de **talla/color** con precio dinámico, reseñas, y botones de compartir
- **Variantes de producto**: combinaciones talla×color con stock, precio e imagen propios
- Carrito de compras (invitados y logueados)
- Checkout con direcciones guardadas, cupón de descuento y canje de puntos
- **Comparador de productos** (hasta 4, misma categoría)
- **Lista de deseos** con toggle desde cualquier página
- Chat en tiempo real (polling 3s) en "Contáctanos" para clientes logueados
- **Newsletter** con suscripción desde el footer
- SEO: meta tags dinámicos, Open Graph, Twitter Cards, JSON-LD Schema.org, sitemap.xml
- Autenticación: login, registro, recuperación de contraseña, **login con Google y Facebook** (Socialite)
- **Puntos de fidelización**: acumulan al recibir pedidos, canjean en checkout

### Panel administrativo (roles `admin` + `trabajador`)
- Dashboard con ventas, pedidos del día, stock bajo, reseñas pendientes
- CRUD de productos con imágenes, galería, campos SEO, video, variantes
- **Gestión de variantes** (talla/color con stock y precio propio)
- CRUD de categorías, banners, beneficios, FAQs
- Gestión de pedidos con timeline visual, cambio de estado y notificación por correo
- **Historial de movimientos de stock** (quién vendió/reabastecío/ajustó, cuándo, stock antes/después)
- **Alertas de reabastecimiento** con stock mínimo configurable por producto
- Gestión de cupones de descuento
- Gestión de reseñas (aprobar/eliminar)
- Bandeja de mensajes del chat de clientes
- **Reportes exportables** a CSV: ventas por período, categoría y producto
- Newsletter: listado, eliminación y exportación
- **Configuración dinámica**: envío, puntos, redes sociales, etc.
- Gestión de usuarios y trabajadores (solo `admin`)

### Roles del sistema
- `admin`: acceso total al panel, incluida gestión de usuarios
- `trabajador`: acceso a productos, pedidos, mensajes, inventario, reportes (sin usuarios)
- `cliente`: rol por defecto al registrarse

## 🧩 Requisitos previos

- PHP >= 8.2 con extensión `pdo_pgsql`
- Composer
- PostgreSQL >= 13 con extensión `pg_trgm`
- Node.js (opcional: Tailwind vía CDN, no requiere build)

## 🚀 Instalación

```bash
composer install
cp .env.example .env
# Editar DB_* en .env con tus datos de PostgreSQL
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

La extensión `pg_trgm` debe estar habilitada en PostgreSQL. En hosting puede requerir solicitud al proveedor.

## 🔑 Cuentas de prueba (seeder)

| Rol | Correo | Contraseña |
|-----|--------|-----------|
| Administrador | admin@negociosrar.com | admin123 |
| Trabajador | trabajador@negociosrar.com | trabajador123 |
| Cliente | cliente@negociosrar.com | cliente123 |

Panel: `http://localhost:8000/admin`

## ⚙️ Configuración adicional

### Colas para correos
```bash
php artisan queue:work --queue=default --sleep=3 --tries=3
```

### Scheduler para carritos abandonados
```cron
* * * * * cd /ruta && php artisan schedule:run >> /dev/null 2>&1
```

### Social Login (Google/Facebook)
Variables requeridas en `.env`:
```
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
```

### Pagos con Culqi
```env
CULQI_PUBLIC_KEY=pk_test_xxx
CULQI_SECRET_KEY=sk_test_xxx
```

## 🎨 Diseño

- Tailwind CSS vía CDN + Alpine.js — sin Vite/npm
- Paleta: `rar-*` (azul corporativo) y `cobre-*` (bronce/accent)
- Toast flotante para feedback

## 📂 Estructura del proyecto

```
app/
├── Http/Controllers/       → Shop (Home, Product, Cart, Checkout, Contact, Compare, etc.)
│   ├── Admin/              → Dashboard, Products, Orders, Inventory, Reports, Variants, etc.
│   └── Auth/               → Login, Register, Password Reset, Socialite
├── Models/                 → User, Product, Order, Cart, ProductVariant, StockMovement, etc.
├── Services/               → StockService
└── Mail/                   → OrderConfirmationMail, OrderStatusUpdatedMail, AbandonedCartMail
database/migrations/        → ~30 migraciones
resources/views/            → Vistas Blade (shop + admin)
routes/web.php              → Todas las rutas
negocios-rar-docs/          → Documentación técnica detallada
```

## 📚 Documentación

La documentación técnica completa está en `negocios-rar-docs/`:
- `01-PRD.md` — Visión del producto
- `02-TRD.md` — Arquitectura técnica
- `03-UXUI.md` — Guía de diseño y marca
- `04-FLUJOS.md` — Flujos de usuario
- `05-BACKEND.md` — Implementación backend detallada
- `06-CONTEXTO-PARA-IA.md` — Contexto obligatorio antes de modificar el sistema
- `07-SEGURIDAD.md` — Políticas de seguridad

---

Desarrollado para Negocios RaR.
