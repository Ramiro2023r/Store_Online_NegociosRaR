# SRD — Security Requirements Document
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 1.1
**Última actualización:** julio 2026
**Propósito:** define los requisitos, controles y responsabilidades de seguridad del sistema. Es de lectura obligatoria antes de: (a) desplegar a producción, (b) integrar una pasarela de pago, (c) dar acceso a nuevos usuarios internos, (d) que cualquier IA o desarrollador modifique áreas sensibles del código.

---

## 1. Alcance y clasificación de datos

| Tipo de dato | Ejemplos en el sistema | Clasificación |
|---|---|---|
| Datos de autenticación | Contraseñas (hasheadas), tokens de sesión | **Crítico** |
| Datos personales (PII) | Nombre, correo, teléfono, dirección de envío | **Sensible** |
| Datos comerciales | Pedidos, montos, historial de compra | **Sensible** |
| Datos de pago | Método de pago (texto), *no se almacenan tarjetas* | **Crítico (si se integra pasarela real)** |
| Comunicaciones | Mensajes del chat de soporte | **Sensible** |
| Datos de catálogo | Productos, precios, categorías | **Público** |

**Regla base:** todo dato clasificado como Crítico o Sensible requiere los controles de las secciones 2 a 7 antes de exponerse en cualquier API, export, log o integración nueva.

---

## 2. Autenticación

### 2.1 Estado actual implementado
- Contraseñas hasheadas automáticamente (cast `'password' => 'hashed'` de Laravel, algoritmo bcrypt por defecto).
- Recuperación de contraseña vía token de un solo uso con expiración (broker `Password` nativo de Laravel).
- Sesiones gestionadas por Laravel (`SESSION_DRIVER=database`), con regeneración de sesión en login (`$request->session()->regenerate()`) para prevenir *session fixation*.

### 2.2 Requisitos pendientes antes de producción
| Requisito | Estado | Prioridad |
|---|---|---|
| Política de contraseña mínima (longitud, complejidad) | No implementada (usa el default de Laravel: 8 caracteres) | **Alta** |
| Verificación de correo obligatoria antes de comprar | No implementada | **Alta** |
| Bloqueo de cuenta tras intentos fallidos (throttle afinado) | Solo throttle básico de Laravel, sin afinar | **Alta** |
| Verificación de `users.active` en el login | **No implementada — brecha conocida** (ver `06-CONTEXTO-PARA-IA.md`) | **Crítica** |
| Autenticación de dos factores (2FA) para roles admin/trabajador | No implementada | **Media** (recomendada antes de dar acceso a más de 1-2 administradores) |
| Registro/expiración de sesiones activas (cerrar sesión remota) | No implementada | Baja |

### 2.3 Regla no negociable
Ningún cambio de código debe debilitar el hashing de contraseñas, la regeneración de sesión en login, ni el mecanismo de expiración de tokens de reseteo de contraseña.

---

## 3. Autorización y control de acceso

### 3.1 Modelo actual
- Control por rol (`admin`, `trabajador`, `cliente`) vía middleware de ruta (`role:admin,trabajador`).
- Sin Policies de Laravel — la autorización vive únicamente en middleware de ruta, no a nivel de objeto individual (ej. no hay chequeo de "¿este pedido pertenece a este usuario?" más allá de comparar `user_id` manualmente en cada controlador).

### 3.2 Riesgos identificados
- **IDOR (Insecure Direct Object Reference) parcial:** rutas como `checkout.success`, `messages.show` o `reviews.destroy` comparan manualmente `$order->user_id === Auth::id()` o `$review->user_id === Auth::id()`. Esto funciona, pero **cualquier ruta nueva que reciba un ID de recurso debe replicar este patrón explícitamente** — no hay una capa automática (Policy) que lo garantice.
- **Escalación de privilegios:** solo el rol `admin` puede cambiar roles de otros usuarios (correcto), pero no hay protección contra que un `admin` se auto-degrade por error y quede el sistema sin administradores. Recomendado: no permitir que el último `admin` activo cambie su propio rol o se desactive.

### 3.3 Requisitos recomendados
- Migrar a Laravel Policies (`ProductPolicy`, `OrderPolicy`, etc.) si el número de roles o reglas de acceso crece.
- Agregar regla de "al menos un admin activo siempre debe existir" antes de permitir degradar/desactivar cuentas admin.

---

