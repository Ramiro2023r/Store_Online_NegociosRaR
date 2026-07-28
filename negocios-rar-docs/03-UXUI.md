# UX/UI — Guía de Experiencia e Interfaz
## Negocios RaR — Plataforma de Tienda Virtual

**Versión:** 1.1
**Última actualización:** julio 2026

---

## 1. Principios de diseño

1. **Claridad sobre decoración:** cada pantalla debe dejar claro en menos de 3 segundos qué puede hacer el usuario ahí.
2. **Confianza visual:** el diseño debe transmitir seriedad (paleta corporativa, tipografía legible, jerarquía clara), como cualquier tienda online establecida.
3. **Fricción mínima en la compra:** del catálogo al checkout, cada paso adicional debe justificarse.
4. **Separación clara de mundos:** la tienda (pública, cálida, orientada a venta) y el panel admin (denso, orientado a datos y control) tienen lenguajes visuales distintos aunque comparten marca.
5. **Mobile-first en la tienda, desktop-first en el admin:** los clientes compran mayormente desde el celular; el equipo interno opera desde computadora.

---

## 2. Identidad visual

### 2.1 Logo
- Isotipo: letra "R" sobre fondo cuadrado con esquinas redondeadas, en azul.
- Logotipo completo: "Negocios" (azul marino oscuro) + "RaR" (azul royal), con línea inferior degradada azul→cobre como firma visual.
- Variantes disponibles: `Mejoradelogo.svg` (horizontal, a color), `Mejoradelogoiconoapp.svg` (isotipo cuadrado para favicon y sidebar admin), `Mejoradelogoiconoapp.png` (fallback para favicon).
- **Favicon:** se usa `Mejoradelogoiconoapp.svg` (type `image/svg+xml`) en ambos layouts.
- **Regla de uso:** como el logo no tiene fondo transparente, sobre fondos oscuros (footer, sidebar admin) siempre se coloca dentro de un contenedor blanco redondeado (chip), nunca directamente sobre el color oscuro.

### 2.2 Paleta de colores

| Token | Hex | Uso |
|---|---|---|
| `rar-900` | `#0F1F3D` | Fondos oscuros: footer, sidebar admin, barra superior |
| `rar-700` | `#0D3B6E` | Hover de botones primarios / estados activos |
| `rar-600` | `#1B4F91` | Color primario: botones CTA, enlaces activos, precios destacados |
| `rar-500` | `#2F6FB0` | Elementos secundarios, iconografía |
| `rar-400` | `#5F8BCD` | Bordes hover, acentos claros |
| `rar-100` | `#DCE7F6` | Fondos suaves (tags, hover ligero) |
| `rar-50` | `#EEF3FB` | Fondos de sección alternos |
| `cobre-500` | `#B0876C` | Acento puntual (detalles, badges, no dominante) |
| `cobre-600` | `#9A6F56` | Texto de acento, stock bajo |
| `cobre-700` | `#7D5A45` | Hover de acento |
| Neutros | escala `gray-*` de Tailwind | Texto secundario, bordes, fondos base |
| Estado éxito | `green-600` / `green-100` | Confirmaciones, pedidos entregados |
| Estado alerta | `cobre-600` / `cobre-100` | Stock bajo, pedidos pendientes |
| Estado error | `red-600` / `red-100` | Errores, pedidos cancelados |

> **Nota:** los colores se definen en el `tailwind.config` inline de los layouts `app.blade.php` y `admin.blade.php`. Cualquier nuevo tono debe agregarse en **ambos** archivos.

### 2.3 Tipografía
- Fuente del sistema (sans-serif por defecto de Tailwind) para toda la interfaz — prioriza legibilidad y velocidad de carga sobre personalización tipográfica.
- Jerarquía: `text-2xl`/`text-3xl` font-bold para títulos de página, `text-sm`/`text-xs` para metadatos y ayudas.

### 2.4 Componentes visuales base
- Bordes redondeados consistentes (`rounded-lg`/`rounded-xl`) en tarjetas, botones e inputs.
- Sombra sutil (`shadow-sm`/`hover:shadow-lg`) para indicar interactividad en tarjetas de producto.
- Badges de estado con fondo claro + texto de color fuerte (patrón `bg-X-100 text-X-700`) para pedidos, stock y disponibilidad.

---

## 3. Estructura de navegación (Tienda pública)

```
Navbar fijo (sticky)
├── Logo (link a Inicio)
├── Inicio
├── Productos
├── Acerca de
├── Contáctanos (requiere login; si no, invita a loguearse)
├── Buscador (desktop)
├── Ícono corazón "Lista de deseos" (visible solo si el usuario está logueado, con badge de conteo de productos guardados)
├── Ícono carrito (con badge de cantidad de productos, contador vía View Composer global)
└── Sesión
    ├── No logueado → botón "Iniciar sesión"
    └── Logueado → menú desplegable: Panel Admin (si aplica) / Mis pedidos / Chat / Cerrar sesión

Footer
├── Logo
├── Tienda (Productos, Acerca de, Contáctanos)
├── Mi cuenta (Iniciar sesión / Mis pedidos / Carrito)
├── Contacto (dirección, teléfono, correo)
└── Copyright + enlaces a Política de Privacidad y Términos y Condiciones
    ├── No logueado → botón "Iniciar sesión"
    └── Logueado → menú desplegable: Panel Admin (si aplica) / Mis pedidos / Chat / Cerrar sesión
```

