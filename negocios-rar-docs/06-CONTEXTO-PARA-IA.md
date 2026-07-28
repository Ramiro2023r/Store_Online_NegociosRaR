# Contexto para IA — Reglas Obligatorias Antes de Modificar el Sistema
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 2.7
**Última actualización:** julio 2026
**Propósito:** este documento debe darse como contexto a cualquier IA (Claude, ChatGPT, Copilot, etc.) o desarrollador antes de pedirle que agregue o modifique algo en este sistema. Resume el estado real del código, sus decisiones de diseño y sus límites conocidos, para evitar que una IA "reinvente" algo ya resuelto o rompa algo que parece no estar conectado a simple vista.

---

## 1. Antes de tocar el código, lee esto

1. **Este proyecto ya tiene una arquitectura y convenciones definidas** (ver `02-TRD.md` y `05-BACKEND.md`). No reescribas desde cero un módulo si se puede extender.
2. **No existe capa de Services/Actions separada:** la lógica de negocio vive dentro de los Controllers. Si vas a agregar lógica compleja nueva, evalúa si conviene extraerla a una clase de servicio — pero no lo hagas "de paso" en un cambio que no lo pedía explícitamente; puede introducir bugs por refactor no solicitado.
3. **No hay tests automatizados robustos.** Cualquier cambio debe probarse manualmente (o agregar tests si el usuario lo pide) antes de darse por funcional.
4. **El proyecto corre con Tailwind vía CDN, sin build de Vite/npm.** No agregues dependencias de frontend que requieran compilación a menos que el usuario acepte explícitamente pasar a un pipeline con `npm run build`.
5. **PostgreSQL, no MySQL.** Cuidado con sintaxis SQL que no sea compatible (ej. usar `ilike` en vez de `like` para búsquedas insensibles a mayúsculas; no asumir `AUTO_INCREMENT`, Postgres usa `SERIAL`/`IDENTITY` vía Eloquent sin que tengas que tocarlo).
6. **PHP 8.2 / Laravel 12.** No uses sintaxis o features exclusivas de PHP 8.3+ o Laravel 13+ (por ejemplo, no uses atributos `#[Fillable]`/`#[Hidden]` de Eloquent — este proyecto usa `protected $fillable` clásico, ya que Laravel 12 no reconoce esos atributos correctamente con el autoload actual).
7. **PostgreSQL requiere extensión `pg_trgm`.** La migración `2026_07_28_000011_create_search_indexes` ejecuta `CREATE EXTENSION IF NOT EXISTS pg_trgm`. El usuario `postgres` necesita permiso para crear extensiones. Si no está disponible, la migración fallará. En entornos compartidos (hosting) puede requerir solicitar la activación manual de `pg_trgm` al proveedor.

---

## 2. Brechas y deuda técnica conocidas (no son "bugs sorpresa", son decisiones de alcance del MVP)

Repasa esta lista **antes** de asumir que algo "falta" o está roto — puede que ya esté documentado como pendiente intencional:

