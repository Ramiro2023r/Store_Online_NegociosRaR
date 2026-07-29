# 09-PROMPTS-IMPLEMENTACION.md

Versión: 1.0

Estado: Plan de Implementación

Proyecto: Negocios RaR

Módulo: Asistente RaR

Proveedor IA: Groq

Modelo Inicial: Llama 3.3

---

# OBJETIVO

Este documento define el orden exacto de implementación del Asistente RaR.

Toda IA de programación (Claude Code, Cursor, Codex CLI, Gemini CLI, Grok Code, Kilo Code, etc.) deberá seguir este orden.

No está permitido cambiar el orden de las fases.

Cada fase debe estar completamente terminada antes de iniciar la siguiente.

---

# REGLAS GENERALES

Antes de ejecutar cualquier implementación la IA deberá leer completamente los siguientes documentos:

01-PRD.md

02-TRD.md

03-UXUI.md

04-FLUJOS.md

05-BACKEND.md

06-CONTEXTO-PARA-IA.md

07-SEGURIDAD.md

08-ASISTENTE-RAR.md

Después de leer la documentación podrá ejecutar únicamente la fase solicitada.

Nunca deberá implementar funcionalidades fuera del alcance de la fase actual.

Nunca deberá eliminar código existente.

Nunca deberá modificar la arquitectura general.

Nunca deberá escribir SQL manual.

Toda lógica deberá pasar por Services.

Toda modificación deberá respetar Policies y Gates existentes.

---

# FASE 1

## Crear el módulo Asistente RaR

Objetivo

Crear únicamente la estructura del módulo.

Debe crear:

app/AssistantRAR

app/AssistantRAR/Controllers

app/AssistantRAR/Services

app/AssistantRAR/Repositories

app/AssistantRAR/Providers

app/AssistantRAR/Tools

app/AssistantRAR/Context

app/AssistantRAR/Memory

app/AssistantRAR/Prompts

app/AssistantRAR/Models

app/AssistantRAR/DTO

app/AssistantRAR/Contracts

app/AssistantRAR/Exceptions

app/AssistantRAR/Events

app/AssistantRAR/Listeners

No implementar lógica.

Solo crear la estructura.

Estado esperado:

✓ El proyecto compila.

✓ No rompe ninguna funcionalidad existente.

---

# FASE 2

## Base de Datos

Crear todas las migraciones necesarias.

Tablas:

assistant_conversations

assistant_messages

assistant_tool_logs

assistant_preferences

assistant_sessions

assistant_memories

assistant_feedback

No crear datos de prueba.

No crear seeders.

Estado esperado:

✓ Todas las migraciones funcionan.

---

# FASE 3

## Modelos

Crear todos los modelos Eloquent.

Definir relaciones.

Crear Casts.

Scopes.

Factories.

No implementar IA.

---

# FASE 4

## Servicios

Crear:

AssistantService

ConversationService

MemoryService

ContextService

PromptBuilder

ToolRegistry

ToolExecutor

ProviderManager

StreamingService

Todos deberán tener Interfaces.

No implementar llamadas HTTP.

---

# FASE 5

## Chat

Crear el Chat flotante.

Debe funcionar en:

Administrador

Trabajador

Cliente

Características:

Chat lateral.

Resizable.

Minimizable.

Responsive.

Historial.

Nueva conversación.

Eliminar conversación.

Streaming preparado.

Sin IA todavía.

---

# FASE 6

## Integración Groq

Crear:

GroqProvider

Implementar:

Autenticación.

Variables .env

Manejo de errores.

Rate Limit.

Timeout.

Streaming.

No implementar herramientas.

Solo conversación.

---

# FASE 7

## Contexto

Antes de cada petición construir automáticamente:

Usuario.

Rol.

Permisos.

Página actual.

Empresa.

Idioma.

Fecha.

Hora.

Historial.

Memoria.

Herramientas disponibles.

Enviar ese contexto al proveedor IA.

---

# FASE 8

## Sistema de Herramientas

Crear Tool Registry.

Cada herramienta deberá registrarse automáticamente.

Ejemplo:

buscarProducto()

crearProducto()

editarProducto()

consultarVentas()

crearBanner()

etc.

No implementar todavía.

Solo registrar.

---

# FASE 9

## Herramientas Productos

Implementar:

Buscar

Crear

Editar

Eliminar

Duplicar

Actualizar Precio

Actualizar Stock

Cambiar Estado

Todo utilizando ProductService.

---

# FASE 10

## Herramientas Categorías

Crear.

Editar.

Eliminar.

Buscar.

---

# FASE 11

## Herramientas Marcas

Implementar.

---

# FASE 12

## Herramientas Inventario

Implementar.

---

# FASE 13

## Herramientas Pedidos

Consultar.

Editar.

Actualizar Estado.

Cancelar.

Timeline.

---

# FASE 14

## Herramientas Usuarios

Consultar.

Crear.

Editar.

Bloquear.

Cambiar Rol.

---

# FASE 15

## Herramientas Reportes

Ventas.

Clientes.

Inventario.

Newsletter.

Pedidos.

Exportaciones.

---

# FASE 16

## IA Cliente

Habilitar únicamente herramientas permitidas.

No mostrar información administrativa.

Agregar recomendaciones.

Ayuda Checkout.

Carrito.

Wishlist.

Pedidos.

Puntos.

Comparador.

---

# FASE 17

## IA Trabajador

Herramientas limitadas.

Sin acceso a Configuración.

Sin acceso Usuarios.

---

# FASE 18

## IA Administrador

Acceso completo.

Todas las herramientas.

Indicadores.

Dashboard.

Análisis.

Reportes.

---

# FASE 19

## Seguridad

Registrar todas las acciones.

Crear auditoría.

Crear logs.

Agregar confirmaciones.

Implementar doble confirmación para operaciones destructivas.

---

# FASE 20

## Memoria Inteligente

Cada usuario tendrá memoria independiente.

Nunca compartir conversaciones.

Nunca compartir contexto.

Nunca compartir preferencias.

---

# FASE 21

## Optimización

Cache.

Streaming.

Reducción Tokens.

Lazy Context.

Compresión Historial.

---

# FASE 22

## Testing

Unitarios.

Feature.

Integración.

Carga.

Seguridad.

---

# FASE 23

## Documentación

Actualizar documentación.

Actualizar Changelog.

Actualizar arquitectura.

---

# DEFINICIÓN DE TERMINADO

El Asistente RaR estará terminado cuando:

✓ Funcione para Cliente.

✓ Funcione para Trabajador.

✓ Funcione para Administrador.

✓ Toda acción pase por Services.

✓ No exista acceso SQL directo.

✓ Toda acción quede registrada.

✓ Toda acción respete permisos.

✓ Todas las conversaciones sean independientes.

✓ El proveedor IA pueda cambiarse sin modificar el resto del sistema.

---

# NOTA PARA LA IA DE PROGRAMACIÓN

No implementar funcionalidades que no existan en Negocios RaR.

Nunca modificar la arquitectura del proyecto.

Nunca eliminar funcionalidades existentes.

Toda implementación deberá respetar completamente la documentación técnica del proyecto.