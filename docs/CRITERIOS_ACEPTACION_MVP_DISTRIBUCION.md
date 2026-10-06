# Criterios de aceptacion MVP - Distribucion

## Proposito

Definir cuando cada fase del MVP puede considerarse terminada. Estos criterios aplican tanto al proyecto Distribucion como a su integracion con el ERP.

## Regla general

Una fase no se considera lista si depende de mocks sin estar marcada como pendiente de integracion. Los mocks ayudan a disenar y probar flujo, pero el MVP listo requiere contratos reales del ERP para datos comerciales.

## Fase 0 - Base tecnica y arquitectura

Aceptacion:

- Existe estructura MVC funcional.
- Existen rutas iniciales definidas.
- Existe cliente API centralizado.
- Existe sesion externa separada.
- Existen documentos de arquitectura y limites con ERP.
- El ERP esta documentado como fuente de verdad.
- No hay consultas directas desde Distribucion a tablas internas del ERP.

No aceptado si:

- Distribucion empieza a duplicar catalogo, precios, inventario o permisos.
- La sesion externa se mezcla con sesion interna del ERP.
- Cada controlador llama al ERP de forma improvisada sin adaptador central.

## Fase 1 - Contratos API ERP

Aceptacion:

- ERP expone `GET /DistribucionApi/contratos`.
- ERP expone `GET /DistribucionApi/configuracion_inicial`.
- Las respuestas JSON tienen estructura consistente.
- Los errores no exponen SQL ni trazas internas.
- CORS queda limitado a dominios autorizados.
- Distribucion puede consumir al menos un endpoint real.

No aceptado si:

- La API devuelve estructuras distintas por endpoint sin criterio comun.
- Distribucion depende de archivos locales para simular configuracion final.
- La API expone campos internos sensibles.

## Fase 2 - Registro, aprobacion y login

Aceptacion:

- Cliente puede enviar solicitud de acceso.
- ERP genera folio o identificador de solicitud.
- ERP mantiene estado del cliente o solicitud.
- ERP permite aprobar, rechazar o suspender.
- Cliente aprobado puede iniciar sesion.
- Cliente pendiente, rechazado o suspendido no accede a funciones privadas completas.
- Distribucion guarda solo sesion/token externo.

No aceptado si:

- Registro aprueba clientes automaticamente sin decision documentada.
- Distribucion asigna lista de precios o permisos.
- El login externo abre sesion interna del ERP.

## Fase 3 - Catalogo visual

Aceptacion:

- Catalogo se consume desde ERP.
- Productos visibles respetan canal y permisos.
- Existen listado y detalle de producto.
- Existen categorias, marcas o filtros suficientes para navegar.
- Las tarjetas muestran solo datos comerciales autorizados.
- Los estados vacios y errores estan contemplados.

No aceptado si:

- Distribucion guarda catalogo maestro como verdad.
- Se muestran productos restringidos.
- Se exponen costos, proveedores, margenes o datos internos.

## Fase 4 - Precios y permisos

Aceptacion:

- Precios se resuelven en ERP.
- Distribucion no calcula precio final.
- Cliente ve precio solo si tiene permiso.
- Si no hay precio visible, aparece mensaje comercial claro.
- El ERP devuelve acciones permitidas por item.
- Cotizacion revalida precios antes de guardar.

No aceptado si:

- Frontend manda precios y ERP los acepta como verdad.
- Distribucion aplica descuentos propios.
- El tipo de cliente se usa como unico permiso sin validacion granular.

## Fase 5 - Disponibilidad comercial

Aceptacion:

- Disponibilidad se resuelve en ERP.
- Se muestran estados comerciales, no stock exacto.
- Estados permitidos estan definidos.
- Productos no disponibles o restringidos ajustan acciones de cotizacion.
- Cotizacion revalida disponibilidad antes de guardar.

No aceptado si:

- Se expone inventario exacto sin decision futura documentada.
- Distribucion calcula disponibilidad local.
- Se aparta o descuenta inventario desde Distribucion.

## Fase 6 - Cotizaciones

Aceptacion:

- Cliente puede armar cotizacion temporal.
- ERP valida cotizacion con dry-run.
- ERP recalcula precios y disponibilidad.
- ERP rechaza items no autorizados.
- ERP registra cotizacion final.
- ERP genera folio.
- Ventas puede ver la cotizacion dentro del ERP.
- Distribucion muestra confirmacion con folio real.
- No se crea venta automatica.
- No se crea pedido automatico.
- No se descuenta inventario.

No aceptado si:

- Distribucion registra cotizacion sin validar contra ERP.
- Distribucion inventa folio.
- La cotizacion se convierte automaticamente en venta.
- La cotizacion aparta inventario.

## Fase 7 - Cuenta cliente

Aceptacion:

- Cliente puede ver perfil basico autorizado.
- Cliente puede ver estado comercial.
- Cliente puede cerrar sesion.
- Historial de cotizaciones se muestra solo si el ERP lo expone.

No aceptado si:

- Distribucion edita permisos, listas o aprobacion.
- Se muestran datos internos del ERP.
- Se promete seguimiento que ERP no puede sostener.

## Criterios transversales de seguridad

Aceptacion:

- HTTPS obligatorio en produccion.
- CORS limitado.
- Login y registro con proteccion contra abuso.
- Tokens con expiracion.
- Logout destruye sesion externa.
- Errores son seguros para cliente.
- Logs tecnicos no exponen informacion sensible en UI.

No aceptado si:

- Se exponen errores SQL.
- Se exponen costos, margenes, proveedores o stock exacto.
- Se confia en datos comerciales enviados desde frontend.

## Criterio final de MVP listo

El MVP esta listo cuando:

1. Cliente solicita acceso.
2. ERP aprueba y configura permisos/lista.
3. Cliente aprobado inicia sesion externa.
4. Cliente navega catalogo autorizado.
5. Cliente ve precios y disponibilidad segun ERP.
6. Cliente arma cotizacion.
7. ERP valida con dry-run.
8. ERP registra cotizacion y genera folio.
9. Ventas recibe la cotizacion en ERP.
10. No hay venta, pedido ni descuento de inventario automatico.

Si alguno de esos pasos depende de mocks, el MVP todavia no esta listo; solo esta en modo prototipo o pendiente de integracion.