## 4. Protección contra vulnerabilidades web comunes (OWASP Top 10 — mapeo al estado actual)

| Riesgo OWASP | Estado en Negocios RaR |
|---|---|
| **Inyección SQL** | Mitigado — Eloquent ORM usa consultas parametrizadas en todo el código; no hay SQL crudo (`DB::raw`) con input de usuario sin sanitizar |
| **Autenticación rota** | Parcialmente mitigado — ver sección 2.2 para brechas pendientes |
| **Exposición de datos sensibles** | Contraseñas nunca se exponen (`$hidden` en modelo `User`); falta forzar HTTPS en producción (ver sección 6) |
| **XXE** | No aplica (no se procesa XML de usuarios) |
| **Control de acceso roto** | Ver sección 3 — mitigado a nivel de ruta, riesgo residual a nivel de objeto individual en rutas nuevas |
| **Configuración de seguridad incorrecta** | Pendiente checklist de producción (`APP_DEBUG=false`, cabeceras de seguridad — ver sección 6) |
| **Manipulación de precio/descuento del lado del cliente** | Mitigado — el sistema de cupones nunca recibe el monto de descuento desde el frontend. El código de cupón se envía al servidor, que recalcula el descuento siempre a partir del carrito real del usuario en ese momento. Doble validación: al aplicar el cupón (AJAX) y al confirmar el pedido (transacción). El `discount_amount` guardado en `Order` es el calculado por el servidor, no un valor enviado desde el formulario |
| **XSS (Cross-Site Scripting)** | Mitigado por defecto — Blade escapa automáticamente con `{{ }}`; **riesgo si se usa `{!! !!}` sin sanitizar contenido de usuario** (no se detectó uso actual, pero debe evitarse a futuro) |
| **Deserialización insegura** | No aplica directamente (no hay deserialización de objetos desde input externo) |
| **Componentes con vulnerabilidades conocidas** | Requiere proceso activo — ver sección 8 (gestión de dependencias) |
| **Logging y monitoreo insuficientes** | **No implementado** — no hay registro de eventos de seguridad (login fallido, cambios de rol, cambios de estado de pedido). Ver sección 7 |

---

## 5. Protección de formularios y sesión

### 5.1 CSRF
- Todos los formularios de escritura (`POST`/`PUT`/`PATCH`/`DELETE`) usan `@csrf`. **Regla no negociable:** ningún formulario nuevo puede omitirlo.

### 5.2 Subida de archivos (imágenes de producto)
- Validación actual: `image|max:4096` (4 MB, tipo imagen).
- **Riesgos no cubiertos todavía:**
  - No hay verificación de contenido real del archivo más allá del mime-type declarado (un atacante podría intentar subir un archivo malicioso con extensión de imagen falsificada).
  - No hay límite de cantidad total de imágenes por producto ni cuota de almacenamiento por cuenta.
- **Recomendación:** antes de abrir la subida de imágenes a más usuarios (ej. si en el futuro los `trabajador` suben catálogo masivamente), agregar validación adicional de contenido (`Illuminate\Validation\Rules\File` con chequeo de dimensiones/tipo real) y un antivirus/scanner si el volumen lo justifica.

### 5.3 Comentarios de reseñas (`reviews.comment`)
- Se renderiza con `{{ }}` en Blade (escape automático). **Regla: nunca usar `{!! !!}`** para mostrar el comentario de una reseña, ya que es contenido generado por el cliente.

### 5.4 JSON libre (`products.attributes`)
- Es un campo de estructura libre. **Riesgo:** si en algún punto se renderiza sin escapar en Blade (`{!! !!}`), podría ser vector de XSS almacenado. Actualmente se renderiza con `{{ }}` (seguro). Cualquier cambio a este campo debe mantener el escape automático.

---

## 6. Configuración de infraestructura y despliegue

### Checklist obligatorio antes de ir a producción

