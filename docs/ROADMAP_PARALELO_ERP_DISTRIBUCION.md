# Roadmap paralelo ERP - Distribucion

## Proposito

Este documento organiza el avance del MVP en dos frentes independientes:

- ERP: fuente de verdad, reglas comerciales, API y operacion interna.
- Distribucion: experiencia externa para clientes autorizados.

La idea es que cada proyecto pueda avanzar sin invadir responsabilidades del otro.

## Regla de avance

Distribucion puede avanzar pantallas y flujos con mocks temporales, pero una fase solo se considera lista cuando consume datos reales del ERP o queda claramente documentada como pendiente de integracion.

ERP puede avanzar contratos y endpoints sin esperar la UI final, siempre que respete los contratos documentados para Distribucion.

## Fase 0 - Base y contratos

Objetivo:

- Dejar clara la frontera entre proyectos.
- Confirmar rutas, contratos y estructura minima.

ERP debe avanzar:

- Definir `DistribucionApi`.
- Crear endpoint de contratos.
- Crear endpoint de configuracion inicial.
- Definir formato uniforme de respuesta JSON.
- Definir formato uniforme de errores.
- Confirmar CORS local y produccion.

Distribucion debe avanzar:

- Confirmar estructura MVC.
- Confirmar `ApiClient`.
- Confirmar `DistribucionApiCliente`.
- Confirmar sesion externa separada.
- Preparar layout base y rutas iniciales.
- Mantener documentacion de alcance.

Bloqueos:

- Sin contrato base del ERP, Distribucion solo puede trabajar con mocks.

Criterio de terminado:

- Distribucion puede consultar un endpoint real de contratos/configuracion del ERP.

## Fase 1 - Acceso externo

Objetivo:

- Permitir registro, aprobacion y login de clientes externos.

ERP debe avanzar:

- Recibir solicitudes de registro.
- Guardar estado inicial como pendiente.
- Crear vista/admin interno para aprobar, rechazar o suspender.
- Asignar tipo de cliente.
- Asignar lista de precios.
- Asignar permisos comerciales.
- Validar login externo.
- Emitir token o respuesta de sesion externa.

Distribucion debe avanzar:

- Pantalla de registro.
- Pantalla de login.
- Manejo de estado pendiente, rechazado o suspendido.
- Guardado de sesion externa.
- Proteccion de rutas privadas.

Bloqueos:

- Login real depende de `POST /DistribucionApi/auth/login`.
- Registro real depende de `POST /DistribucionApi/auth/registro`.
- Permisos reales dependen de aprobacion dentro del ERP.

Criterio de terminado:

- Cliente pendiente no entra.
- Cliente suspendido no entra.
- Cliente aprobado puede iniciar sesion en Distribucion sin usar sesion interna del ERP.

## Fase 2 - Catalogo read-only

Objetivo:

- Mostrar catalogo autorizado sin duplicar datos maestros.

ERP debe avanzar:

- Exponer listado de productos activos.
- Exponer detalle por `slug`.
- Exponer categorias.
- Exponer marcas.
- Exponer filtros.
- Incluir imagenes y datos comerciales permitidos.
- Ocultar productos restringidos o no comerciales.

Distribucion debe avanzar:

- Pantalla de catalogo.
- Busqueda visual.
- Filtros UI.
- Tarjetas de producto.
- Detalle de producto.
- Estados vacios y errores de carga.

Bloqueos:

- Catalogo real depende de `GET /DistribucionApi/catalogo`.
- Detalle real depende de `GET /DistribucionApi/producto/{slug}`.
- Filtros reales dependen de categorias, marcas y filtros expuestos por ERP.

Criterio de terminado:

- El cliente navega el catalogo autorizado sin que Distribucion guarde catalogo maestro.

## Fase 3 - Precios y permisos

Objetivo:

- Mostrar precios solo cuando el ERP lo autorice.

ERP debe avanzar:

- Resolver precio por cliente.
- Aplicar lista asignada.
- Determinar permiso para ver precio.
- Determinar permiso para cotizar.
- Devolver razones comerciales cuando no haya precio visible.
- Revalidar cualquier precio al cotizar.

Distribucion debe avanzar:

- Mostrar precio autorizado.
- Mostrar mensaje cuando el precio este oculto.
- Bloquear o adaptar CTA de cotizacion segun permiso.
- Evitar calculos comerciales propios.

Bloqueos:

- Precio real depende de `POST /DistribucionApi/precios/resolver`.
- El boton de cotizar depende del permiso devuelto por ERP.

Criterio de terminado:

- Distribucion no calcula precios y muestra solo lo permitido para el cliente.

## Fase 4 - Disponibilidad comercial

Objetivo:

- Mostrar disponibilidad util sin revelar inventario exacto.

ERP debe avanzar:

- Resolver disponibilidad por producto, variante o presentacion.
- Traducir stock interno a estados comerciales.
- Evitar exponer almacenes internos o cantidades exactas salvo decision futura.
- Definir productos bajo pedido, agotados, restringidos o por consultar.

Distribucion debe avanzar:

- Mostrar estado de disponibilidad.
- Ajustar mensajes y CTA segun disponibilidad.
- Revalidar disponibilidad antes de registrar cotizacion.

Bloqueos:

- Disponibilidad real depende de `POST /DistribucionApi/disponibilidad/resolver`.

Criterio de terminado:

- El cliente entiende si puede cotizar o debe consultar, sin ver stock exacto.

## Fase 5 - Cotizaciones

Objetivo:

- Permitir que el cliente arme y registre una solicitud de cotizacion.

ERP debe avanzar:

- Crear dry-run de cotizacion.
- Revalidar productos, cantidades, precios, permisos y disponibilidad.
- Registrar cotizacion final.
- Generar folio.
- Crear bandeja interna para ventas.
- Guardar trazabilidad basica de origen Distribucion.

Distribucion debe avanzar:

- Carrito o lista de cotizacion temporal.
- Resumen editable.
- Llamada a dry-run.
- Pantalla de validaciones.
- Registro final.
- Confirmacion con folio.

Bloqueos:

- Validacion real depende de `POST /DistribucionApi/cotizacion/dryrun`.
- Registro real depende de `POST /DistribucionApi/cotizacion/registrar`.
- Seguimiento interno depende de la bandeja ERP.

Criterio de terminado:

- Cliente registra cotizacion con folio.
- Ventas la ve en ERP.
- No se crea venta automatica.
- No se descuenta inventario.

## Fase 6 - Cuenta y seguimiento basico

Objetivo:

- Dar al cliente una vista simple de su estado e historial si el ERP lo expone.

ERP debe avanzar:

- Exponer perfil comercial basico.
- Exponer cotizaciones del cliente si aplica.
- Definir estados de seguimiento visibles externamente.

Distribucion debe avanzar:

- Vista de cuenta.
- Estado del cliente.
- Historial basico de cotizaciones si existe endpoint.

Bloqueos:

- Historial depende de endpoint ERP especifico.
- Estados visibles dependen de decision comercial.

Criterio de terminado:

- Cliente puede revisar informacion basica sin acceder a datos internos del ERP.

## Dependencias criticas

- La aprobacion de clientes vive en ERP.
- Las listas de precios viven en ERP.
- Los permisos comerciales viven en ERP.
- El catalogo maestro vive en ERP.
- La disponibilidad real vive en ERP.
- El folio de cotizacion lo genera ERP.

## Orden sugerido de implementacion

1. ERP: contratos y configuracion inicial.
2. Distribucion: conexion API y manejo de errores.
3. ERP: registro/login/aprobacion.
4. Distribucion: pantallas de acceso y sesion.
5. ERP: catalogo y detalle.
6. Distribucion: catalogo visual.
7. ERP: precios y disponibilidad.
8. Distribucion: precios, disponibilidad y CTAs.
9. ERP: dry-run y registro de cotizacion.
10. Distribucion: carrito, resumen y confirmacion.
11. ERP: bandeja interna.
12. Distribucion: cuenta/historial si aplica.

## Primer entregable usable

El primer entregable usable deberia ser:

- Cliente aprobado inicia sesion.
- Ve catalogo autorizado.
- Agrega productos a una cotizacion.
- ERP valida y devuelve folio.
- Ventas atiende manualmente desde ERP.

Todo lo demas puede quedar como mejora posterior.