| Brecha | Detalle | Impacto si no se atiende |
|---|---|---|
| Carrito de invitado no se fusiona al loguearse | Un invitado que agrega productos y luego inicia sesión pierde esos ítems (quedan en un carrito huérfano por `session_id`) | Posible pérdida de carrito percibida como bug por el cliente |
| No se valida stock disponible al momento del checkout | Se descuenta stock sin verificar que siga siendo ≥ cantidad pedida | Riesgo de stock negativo en alta concurrencia |
| Campo `users.active` no bloquea login | Existe la columna pero `LoginController` no la verifica | Un admin puede "desactivar" a alguien y esa persona igual podría iniciar sesión |
| Sin pasarela de pago real | El checkout guarda el método de pago como texto, no procesa cobro | No hay dinero real moviéndose por el sistema todavía |
| Sin notificaciones automáticas | ~~Cambiar el estado de un pedido no avisa al cliente por correo/SMS~~ ✅ Implementado envío por correo. SMS pendiente. | El cliente debe entrar manualmente a "Mis pedidos" para enterarse |
| ~~Chat sin tiempo real~~ | ✅ **Resuelto.** Cliente y admin usan Alpine.js con polling cada 3s + envío AJAX. Sin recarga de página. Ver `ContactController`, `Admin\MessageController`, `contact.blade.php`, `admin/messages/show.blade.php` | — |
| ~~Envío gratis y costo de envío hardcodeados~~ | ✅ **Resuelto.** `shipping_min_amount` y `shipping_cost` ahora se leen de la tabla `settings`, editables desde el panel admin → Configuración general. Ver `BACKEND.md` sección 3.14 | — |
| `category_id` en productos usa `cascadeOnDelete` | Borrar una categoría borra todos sus productos | Riesgo operativo alto si un admin borra una categoría por error |
| `orders.user_id` usa `cascadeOnDelete` | Borrar un usuario borra su historial de pedidos | Pérdida de datos de ventas si se elimina un cliente |
| Sin verificación de email obligatoria | Un usuario puede comprar con un correo no verificado | Riesgo de correos falsos/typos sin detectar |
| Panel admin no es responsive | Sidebar fijo de 256px asume escritorio | No usable cómodamente desde celular |
| Sin rate limiting afinado en login/registro | Solo el throttle básico por defecto de Laravel | Vulnerable a fuerza bruta si no se refuerza en producción |
| ~~Reportes exportables~~ | ✅ **Resuelto.** `Admin\ReportController` con vista `admin/reports/index.blade.php` muestra ventas por período/categoría/producto con filtro de fechas y exportación CSV. Rutas `admin.reports.index` y `admin.reports.export`. Sin dependencias externas (CSV nativo con BOM UTF-8). | — |
| ~~Gestión de inventario~~ | ✅ **Resuelto.** Migraciones: `min_stock`/`restock_quantity` en products, `product_variants` (talla+color con stock/precio propio), `stock_movements` (traza completa), `variant_id` en order_items y cart_items. Modelos `ProductVariant`, `StockMovement`. Servicio `StockService` registra movimientos automáticos al vender/reabastecer/ajustar. Admin: gestión de variantes, historial de movimientos con filtros, alertas de reabastecimiento en dashboard e inventario. Frontend: selectores dinámicos talla/color en producto con precio y stock en vivo. | — |
| ~~Multi-imagen con zoom y video~~ | ✅ **Resuelto.** Migración `video_url` en products. Galería con thumbnails clickables (Alpine.js), zoom al pasar mouse con lupa + vista ampliada a la derecha (CSS `background-size` + `transform`). Soporte de video YouTube/Vimeo/MP4 con overlay y autoplay. Admin: campo `video_url` en el formulario de producto. | — |

**Regla para la IA:** si el usuario pide una feature que toca una de estas áreas, avísale explícitamente que estás resolviendo también una deuda técnica conocida (no lo hagas en silencio), y confirma el alcance antes de tocar relaciones de base de datos con `cascadeOnDelete` — un cambio ahí puede ser irreversible sobre datos reales.

---

## 3. Reglas de seguridad no negociables

1. **Nunca elimines o debilites el middleware `role:admin,trabajador` o `role:admin`** de las rutas `/admin/*` sin que el usuario lo pida explícita y conscientemente.
2. **Nunca expongas rutas de gestión de usuarios (`/admin/users/*`) a rol `trabajador`.** Solo `admin` debe poder crear/modificar/eliminar usuarios y cambiar roles.
3. **Nunca quites `@csrf` de un formulario existente** ni generes formularios nuevos sin él.
4. **Nunca guardes contraseñas en texto plano.** Usa siempre `Hash::make()` o el cast `'password' => 'hashed'` ya configurado en el modelo `User`.
5. **Nunca hagas login automático o bypass de autenticación "para pruebas"** que quede en el código de producción, ni siquiera si el usuario lo pide para "probar más rápido" — ofrece alternativas (ej. seeders con cuentas demo, que ya existen).
6. **Si agregas subida de archivos nueva**, sigue el patrón ya usado (`Storage::disk('public')`, validación `image|max:4096`), no guardes archivos fuera de `storage/app/public` sin justificación.

---

## 4. Reglas de datos no negociables

1. **No cambies el tipo de dato de `products.price`/`compare_price`/`order_items.unit_price`** (decimal 10,2) sin considerar el impacto en cálculos monetarios existentes.
2. **`OrderItem` y `Order` son snapshots históricos.** Si modificas `Product`, nunca hagas que `OrderItem` "lea en vivo" del producto actual — el diseño actual guarda `product_name` y `unit_price` congelados a propósito, para que el historial de ventas no cambie retroactivamente si el producto se edita o borra después.
3. **`order_number` debe seguir siendo único.** Si cambias su formato (`RAR-XXXXXXXX`), asegúrate de mantener la unicidad (hoy se logra con `Str::random(8)`, que no garantiza unicidad matemática — si el volumen de pedidos crece mucho, considera agregar una verificación de colisión o usar un secuencial).
4. **No agregues campos a `attributes` (JSON) de `Product` asumiendo una estructura fija.** Es un campo libre clave-valor por diseño, para soportar cualquier tipo de producto (moda, tecnología, hogar, etc.) sin rígidez de esquema.
5. **El descuento de un cupón SIEMPRE debe revalidarse en el servidor al momento de confirmar el pedido** (nunca confiar en el descuento calculado previamente en sesión/frontend). Sigue el mismo principio ya aplicado a validación de stock: `CheckoutController@store` recalcula el descuento con `Coupon::calculateDiscount()` incluso si el cupón ya se validó antes vía AJAX. El valor en sesión (`applied_coupon`) es solo una referencia al ID del cupón, no un monto precalculado.