- [ ] `APP_ENV=production` y `APP_DEBUG=false` (evita exponer stack traces con rutas de servidor, queries, etc.)
- [ ] `APP_URL` configurado con HTTPS (`https://...`)
- [ ] Certificado SSL/TLS válido y forzado (redirección HTTP → HTTPS a nivel de servidor web)
- [ ] `SESSION_SECURE_COOKIE=true` (cookies de sesión solo por HTTPS)
- [ ] Variables de entorno (`.env`) fuera del control de versiones y con permisos de archivo restringidos en el servidor
- [ ] Base de datos PostgreSQL con usuario de aplicación de **privilegios mínimos** (no usar el superusuario `postgres` en producción)
- [ ] Backups automáticos de la base de datos (frecuencia mínima diaria) con prueba periódica de restauración
- [ ] Cabeceras de seguridad HTTP configuradas a nivel de servidor/proxy (Nginx/Apache): `Strict-Transport-Security`, `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Content-Security-Policy` (ajustada para permitir el CDN de Tailwind y Alpine.js que usa el proyecto)
- [ ] Rate limiting a nivel de servidor/proxy o Laravel (`throttle` middleware) afinado en `/login`, `/registro`, `/olvide-password`
- [ ] `php artisan config:cache`, `route:cache`, `view:cache` ejecutados (además de rendimiento, reduce superficie de exposición de configuración en caliente)
- [ ] Acceso administrativo al servidor (SSH, panel de hosting) restringido y con autenticación fuerte, independiente de la autenticación de la app

---

## 7. Logging, monitoreo y auditoría

### 7.1 Estado actual
- Laravel registra errores generales en `storage/logs/laravel.log` (canal `stack`/`single`).
- **No existe registro de eventos de seguridad de negocio:** quién cambió el rol de un usuario, quién cambió el estado de un pedido, intentos de login fallidos repetidos, accesos denegados por rol (403).

### 7.2 Requisitos recomendados
- Implementar un log de auditoría mínimo para acciones sensibles:
  - Cambios de rol de usuario (quién, a quién, cuándo, rol anterior → nuevo).
  - Cambios de estado de pedido.
  - Eliminación de productos, categorías o usuarios.
  - Intentos de acceso a `/admin/*` sin permisos suficientes (403).
- Configurar alertas (correo o similar) ante patrones anómalos: múltiples 403 seguidos desde la misma cuenta/IP, múltiples intentos fallidos de login.

---

## 8. Gestión de dependencias

- El proyecto depende de Composer (`laravel/framework`, `laravel/tinker`, etc.) y de dos CDNs externos en el frontend: Tailwind CSS y Alpine.js.
- **Riesgo de las dependencias vía CDN:** si el CDN es comprometido o cambia el contenido servido, afecta directamente a la tienda (riesgo de *supply chain*). Es aceptable para MVP/desarrollo; **antes de producción con tráfico real, se recomienda auto-alojar Tailwind (compilado) y Alpine.js** en vez de depender de CDNs de terceros en tiempo de ejecución.
- **Proceso recomendado:** ejecutar `composer audit` periódicamente (o integrarlo a CI/CD) para detectar vulnerabilidades conocidas en dependencias PHP.

---

## 9. Seguridad específica de e-commerce / pagos

**Estado actual:** el sistema **procesa pagos con tarjeta a través de Culqi** (integración via API REST). Los datos de tarjeta nunca llegan al servidor propio — Culqi los tokeniza directamente desde el frontend. En modo test (llaves `pk_test_*`/`sk_test_*`) no se procesan cobros reales. Para producción, se requieren llaves LIVE (`pk_live_*`/`sk_live_*`) y verificación de negocio en Culqi.

**Antes de integrar una pasarela de pago real (Culqi, Mercado Pago, Stripe, etc.):**
- **Nunca** almacenar número de tarjeta, CVV o datos sensibles de pago en la base de datos propia — usar siempre tokenización del lado del proveedor de pagos.
- Si se procesan tarjetas de cualquier forma (incluso tokenizadas), evaluar el alcance de cumplimiento **PCI DSS** correspondiente (la mayoría de negocios pequeños con tokenización delegada caen en el nivel más bajo de requisitos, pero debe confirmarse con el proveedor de pagos elegido).
- Validar montos y estados de pedido **siempre del lado del servidor**, nunca confiar en el monto que llegue desde el frontend en el checkout.
- Implementar verificación de webhooks (firma/secreto) si la pasarela notifica pagos de forma asíncrona, para evitar que alguien simule una notificación de "pago exitoso" falsa.

---

## 10. Cumplimiento legal — Protección de datos personales (Perú)

Dado que el negocio opera en Perú (Lima), aplica la **Ley N° 29733 – Ley de Protección de Datos Personales** y su reglamento, supervisada por la Autoridad Nacional de Protección de Datos Personales.

