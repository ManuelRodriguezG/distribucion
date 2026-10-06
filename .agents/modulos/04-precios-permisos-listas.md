# Modulo 04 - Precios, permisos y listas

## Objetivo

Implementar visibilidad de precios segun cliente, permisos y lista asignada.

## Fuente de verdad

ERP:

- Listas de precios.
- Clientes/grupos/canales.
- Permisos comerciales.
- Reglas de visibilidad.

## Permisos relevantes

- `distribucion.precio.ver_publico`.
- `distribucion.precio.ver_mayoreo`.
- `distribucion.precio.ver_lista_asignada`.
- ``distribucion.catalogo.ver``.
- ``distribucion.catalogo.ver`_detalle`.

## Regla de resolucion

1. Si tiene lista asignada y permiso, mostrar precio de lista.
2. Si no tiene lista pero puede ver publico, mostrar precio publico.
3. Si no puede ver precio, mostrar `Solicitar precio`.
4. Si SKU no tiene precio en lista, aplicar politica ERP.

## No exponer

- Costo.
- Margen.
- Utilidad.
- Proveedor.
- Costo promedio.
- Reglas internas completas.

## Pendientes

- UI para precio oculto.
- UI para precio visible.
- Mensajes por permiso.
- Integracion con `precios/resolver`.
- Pruebas con perfiles distintos.


## Decisiones 2026-09-09

- Distribucion queda como portal con login inicial para pantallas comerciales; `Auth/login` y `Auth/registro` siguen publicos para permitir acceso y solicitudes.
- La habilitacion de modulos se basa en permisos devueltos por el ERP: catalogo, detalle y cotizacion se bloquean localmente si falta el permiso, pero el ERP sigue revalidando cada endpoint.
- `Cuenta` queda disponible para cualquier usuario autenticado para mostrar estatus, permisos, lista asignada y campos pendientes si el ERP los devuelve.
- El formulario de registro captura datos comerciales base, intereses y ubicacion para alimentar la revision interna; no asigna lista ni permisos desde Distribucion.
- La UI de precios solo presenta `precio.visible`, `precio.monto`, `precio.moneda`, `precio.tipo` y `precio.mensaje` cuando vienen del ERP; si no hay monto visible, mantiene `Solicitar precio` o el mensaje autorizado.
- Decision posterior: login, registro y usuarios autenticados sin permisos comerciales usan layout de autenticacion sin sidebar; el shell interno solo aparece cuando el ERP ya devolvio permisos de modulo.

## Decisiones 2026-09-10

- Regla de rutas: las URLs publicas de Distribucion deben escribirse en minusculas (`/auth/login`, `/catalogo`, `/cotizacion`, `/cuenta`). El router redirige controlador/metodo a minusculas como canonico.
- El registro externo se orienta a solicitud de acceso de mayoreo, no a distribuidor autorizado. El objetivo comercial inmediato es identificar negocio, ramo, zona, datos de contacto e interes para habilitar precios despues.
- Clasificacion inicial de negocio: venta por internet, veterinaria, petshop/tienda de mascotas, acuario, acuario y tienda de mascotas, estetica canina, criador, vendedor en mercado o tianguis, vendedor ambulante u otro.
- Estado API: Distribucion envia la solicitud a `POST /DistribucionApi/auth/registro` del ERP, no a ecommerce. El ERP ya recibe registro comercial estructurado de mayoreo; Distribucion envia JSON con todos los campos definidos y solo completa `empresa` desde `nombre_negocio`.
- Pendiente futuro: definir si el login externo seguira por correo o si tambien podra iniciar con telefono/WhatsApp.

## Decisiones 2026-09-11

- Distribucion no calcula permisos comerciales finales; consume `perfil.permisos` y `perfil.acciones` del ERP.
- Las guardas de modulo del frontend deben preferir acciones ERP: `ver_catalogo`, `ver_detalle`, `ver_precio`, `ver_disponibilidad`, `agregar_cotizacion`, pedido_preliminar, descargar_catalogo y editar_cuenta.
- Los permisos tipo `distribucion.catalogo.ver` se conservan como trazabilidad o detalle, pero no deben ser la unica fuente para mostrar navegacion o bloquear modulos si el ERP ya mando acciones resueltas.
- Catalogo, detalle, cotizacion, menu lateral y boton superior de cotizacion usan `SesionCliente::tieneAccion(...)`; el ERP sigue revalidando cada endpoint privado.

## Ajuste 2026-09-12

- Para accesos de UI, si el ERP envia una accion explicita se respeta esa accion. Si la accion no viene, Distribucion puede usar el permiso equivalente como respaldo, por ejemplo distribucion.catalogo.ver para abrir catalogo.
- Si una accion viene explicitamente en false, no se sobreescribe con el permiso de respaldo.
- La UI de cliente no debe mostrar claves internas de permisos ni textos tecnicos como guardrails, sin_permiso o nombres de API.
- La ruta raiz redirige al catalogo cuando el cliente tiene acceso a catalogo; si no, redirige a cuenta/estado.
