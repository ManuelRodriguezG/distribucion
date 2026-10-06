# Modulo 01 - API ERP y contratos

## Objetivo

Crear y mantener los contratos entre `distribucion` y `panel_de_control`.

## Archivos de Distribucion relacionados

- `app/core/ApiClient.php`.
- `app/modelos/DistribucionApiCliente.php`.
- `app/config/configuracion.php`.
- `docs/API_DISTRIBUCION_INSTRUCCIONES.md`.

## Archivos previstos en ERP

- `app/controladores/DistribucionApi.php`.
- `app/modelos/DistribucionCatalogoApi.php`.
- `app/modelos/DistribucionClientesApi.php`.
- `app/modelos/DistribucionCotizacionesApi.php`.
- `app/modelos/DistribucionPermisosApi.php`.
- `app/modelos/DistribucionApiEsquema.php`.

## Endpoints MVP

- `GET contratos`.
- `GET configuracion_inicial`.
- `POST auth/registro`.
- `POST auth/login`.
- `GET catalogo`.
- `GET producto/{slug}`.
- `GET categorias`.
- `GET marcas`.
- `GET filtros`.
- `POST precios/resolver`.
- `POST disponibilidad/resolver`.
- `POST cotizacion/dryrun`.
- `POST cotizacion/registrar`.

## Reglas

- Toda respuesta debe ser JSON.
- Incluir `error`, `mensaje` y datos de negocio.
- CORS solo para dominios autorizados.
- OPTIONS debe responder sin tocar negocio.
- Versionar contrato con `fase1-2026-09-08`.
- No exponer datos sensibles.

## Handoff / continuidad

Fecha: 2026-09-09

- Contexto actual: se creo en el ERP `panel_de_control` la primera capa de contratos `DistribucionApi` para que este frontend consuma JSON sin consultar tablas internas.
- Cambios recientes: el ERP expone `contratos`, `configuracion_inicial`, `catalogo`, `producto/{slug}`, `categorias`, `marcas`, `filtros`, `precios/resolver`, `disponibilidad/resolver`, `cotizacion/dryrun`, `cotizacion/registrar`, `auth/registro` y `auth/login`.
- Cambios recientes: se agrego el servicio ERP interno `CatalogoCanalesErp`; `DistribucionApi/catalogo`, `producto/{slug}`, `categorias`, `marcas` y `filtros` ya consultan `erp_catalogo_canales_vinculos` para `canal=distribucion` cuando existe esquema. `disponibilidad/resolver` calcula estado comercial desde `erp_inventario_existencias` sin devolver cantidades.
- Cambios recientes: se agrego `PreciosCanalesErp`; `precios/resolver` ya valida SKU visible por canal, lista activa y permisos del contexto antes de devolver monto. Sin permiso responde `Solicitar precio`.
- Cambios recientes: `DistribucionClientesApi` ya persiste solicitudes, aprueba clientes externos, emite tokens, cambia estatus, asigna tipo/lista/permisos y audita acciones propias cuando existe esquema.
- Cambios recientes: `DistribucionApiEsquema` ya audita entidades faltantes y genera plan DDL read-only para 8 tablas, sin ejecutar migraciones.
- Cambios recientes: se creo `DistribucionAdmin` en ERP con endpoints internos protegidos para administrar solicitudes, clientes, permisos/listas y cotizaciones; aprobacion, estatus, tipo, lista, permisos y acciones de seguimiento de cotizacion ya ejecutan escritura auditada.
- Decision: los endpoints operativos que dependen de catalogo, listas, tokens, permisos o inventario usan servicios ERP internos. Si falta esquema/configuracion devuelven contrato seguro con `configurado=false`; no muestran precios numericos sin permiso y no exponen stock exacto.
- Decision: catalogo no publica todo el maestro ERP; solo SKUs con vinculo de canal `distribucion`, `sincronizar_catalogo=1` y estatus autorizado (`activo`, `publicado` o `aprobado`).
- Impacta a: `app/core/ApiClient.php`, `app/modelos/DistribucionApiCliente.php` y los futuros controladores/vistas que consuman estos metodos.
- Pendientes: definir conversion futura a pedido/venta reutilizando reglas del ERP y ampliar curacion comercial de SKUs publicados.
- Cambios recientes: se aplico el esquema `erp_distribucion_*` en la base efectiva `artianicom_sys` con respaldo externo `C:\xampp\panel_db_backups\artianicom_sys_panel_de_control_20260909_002739_antes_distribucion_api_schema.sql`. La auditoria posterior quedo `ready=true`.
- Cambios recientes: se agrego vista interna ERP `DistribucionAdmin/administracion` con bandejas de solicitudes, clientes y cotizaciones; consume endpoints protegidos con CSRF.
- Cambios recientes: se agrego entrada de navegacion ERP en sidebar para abrir la administracion interna de Distribucion con permiso `distribucion.ver`.
- Cambios recientes: se agrego UAT operativo `storage/uat/uat_distribucion_api_operaciones.php` en ERP; valida solicitud, aprobacion, tipo, lista, permisos, login, precio, disponibilidad, dry-run, registro de cotizacion y accion interna. Si no hay SKU visible, crea un vinculo temporal de canal y lo limpia al final.
- Cambios recientes: el frontend `distribucion` normaliza respuestas `depurar.*` del ERP, usa `http://panel.com.local/DistribucionApi`, alinea registro con tipos permitidos y conecta catalogo/producto/cotizacion con carrito local y endpoints `/Cotizacion/dryrun` + `/Cotizacion/registrar`.
- Cambios recientes: UAT read-only `storage/uat/uat_api_cliente_readonly.php` en `distribucion` confirma `configuracion_inicial`, `contratos` y `catalogo` contra el ERP.
- Cambios recientes: se agregaron endpoints internos ERP `DistribucionAdmin/skus_publicables`, `publicar_sku` y `desactivar_sku`; la UI Admin ahora incluye pestaña Productos para publicar/desactivar SKUs del canal.
- Cambios recientes: se publicaron permanentemente 6 SKUs reales para `canal=distribucion`: `1867`, `1856`, `1849`, `1869`, `1845`, `1855` con vinculos `1748` a `1753`.
- Cambios recientes: se normalizo `imagen_principal` como URL absoluta del ERP y se corrigio que el frontend no reenvie el parametro interno `url` al API ERP.
- Validacion 2026-09-09: HTTP temporal confirmo `Catalogo` con productos/boton cotizar/imagenes absolutas, `Cotizacion` con carrito/dry-run, y `/Cotizacion/dryrun` devolviendo JSON limpio.
- Siguiente paso recomendado: probar manualmente en navegador con el vhost correcto de Distribucion; si el host `distribucion.artiani.com.local` muestra ERP, corregir VirtualHost/DocumentRoot a `C:\xampp\htdocs\distribucion`.

## Lectura obligatoria

Antes de trabajar este modulo:

- Leer `docs/API_DISTRIBUCION_INSTRUCCIONES.md`.
- Si se edita ERP, leer `C:\xampp\htdocs\panel_de_control\AGENTS.md`.
- Revisar `C:\xampp\htdocs\panel_de_control\app\controladores\EcommercePublico.php` solo como referencia de patron, no para mezclar contratos.