### Estado actual:
- [x] Política de Privacidad y Términos y Condiciones publicados en `/politica-de-privacidad` y `/terminos-y-condiciones`.
- [x] Consentimiento explícito del cliente durante el registro (checkbox obligatorio). Se registran `accepted_terms_at` (timestamp) y `accepted_terms_version` en la tabla `users`.

### Requisitos a cubrir antes de operar con clientes reales:
- [x] Integración de pasarela de pago Culqi implementada (tokenización desde frontend, cargo via API).
- [ ] Obtener llaves LIVE de Culqi (requiere verificación de negocio en panel.culqi.com) y reemplazarlas en `.env`.
- [ ] Configurar webhooks de Culqi para notificaciones de pago asíncronas.
- [ ] Inscripción del banco de datos personales ante la autoridad competente, si corresponde según el volumen y tipo de datos tratados.
- [ ] Mecanismo para que el cliente pueda solicitar acceso, rectificación, cancelación u oposición (derechos ARCO) sobre sus datos.
- [ ] Definición de tiempo de retención de datos (ej. ¿por cuánto tiempo se conservan pedidos, mensajes de chat, cuentas inactivas?). Actualmente no hay política ni proceso de purga de datos.

**Nota:** este documento señala los requisitos técnicos y de proceso; la redacción legal de la política de privacidad y términos y condiciones debe hacerla o revisarla un abogado, no una IA.

---

## 11. Plan de respuesta ante incidentes (mínimo recomendado)

Actualmente **no existe un plan formal**. Se recomienda definir, aunque sea a nivel básico, antes de operar con clientes reales:

1. **Detección:** ¿quién revisa los logs? ¿con qué frecuencia?
2. **Contención:** procedimiento para desactivar una cuenta comprometida (`active = false` + forzar cierre de sesión — hoy `active` no fuerza logout automático, ver sección 2.2).
3. **Notificación:** a quién se avisa internamente y, si corresponde legalmente, a los clientes afectados y a la autoridad de protección de datos.
4. **Recuperación:** restauración desde backup, rotación de credenciales/API keys si se sospecha compromiso.
5. **Post-mortem:** qué se ajusta en el sistema o proceso para que no vuelva a pasar.

---

## 12. Checklist de seguridad para cualquier IA antes de tocar código

Complementa (no reemplaza) el checklist general de `06-CONTEXTO-PARA-IA.md`:

- [ ] ¿El cambio introduce una nueva ruta que recibe un ID de recurso (pedido, mensaje, producto)? → Debe verificar explícitamente que el recurso pertenece al usuario autenticado, salvo que sea intencionalmente admin-only.
- [ ] ¿El cambio agrega un formulario nuevo? → Debe llevar `@csrf`.
- [ ] ¿El cambio renderiza contenido generado por usuarios (nombre, mensaje de chat, atributos de producto)? → Debe usar `{{ }}` de Blade, nunca `{!! !!}` con datos no confiables.
- [ ] ¿El cambio toca autenticación, sesión, o el campo `active`/`role` de `User`? → Requiere confirmación explícita del usuario dueño del proyecto antes de aplicarse (ver regla de la sección 2.2 y 3.3).
- [ ] ¿El cambio agrega manejo de pagos o datos financieros nuevos? → Aplica íntegramente la sección 9 antes de escribir una sola línea de código.
- [ ] ¿El cambio agrega recolección de nuevos datos personales del cliente? → Debe evaluarse contra la sección 10 (¿hace falta actualizar la política de privacidad? ¿hace falta consentimiento explícito?).

---

## 13. Resumen de prioridades (para planificar el roadmap de seguridad)

| Prioridad | Acción |
|---|---|
| 🔴 Crítica | Verificar `users.active` en el login antes de dar acceso a clientes reales |
| 🔴 Crítica | Checklist completo de producción (sección 6) antes de salir en vivo |
| 🔴 Crítica | Política de privacidad + consentimiento (sección 10) antes de operar con clientes reales |
| 🟠 Alta | Verificación de correo obligatoria + política de contraseñas |
| 🟠 Alta | Logging de auditoría mínimo (sección 7) |
| 🟠 Alta | Definir estrategia de pagos segura (sección 9) antes de integrar cualquier pasarela |
| 🟡 Media | 2FA para roles admin/trabajador |
| 🟡 Media | Auto-alojar Tailwind/Alpine.js en vez de CDN en producción |
| 🟢 Baja | Migrar autorización a Policies de Laravel si el sistema crece en complejidad de roles |