---

## 5. Convenciones a respetar al agregar código nuevo

- **Nombres de ruta:** sigue el patrón `recurso.accion` y, si es del panel admin, prefijo `admin.`.
- **Controladores del admin:** van en `App\Http\Controllers\Admin\`, no los mezcles con los controladores públicos.
- **Vistas del admin:** van en `resources/views/admin/`, extienden `layouts.admin`, no `layouts.app`.
- **Vistas públicas:** extienden `layouts.app`.
- **Colores:** usa siempre los tokens Tailwind ya definidos (`rar-50` a `rar-900`, `cobre-50/100/200/500/600/700/800`) — no introduzcas colores nuevos sueltos (ej. `bg-blue-600` directo) que rompan la consistencia de marca. Si necesitas un tono nuevo, agrégalo a la config de `tailwind.config` en **ambos** layouts (`app.blade.php` y `admin.blade.php` los definen por separado, ya que no hay un archivo de config central compartido al usar Tailwind vía CDN).
- **Feedback al usuario:** los mensajes de éxito se muestran como **toast flotante** (esquina superior derecha, fondo `bg-rar-600`, auto-dismiss 4s) usando el patrón existente de `session('success')`. No inventes un sistema de notificaciones paralelo.
- **Validación:** sigue el patrón de `$request->validate([...])` inline en el controlador, consistente con el resto del código, salvo que el usuario pida explícitamente migrar a Form Requests.

---

## 6. Checklist antes de entregar cualquier cambio

Antes de dar por terminada una tarea sobre este sistema, la IA debe verificar:

- [ ] ¿El cambio respeta los roles existentes (`admin`, `trabajador`, `cliente`) y no abre acceso no intencional?
- [ ] ¿Se usó PostgreSQL-compatible SQL (no `like` case-sensitive donde se necesita insensibilidad, no sintaxis MySQL-only)?
- [ ] ¿Se usó `protected $fillable` clásico en todos los modelos (nunca `#[Fillable]` de PHP 8)?
- [ ] ¿Los formularios nuevos tienen `@csrf` y, si corresponde, `@method`?
- [ ] ¿Se corrió `php -l` (o equivalente) sobre los archivos PHP nuevos/modificados para descartar errores de sintaxis?
- [ ] ¿Las rutas nuevas están correctamente nombradas y agrupadas bajo el middleware correcto?
- [ ] ¿Se avisó al usuario si el cambio toca una de las "brechas conocidas" de la sección 2?
- [ ] ¿Se mantuvo la paleta de colores/marca definida en `03-UXUI.md`?
- [ ] Si se tocó una migración con `cascadeOnDelete`, ¿se confirmó explícitamente con el usuario que ese comportamiento es el deseado?

---

## 7. Qué hacer si el usuario pide algo que contradice este documento

Si el usuario pide explícitamente algo que rompe una regla de esta guía (por ejemplo, "quita la validación de rol admin para que cualquiera entre al panel"), la IA debe:
1. Cumplir la solicitud si es una decisión de negocio legítima del dueño del proyecto (no es una regla de seguridad universal externa, es una guía interna del propio proyecto).
2. Pero **debe advertir explícitamente** la implicación de seguridad/datos antes de hacerlo, en una frase clara, no enterrada en el resto de la respuesta.

Este documento no reemplaza el criterio del usuario sobre su propio negocio — existe para que la IA no tome decisiones de alcance por su cuenta sin que el usuario las vea venir.

---

## 8. Dónde está la fuente de verdad de cada cosa

| Pregunta | Dónde mirar primero |
|---|---|
| ¿Qué hace el negocio y para quién? | `01-PRD.md` |
| ¿Cómo está construido técnicamente? | `02-TRD.md` |
| ¿Cómo debe verse y sentirse? | `03-UXUI.md` |
| ¿Cómo se mueve un usuario por el sistema? | `04-FLUJOS.md` |
| ¿Cómo está implementado el backend en detalle? | `05-BACKEND.md` |
| ¿Qué no debo romper o asumir? | Este documento (`06-CONTEXTO-PARA-IA.md`) |
| ¿Cuál es el comportamiento real del código, sin ambigüedad? | El código fuente en `app/`, `routes/web.php`, `database/migrations/` — estos documentos resumen, el código manda |
