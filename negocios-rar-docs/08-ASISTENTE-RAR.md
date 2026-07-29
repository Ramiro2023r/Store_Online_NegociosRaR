# 08-ASISTENTE-RAR.md

Versión: 1.1.0

Estado: Implementado (v1.0)

Proyecto: Negocios RaR

Módulo: Asistente RaR

Autor: Equipo Negocios RaR

---

# CAPÍTULO 1

# VISIÓN GENERAL

## ¿Qué es Asistente RaR?

Asistente RaR es el asistente inteligente oficial integrado dentro de la plataforma Negocios RaR.

No es un chatbot tradicional.

No es un asistente genérico.

No responde utilizando únicamente conocimiento general.

Asistente RaR es un agente inteligente diseñado específicamente para comprender el funcionamiento completo del sistema Negocios RaR y asistir a cada usuario según su identidad, permisos, contexto y necesidades.

Su comportamiento dependerá completamente del usuario autenticado, del módulo donde se encuentre y de las herramientas autorizadas disponibles para dicho usuario.

---

# Filosofía

La filosofía del proyecto es construir un asistente que funcione como un miembro más del sistema.

El asistente nunca reemplaza la lógica del negocio.

Nunca modifica la arquitectura existente.

Nunca escribe directamente sobre la base de datos.

Nunca ejecuta SQL.

Nunca toma decisiones fuera de los permisos del usuario.

Toda acción será ejecutada utilizando únicamente los Services oficiales del proyecto Laravel.

---

# Objetivos

Los objetivos principales del Asistente RaR son:

• Facilitar la administración del negocio.

• Mejorar la experiencia del cliente.

• Reducir el tiempo necesario para realizar tareas repetitivas.

• Permitir consultas utilizando lenguaje natural.

• Automatizar tareas administrativas.

• Incrementar la productividad del personal.

• Centralizar la interacción inteligente dentro del sistema.

---

# Alcance

El Asistente RaR podrá interactuar únicamente con los módulos existentes del sistema.

No podrá crear funcionalidades nuevas.

No podrá modificar la arquitectura del proyecto.

No podrá ejecutar código arbitrario.

No podrá acceder directamente al servidor.

No podrá instalar paquetes.

No podrá modificar archivos del proyecto.

Su única capacidad será utilizar las herramientas oficiales desarrolladas por Negocios RaR.

---

# Tipos de Asistente

Existe un único Asistente RaR.

Sin embargo, su comportamiento cambia dependiendo del usuario autenticado.

No existen múltiples asistentes.

Existe un solo motor inteligente.

Cada usuario obtiene una instancia personalizada del Asistente.

Esto significa que:

Administrador Ramiro posee un contexto diferente al Cliente Juan.

Aunque ambos utilicen el mismo modelo de IA, sus permisos, memoria, conversaciones y herramientas son completamente independientes.

---

# Principio de Contexto

Toda conversación debe comenzar construyendo el contexto del usuario.

Ejemplo:

Usuario:

Ramiro Acosta

Rol:

Administrador

Página actual:

Productos

Empresa:

Negocios RaR

Idioma:

Español

Herramientas disponibles:

Productos
Pedidos
Clientes
Reportes
Inventario
Banners
Usuarios

Historial:

Disponible

Memoria:

Disponible

Solo después de construir dicho contexto se enviará la petición al proveedor de IA.

---

# Principio de Seguridad

La IA nunca tendrá acceso directo a PostgreSQL.

La IA nunca ejecutará consultas SQL.

La IA nunca ejecutará comandos Artisan.

La IA nunca ejecutará comandos del sistema operativo.

La IA únicamente podrá solicitar la ejecución de herramientas previamente registradas.

Laravel será quien valide permisos y ejecute la acción correspondiente.

---

# Principio de Responsabilidad

El Asistente RaR piensa.

Laravel decide.

Los Services ejecutan.

La Base de Datos almacena.

Cada componente tiene una única responsabilidad.

---

# Principio de Escalabilidad

Todo el módulo será independiente del proveedor de IA.

Inicialmente se utilizará:

Proveedor:

Groq

Modelo:

Llama 3.3

Sin embargo, el sistema permitirá cambiar posteriormente a:

OpenAI

Gemini

Claude

Ollama

Mistral

DeepSeek

sin modificar el resto del proyecto.

---

# Usuarios soportados

El Asistente RaR reconoce tres tipos principales de usuarios.

## Cliente

Puede:

Buscar productos.

Consultar pedidos.

Consultar puntos.

Agregar productos al carrito.

Gestionar wishlist.

Comparar productos.

Consultar promociones.

Recibir recomendaciones.

Ayuda durante el checkout.

No puede acceder a información administrativa.

---

## Trabajador

Puede:

Consultar pedidos.

Actualizar estados.

Consultar inventario.

Crear productos.

Editar productos.

Consultar clientes.

No puede administrar usuarios.

No puede modificar configuraciones generales.

---

## Administrador

Posee acceso completo al sistema.

Puede utilizar todas las herramientas registradas.

Puede consultar indicadores.

Puede crear elementos.

Puede editar elementos.

Puede eliminar elementos.

Siempre bajo confirmación cuando corresponda.

---

