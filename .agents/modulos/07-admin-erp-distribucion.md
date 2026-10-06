# Modulo 07 - Administracion ERP de Distribucion

## Objetivo

Crear herramientas internas en ERP para aprobar clientes, asignar listas, controlar permisos y revisar cotizaciones.

## Proyecto donde vive

Este modulo vive principalmente en:

```text
C:\xampp\htdocs\panel_de_control
```

Antes de editar ERP, leer:

- `C:\xampp\htdocs\panel_de_control\AGENTS.md`.
- `docs/API_DISTRIBUCION_INSTRUCCIONES.md` en este proyecto.

## Controlador sugerido

- `app/controladores/DistribucionAdmin.php`.

## Modelos sugeridos

- `DistribucionClientesApi.php`.
- `DistribucionPermisosApi.php`.
- `DistribucionCotizacionesApi.php`.
- `DistribucionApiEsquema.php`.

## Permisos internos ERP

- `distribucion.ver`.
- `distribucion.editar`.
- `distribucion.aprobar_clientes`.
- `distribucion.asignar_precios`.
- `distribucion.cotizaciones.ver`.
- `distribucion.cotizaciones.gestionar`.

## Funciones internas

- Listar solicitudes.
- Aprobar cliente.
- Rechazar cliente.
- Suspender cliente.
- Asignar tipo de cliente.
- Asignar lista de precios.
- Asignar permisos.
- Ver cotizaciones.
- Ver detalle de cotizacion.
- Planear conversion a pedido/venta.

## Reglas

- Toda accion sensible debe auditarse.
- Cambios de lista de precio requieren permiso especial.
- No convertir cotizacion automaticamente en venta durante MVP.

## Handoff / continuidad

Fecha: 2026-09-09

- Contexto actual: se creo en el ERP `panel_de_control` el controlador interno `DistribucionAdmin`, protegido por sesion ERP desde `Core.php`.
- Cambios recientes: existen endpoints internos para solicitudes, detalle de solicitud, aprobacion/rechazo/suspension, asignacion de tipo/lista/permisos, bandeja y detalle de cotizaciones, acciones de seguimiento, plan de conversion y auditoria/plan de esquema.
- Decision: aprobacion de clientes, estatus, tipo, lista, permisos y seguimiento de cotizaciones ya son escrituras auditadas sobre tablas `erp_distribucion_*`. La conversion a venta/pedido sigue bloqueada en MVP para no duplicar reglas del ERP.
- Decision: se agregaron permisos internos sugeridos al plan de seguridad: `distribucion.ver`, `distribucion.editar`, `distribucion.aprobar_clientes`, `distribucion.asignar_precios`, `distribucion.cotizaciones.ver`, `distribucion.cotizaciones.gestionar`.
- Estado: esquema, permisos internos y escrituras auditadas base ya quedaron aplicados para Distribucion Admin.
- Cambios recientes: se aplico esquema `erp_distribucion_*` y permisos internos `distribucion.*` con respaldo externo `C:\xampp\panel_db_backups\artianicom_sys_panel_de_control_20260909_002739_antes_distribucion_api_schema.sql`.
- Cambios recientes: se agrego UAT operativo `storage/uat/uat_distribucion_api_operaciones.php`; valida solicitud, aprobacion, tipo, lista, permisos, login/token, precio, disponibilidad, dry-run, cotizacion y accion interna con limpieza de datos temporales.
- Cambios recientes: se creo la vista interna `DistribucionAdmin/administracion` y el JS `assets/js/custom/apps/erp/distribucion/administracion.js`; permite operar solicitudes, clientes, listas/permisos y seguimiento de cotizaciones con CSRF.
- Cambios recientes: se agrego entrada de navegacion ERP en sidebar para `DistribucionAdmin/administracion`, visible con `distribucion.ver`.
- Cambios recientes: el frontend externo ya normaliza respuestas ERP y conecta catalogo/producto/cotizacion con endpoints de la API.
- Cambios recientes: se agrego pestaña Productos en Admin Distribucion y endpoints `skus_publicables`, `publicar_sku`, `desactivar_sku`.
- Cambios recientes: se publicaron permanentemente 6 SKUs reales para `canal=distribucion`: `1867`, `1856`, `1849`, `1869`, `1845`, `1855` con vinculos `1748` a `1753`.
- Validacion 2026-09-09: UAT ERP completo paso con SKU permanente, precio, disponibilidad, cotizacion y accion interna; HTTP temporal confirmo catalogo/producto/cotizacion del frontend.
- Pendientes: corregir VirtualHost/DocumentRoot de `distribucion.artiani.com.local` si sigue mostrando ERP y hacer prueba manual visual en navegador.
- Siguiente paso recomendado: abrir el frontend con el vhost correcto y recorrer catalogo, detalle, carrito/dry-run y registro con un cliente aprobado.
