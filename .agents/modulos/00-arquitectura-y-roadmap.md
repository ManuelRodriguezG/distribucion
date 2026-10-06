# Modulo 00 - Arquitectura y roadmap

## Objetivo

Definir y mantener la arquitectura general del proyecto Distribucion, sus fases, limites con ERP y criterios de avance.

## Decision base

- Distribucion vive como proyecto separado en `C:\xampp\htdocs\distribucion`.
- Host local: `http://distribucion.artiani.com.local`.
- Produccion futura: `https://distribucion.artiani.com.mx`.
- ERP principal vive en `C:\xampp\htdocs\panel_de_control`.
- ERP expone API; Distribucion consume.
- Responsabilidades del ERP separadas en `docs/RESPONSABILIDADES_ERP_DISTRIBUCION.md`.
- Guia de implementacion MVP de Distribucion en `docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md`.
- Roadmap paralelo ERP/Distribucion en `docs/ROADMAP_PARALELO_ERP_DISTRIBUCION.md`.
- Contrato minimo API MVP en `docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md`.
- Decisiones tecnicas MVP en `docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md`.
- Alcance funcional MVP por pantalla en `docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md`.
- Criterios de aceptacion MVP en `docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md`.
- Primeros pasos reales de implementacion en `docs/PRIMEROS_PASOS_IMPLEMENTACION_MVP.md`.
- Cierre de arquitectura Chat 00 en `docs/CIERRE_ARQUITECTURA_CHAT_00.md`.

## Fases

### Fase 0 - Base de proyecto

Estado: iniciada.

Incluye:

- Estructura MVC.
- Configuracion de dominio local.
- Cliente API.
- Sesion externa.
- Vistas iniciales.
- Documento de contrato API.
- AGENTS y contexto modular.

### Fase 1 - API ERP read-only

Crear en ERP:

- `DistribucionApi.php`.
- Modelos de catalogo, permisos, precios y disponibilidad.
- `contratos`.
- `configuracion_inicial`.
- `catalogo`.
- `producto/{slug}`.
- `categorias`.
- `marcas`.
- `filtros`.

### Fase 2 - Registro y aprobacion

Crear flujo:

- Solicitud de acceso.
- Login externo.
- Estatus pendiente/aprobado/suspendido.
- Admin ERP aprueba y asigna tipo/lista/permisos.

### Fase 3 - Catalogo visual

Construir UX:

- Busqueda.
- Filtros.
- Tarjetas.
- Detalle.
- Variantes/presentaciones/paquetes.
- Mensajes segun permisos.

### Fase 4 - Precios y disponibilidad

Resolver:

- Precio publico.
- Precio lista asignada.
- Ocultar precio.
- Disponibilidad comercial.

### Fase 5 - Cotizaciones

Implementar:

- Carrito/lista de cotizacion.
- Dry-run.
- Registro con folio.
- Bandeja interna ERP.

### Fase 6 - Prepedido/Pedido futuro

Solo despues de validar MVP:

- Convertir cotizacion a pedido preliminar.
- Flujo manual a ventas.
- Sin descuento directo de inventario desde Distribucion.

## No-go

- No duplicar catalogo.
- No copiar precios a tablas locales como verdad.
- No crear venta directa.
- No apartar inventario.
- No mezclar sesion ERP interna con clientes externos.

## Decision 2026-10-06 - Home publica de acceso comercial

- La ruta `/` deja de enviar directamente al login cuando no existe sesion y ahora muestra una home publica institucional del portal Distribucion.
- `Inicio::index()` bifurca por sesion: usuarios anonimos ven `home_publica` con layout `publico`; usuarios autenticados conservan el dashboard privado en `inicio`.
- La home publica no consume API, no muestra productos reales, precios ni stock, y presenta el portal como acceso comercial privado sujeto a revision del ERP.









