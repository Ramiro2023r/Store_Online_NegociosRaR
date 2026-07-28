# 🛍️ Negocios RaR — Tienda Virtual (Laravel + PostgreSQL)

Proyecto completo de e-commerce con módulo de **tienda online** y **panel administrativo**, construido en Laravel 13 con base de datos PostgreSQL.
<img width="1915" height="877" alt="image" src="https://github.com/user-attachments/assets/ba75ff11-de9f-4cbe-b9a0-008f4628ec72" />


## ✅ Qué incluye

**Tienda online (pública):**
- Home con carrusel de promociones, categorías destacadas, productos destacados y nuevos.
- Menú: Inicio / Productos / Acerca de / Contáctanos / Iniciar sesión.
- Listado de productos con filtros por categoría, marca y rango de precio, y ordenamiento.
- Ficha de producto con galería, stock, atributos y "agregar al carrito".
- Carrito de compras (funciona para invitados y usuarios logueados).
- Checkout con datos de envío y métodos de pago.
- "Mis pedidos" para el cliente.
- Login, registro y recuperación de contraseña.
- Chat de "Contáctanos" — solo visible si el usuario inició sesión.

**Panel administrativo (roles `admin` y `trabajador`):**
- Dashboard con ventas totales, pedidos del día, productos, clientes y alertas de stock bajo.
- CRUD de productos (con imágenes, destacados, activar/desactivar).
- CRUD de categorías.
- Gestión de pedidos/ventas con cambio de estado (pendiente → pagado → enviado → entregado).
- Bandeja de mensajes de clientes (chat de soporte).
- Gestión de usuarios y trabajadores (solo rol `admin`).

**Roles del sistema:**
- `admin`: acceso total al panel, incluida gestión de usuarios.
- `trabajador`: acceso al panel (productos, categorías, pedidos, mensajes), sin gestión de usuarios.
- `cliente`: rol por defecto al registrarse en la tienda.

## 🧩 Requisitos previos

- PHP >= 8.3 con extensión `pdo_pgsql` habilitada
- Composer
- PostgreSQL >= 13 (local o en la nube)
- Node.js (opcional, no es obligatorio: el diseño usa Tailwind vía CDN)

## 🚀 Instalación paso a paso

1. Instalar dependencias PHP:
```
composer install
```

2. Configurar el entorno:
```
cp .env.example .env
```
Edita `.env` y ajusta tus datos de PostgreSQL:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=negocios_rar
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

3. Crear la base de datos (si aún no existe):
```
createdb -U postgres negocios_rar
```
o desde psql:
```sql
CREATE DATABASE negocios_rar;
```

4. Generar la clave de la aplicación:
```
php artisan key:generate
```

5. Ejecutar migraciones y poblar datos de ejemplo:
```
php artisan migrate --seed
```
Esto crea las tablas y agrega: 3 usuarios de prueba (admin, trabajador, cliente), 6 categorías y 15 productos de ejemplo.

6. Enlazar el almacenamiento público (para ver imágenes subidas de productos):
```
php artisan storage:link
```

7. Levantar el servidor:
```
php artisan serve
```
Abre http://localhost:8000

## 🔑 Cuentas de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@negociosrar.com | admin123 |
| Trabajador | trabajador@negociosrar.com | trabajador123 |
| Cliente | cliente@negociosrar.com | cliente123 |

Panel administrativo: http://localhost:8000/admin (solo accesible con cuenta admin o trabajador).

## ✉️ Recuperar contraseña (correo)

Por defecto `MAIL_MAILER=log`, así que los enlaces de recuperación de contraseña se escriben en `storage/logs/laravel.log` en vez de enviarse por correo real. Para enviarlos de verdad, configura un proveedor SMTP (Gmail, Mailgun, SendGrid, Resend, etc.) en las variables `MAIL_*` del `.env`.

## 💳 Configurar pagos con Culqi