## 4. Estructura de navegación (Panel Admin)

```
Sidebar fijo (dark, izquierda)
├── Logo (chip blanco)
├── Dashboard
├── Productos
├── Categorías
├── Ventas / Pedidos
├── Mensajes
├── Usuarios (solo visible para rol Admin)
├── — separador —
├── Ver tienda (vuelve al storefront)
└── Perfil + Cerrar sesión (pie del sidebar)
```

---

## 5. Pantallas clave y su propósito

| Pantalla | Objetivo UX |
|---|---|
| **Home** | Enganchar rápido: carrusel de promos, acceso directo por categoría, prueba social vía productos destacados |
| **Lista de deseos (wishlist)** | Reducir la fricción de "no estoy listo para comprar ahora pero no quiero perderlo": guardar productos con un clic (corazón), acceder después desde el navbar, y agregar al carrito directamente desde la lista sin tener que ir a la ficha |
| **Listado de productos** | Encontrar lo que se busca rápido: filtros persistentes en URL (compartibles), resultados claros con conteo |
| **Ficha de producto** | Resolver dudas de compra sin salir de la página: stock, atributos, relacionados, CTA prominente |
| **Carrito** | Confirmar qué se va a comprar antes de comprometerse: edición fácil de cantidades, total siempre visible |
| **Checkout** | Reducir abandono: formulario corto, resumen de pedido siempre visible al lado |
| **Mis pedidos** | Dar tranquilidad post-compra: estado visual claro con color |
| **Contáctanos (chat)** | Resolver dudas humanas rápido; gate de login refuerza que es un canal 1:1 con el negocio, no un formulario anónimo |
| **Dashboard admin** | Vista de "salud del negocio" en 5 segundos: ventas, pedidos, alertas |
| **Gestión de productos/categorías** | Operación diaria simple, sin curva de aprendizaje |
| **Gestión de pedidos** | Cambiar estado sin fricción (un solo select + botón) |

---

## 6. Patrones de interacción

- **Filtros de productos:** se aplican vía query params en la URL (`?category=&brand=&min_price=&max_price=&sort=`), lo que permite compartir/guardar enlaces de búsqueda y mantener estado al recargar.
- **Carrito accesible sin cuenta:** invitados usan un `session_id` en sesión de Laravel; al iniciar sesión, el flujo de checkout ya exige autenticación (esto es intencional: el carrito es exploratorio, el checkout es un compromiso).
- **Feedback de acciones:** mensajes de éxito vía `session('success')` mostrados como **toast flotante** en la esquina superior derecha, con fondo azul (`bg-rar-600`), auto-dismiss a los 4 segundos y cierre manual. Usa Alpine.js para la animación de entrada/salida.
- **Confirmaciones destructivas:** eliminar producto/categoría/usuario usa `confirm()` de JavaScript antes de enviar el formulario (protección mínima ante clics accidentales).
- **Estados vacíos:** listados sin resultados (carrito vacío, sin pedidos, sin mensajes) siempre muestran un mensaje + ilustración emoji + CTA de siguiente paso, nunca una pantalla en blanco.

---

## 7. Responsive

- **Tienda:** grid de productos pasa de 3–4 columnas (desktop) a 2 columnas (mobile); menú principal colapsa a menú hamburguesa; buscador de escritorio se oculta en mobile (queda accesible desde la página de productos).
- **Carrito/checkout:** en mobile, el resumen de pedido se apila debajo del formulario (en desktop va en columna lateral fija).
- **Admin:** diseñado primero para escritorio; el sidebar fijo de 256px asume pantallas ≥ 1024px. **Pendiente:** versión mobile del panel admin (no cubierta en el MVP).

---

## 8. Accesibilidad (estado actual y pendientes)

**Implementado:**
- Contraste de color validado en combinaciones principales (texto sobre `rar-900`, botones sobre blanco).
- Inputs con `<label>` asociado visualmente.

**Pendiente (a considerar en siguientes iteraciones):**
- Atributos `aria-*` explícitos en menús desplegables y modales.
- Navegación completa por teclado en el carrusel del home.
- Textos alternativos (`alt`) más descriptivos en imágenes de producto (actualmente usan el nombre del producto, lo cual es un buen mínimo pero podría enriquecerse).

---

## 9. Voz y tono de contenido

- Español neutro, cercano pero profesional (no jerga excesiva).
- Mensajes de sistema (errores, confirmaciones) claros y accionables: nunca solo "Error", siempre explicar qué pasó y qué hacer.
- CTAs en imperativo y específicos: "Agregar al carrito", "Confirmar pedido", "Ver productos" — nunca genéricos como "Enviar" a secas cuando se puede ser más claro.