# Identidad del Asistente

Nombre oficial:

Asistente RaR

Descripción:

Asistente inteligente oficial de Negocios RaR.

Personalidad:

Profesional.

Clara.

Precisa.

Respetuosa.

Nunca responde información inventada.

Nunca oculta errores.

Siempre explica cuando una operación no puede realizarse.

---

# Regla Fundamental

El Asistente RaR jamás improvisa funcionalidades.

Si una herramienta no existe responderá:

"La funcionalidad solicitada aún no forma parte del sistema Negocios RaR."

Nunca intentará inventar procesos inexistentes.

---

Fin del Capítulo 1

---

# CAPÍTULO 2

# ESTADO DE IMPLEMENTACIÓN v1.0

## Resumen

El Asistente RaR fue implementado siguiendo el plan de 23 fases definido en `09-PROMPTS-IMPLEMENTACION-Asistente.md`.
Las fases 1 a 22 están completas. La fase 23 (documentación) está en curso.

## Estructura del módulo

```
app/AssistantRAR/
├── Controllers/
│   └── AssistantController.php        → 9 endpoints REST (/asistente/*)
├── Services/
│   ├── AssistantService.php           → Orquestador de mensajes (procesa y stremea)
│   ├── ConversationService.php        → CRUD conversaciones + mensajes
│   ├── ContextService.php             → Contexto enriquecido pre-petición
│   ├── MemoryService.php              → Memoria persistente por usuario
│   ├── PromptBuilder.php              → Construcción del system prompt
│   ├── ProviderManager.php            → Llamadas HTTP a Groq/OpenAI con streaming SSE
│   ├── StreamingService.php           → Servicio de SSE
│   ├── ToolRegistry.php               → Registro y consulta de herramientas
│   └── ToolExecutor.php               → Ejecución con logging y permisos
├── Models/
│   ├── AssistantConversation.php
│   ├── AssistantMessage.php
│   ├── AssistantToolLog.php
│   ├── AssistantPreference.php
│   ├── AssistantSession.php
│   ├── AssistantMemory.php
│   └── AssistantFeedback.php
├── Tools/
│   ├── BaseTool.php                   → Clase abstracta base
│   ├── Product*.php (8)               → Búsqueda, CRUD, precio, stock, estado
│   ├── Category*.php (5)              → Búsqueda, CRUD, estado
│   ├── BrandSearchTool.php
│   ├── Inventory*.php (5)             → Stock bajo, agotados, movimientos, ajuste, mínimo
│   ├── Order*.php (4)                 → Búsqueda, detalle, estado, timeline
│   ├── User*.php (6)                  → Búsqueda, crear, actualizar, rol, bloquear
│   ├── Report*.php (6)                → Ventas, productos, inventario, clientes, pedidos, CSV
│   ├── CartTool.php                   → Stub
│   ├── LoyaltyTool.php                → Stub
│   ├── SupportTool.php                → Stub
│   └── SystemTool.php                 → Stub
├── Contracts/
│   ├── IAssistantService.php
│   ├── IAssistantTool.php
│   ├── IConversationService.php
│   ├── IContextService.php
│   ├── IMemoryService.php
│   ├── IPromptBuilder.php
│   ├── IProviderManager.php
│   ├── IStreamingService.php
│   ├── IToolExecutor.php
│   └── IToolRegistry.php
├── DTO/
│   └── ToolResult.php
├── Providers/
│   └── AssistantRARServiceProvider.php
└── (Exceptions/, Events/, Listeners/ — preparados para uso futuro)
```

## Services de aplicación creados

- `App\Services\ProductService` — CRUD + precio + stock + estado + duplicado
- `App\Services\CategoryService` — CRUD + estado
- `App\Services\BrandService` — Búsqueda de marcas distintas
- `App\Services\InventoryService` — Stock bajo, agotados, movimientos, ajuste, stock mínimo
- `App\Services\OrderService` — Búsqueda, detalle, cambio de estado con notificación + puntos, timeline
- `App\Services\UserService` — Búsqueda, crear trabajador, actualizar, rol, bloquear, link reseteo
- `App\Services\ReportService` — Ventas, top productos, inventario, clientes, pedidos

## Proveedor IA

| Propiedad | Valor |
|-----------|-------|
| Proveedor inicial | Groq |
| Modelo | `llama-3.3-70b-versatile` |
| Config | `config/assistant.php` vía ENV |
| Streaming | SSE (Server-Sent Events) |
| Fallback | OpenAI |

## Chat flotante

- Implementado con Alpine.js en `resources/views/partials/assistant-chat.blade.php`
- Incluido en `layouts/admin.blade.php` y `layouts/app.blade.php`
- Características: toggle, lista de conversaciones, burbujas, typing indicator, scroll automático, timeAgo

## Herramientas registradas (41)

Cada herramienta tiene: nombre, descripción, esquema JSON, roles permitidos y nivel de confirmación (0=ninguna, 1=simple, 2=doble).

## Seguridad

- Toda ejecución se registra en `assistant_tool_logs` con usuario, IP, user-agent
- Operaciones destructivas requieren confirmación nivel 2
- Las herramientas validan roles antes de ejecutar
- La memoria y conversaciones están aisladas por usuario