El sistema integra [Culqi](https://www.culqi.com) como pasarela de pago para pagos con tarjeta de crédito/débito.

### Paso 1: Crear cuenta y obtener llaves de prueba
1. Regístrate en [https://panel.culqi.com](https://panel.culqi.com).
2. Ve a la sección **"API y llaves"** del panel.
3. Copia las llaves de **prueba (TEST)**: `pk_test_xxxxxxxx` (pública) y `sk_test_xxxxxxxx` (secreta).
4. Agrégalas en tu `.env`:
   ```
   CULQI_PUBLIC_KEY=pk_test_xxxxxxxx
   CULQI_SECRET_KEY=sk_test_xxxxxxxx
   ```

### Paso 2: Tarjetas de prueba
Culqi proporciona tarjetas para simular distintos escenarios en modo test:

| Escenario | Número de tarjeta | Código | Resultado |
|---|---|---|---|
| Aprobado | `4111111111111111` | Cualquiera (3 dígitos) | `venta_exitosa` |
| Rechazado | `4000000000000002` | Cualquiera | `rechazo` |
| Insuficiente | `4000000000000005` | Cualquiera | `codigo_insuficiente` |

Usa cualquier fecha de expiración futura y un email cualquiera en el formulario de Culqi.

### Paso 3: Probar el flujo completo
1. Inicia el servidor: `php artisan serve`
2. Asegúrate de tener un queue worker corriendo para los correos: `php artisan queue:work`
3. Agrega productos al carrito y ve al checkout.
4. Selecciona **"Tarjeta de crédito/débito"** — se abrirá el widget de Culqi.
5. Ingresa los datos de la tarjeta de prueba y completa el pago.
6. Si el pago es exitoso, el pedido se crea con estado **"Pagado"** y se envía el correo de confirmación.
7. Si falla, verás un mensaje de error claro y el carrito **no se vacía**.

### Paso 4: Pasar a producción
1. En el panel de Culqi, completa el proceso de **verificación de negocio** (documentación requerida).
2. Una vez verificada, Culqi activará las llaves **LIVE**.
3. Reemplaza las llaves en tu `.env`:
   ```
   CULQI_PUBLIC_KEY=pk_live_xxxxxxxx
   CULQI_SECRET_KEY=sk_live_xxxxxxxx
   ```
4. Asegúrate de que tu dominio use HTTPS y configura los webhooks de Culqi si deseas notificaciones de pago asíncronas.

---

## 📬 Notificaciones de pedidos por correo

El sistema envía dos correos automáticos usando colas (`QUEUE_CONNECTION=database`):

| Correo | Cuándo se envía | Mailable |
|---|---|---|
| Confirmación de pedido | Al finalizar el checkout exitosamente | `OrderConfirmationMail` |
| Cambio de estado | Al actualizar el estado del pedido en el panel admin | `OrderStatusUpdatedMail` |

**Requisitos para producción:**

1. **Proveedor de correo transaccional:** configura un servicio como Resend, Mailgun, SES o SendGrid en las variables `MAIL_*` del `.env`.
2. **Registros DNS:** agrega registros SPF, DKIM y DMARC en la zona DNS del dominio desde el que se enviarán los correos (`MAIL_FROM_ADDRESS`).
3. **Queue worker:** debe haber un proceso de cola corriendo permanentemente para que los correos se despachen en segundo plano:
   ```bash
   php artisan queue:work --queue=default --sleep=3 --tries=3
   ```
   En producción, gestiona este proceso con **Supervisor** (Linux) o el gestor de procesos de tu hosting para que se reinicie automáticamente si falla.

**En desarrollo** (con `MAIL_MAILER=log`): los correos no se envían realmente, pero se encolan y se escriben en `storage/logs/laravel.log`. Para procesar la cola localmente:
```bash
php artisan queue:work --once  # procesa un solo trabajo
php artisan queue:work          # queda escuchando permanentemente
```

## 🎨 Notas de diseño

- El frontend usa Tailwind CSS vía CDN y Alpine.js para el carrusel, por lo que no necesitas correr `npm install` ni `npm run build` para ver la tienda funcionando.
- El logo principal está en `public/images/Mejoradelogo.svg` y el icono cuadrado en `public/images/Mejoradelogoiconoapp.svg`. Puedes reemplazarlos por tu propio diseño cuando quieras.

## 📂 Estructura relevante

```
app/Models/                    User, Category, Product, Cart, Order, Message, etc.
app/Http/Controllers/          Controladores de la tienda (Home, Product, Cart, Checkout, Contact)
app/Http/Controllers/Auth/     Login, Registro, Recuperar contraseña
app/Http/Controllers/Admin/    Dashboard, Productos, Categorías, Pedidos, Usuarios, Mensajes
app/Http/Middleware/EnsureUserHasRole.php   Middleware de roles (admin / trabajador)
database/migrations/           Todas las tablas del sistema
database/seeders/              Datos de ejemplo (usuarios, categorías, productos)
resources/views/               Vistas Blade (tienda + panel admin)
routes/web.php                 Todas las rutas de la aplicación
```

## 🛠️ Próximos pasos sugeridos


- Agregar reseñas y comentarios de productos.
- Agregar reportes de ventas exportables (Excel/PDF) en el panel admin.

---
Desarrollado como proyecto base para Negocios RaR. Puedes personalizar colores, textos y el logo desde `resources/views/layouts` y `public/images`.
