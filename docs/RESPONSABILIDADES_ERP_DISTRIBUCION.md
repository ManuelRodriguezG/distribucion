# Responsabilidades del ERP para Distribucion

## Proposito

Este documento separa las tareas que pertenecen al ERP para que puedan avanzarse desde `C:\xampp\htdocs\panel_de_control` sin depender de la implementacion visual de Distribucion.

Regla principal: el ERP es la fuente de verdad. Distribucion consume la API y no debe duplicar catalogo, precios, inventario, permisos ni aprobaciones comerciales.

## Alcance ERP del MVP

El ERP debe exponer una API para clientes externos autorizados, resolver reglas comerciales y recibir solicitudes de cotizacion. No debe crear ventas automaticas ni descontar inventario desde Distribucion durante el MVP.

## Bloque 1 - API base

Objetivo:

- Crear una API dedicada para Distribucion.
- Entregar respuestas JSON consistentes.
- Versionar o documentar contratos.
- Validar errores con formato uniforme.
- Permitir consumo desde el dominio local y productivo autorizado.

Endpoints esperados:

- `GET /DistribucionApi/contratos`
- `GET /DistribucionApi/configuracion_inicial`

Criterio de terminado:

- Distribucion puede consultar la API.
- La API responde con estructura estable.
- Existe una forma clara de saber que endpoints estan disponibles y en que version.

## Bloque 2 - Clientes externos

Objetivo:

- Gestionar solicitudes de acceso de clientes, revendedores, mayoristas o distribuidores.
- Mantener aprobacion comercial dentro del ERP.
- Separar sesion externa de la sesion interna del ERP.

Tareas:

- Recibir solicitud de registro.
- Guardar solicitud pendiente.
- Aprobar, rechazar o suspender acceso.
- Asignar tipo de cliente.
- Asignar lista de precios.
- Asignar permisos comerciales.
- Validar login externo.
- Devolver token o sesion externa valida para Distribucion.

Endpoints esperados:

- `POST /DistribucionApi/auth/registro`
- `POST /DistribucionApi/auth/login`
- `GET /DistribucionApi/cliente/perfil` si se incluye cuenta basica en MVP.

Criterio de terminado:

- Un cliente puede registrarse desde Distribucion.
- El ERP puede aprobarlo o suspenderlo.
- Solo clientes aprobados pueden iniciar sesion.
- La respuesta distingue estados como `pendiente`, `aprobado`, `rechazado` y `suspendido`.

## Bloque 3 - Catalogo read-only

Objetivo:

- Exponer catalogo comercial autorizado para Distribucion.
- Mantener productos, SKUs, variantes, imagenes, marcas y categorias como verdad del ERP.

Tareas:

- Exponer productos activos para Distribucion.
- Exponer categorias.
- Exponer marcas.
- Exponer filtros.
- Exponer detalle por `slug`.
- Incluir imagenes, variantes, presentaciones o paquetes cuando existan.
- Ocultar productos no autorizados o no comerciales.

Endpoints esperados:

- `GET /DistribucionApi/catalogo`
- `GET /DistribucionApi/producto/{slug}`
- `GET /DistribucionApi/categorias`
- `GET /DistribucionApi/marcas`
- `GET /DistribucionApi/filtros`

Criterio de terminado:

- Distribucion puede pintar listado y detalle sin guardar catalogo propio.
- El ERP controla que productos aparecen.
- No se exponen costos, proveedores, margenes ni datos internos.

## Bloque 4 - Precios y permisos

Objetivo:

- Resolver precios y permisos comerciales desde el ERP.
- Evitar que Distribucion calcule o confie en precios enviados por frontend.

Tareas:

- Resolver precio por cliente.
- Aplicar lista asignada.
- Determinar si el cliente puede ver precio.
- Determinar si el producto puede cotizarse.
- Devolver mensajes comerciales cuando no aplique precio.
- Rechazar o recalcular cualquier precio enviado desde frontend.

Endpoint esperado:

- `POST /DistribucionApi/precios/resolver`

Criterio de terminado:

- Distribucion envia productos, variantes y cantidades.
- ERP devuelve precio autorizado o razon de ocultamiento.
- Ninguna regla comercial de precios vive en Distribucion.

## Bloque 5 - Disponibilidad comercial

Objetivo:

- Resolver disponibilidad sin exponer stock exacto ni estructura interna de almacenes.

Tareas:

- Determinar disponibilidad por producto, variante o presentacion.
- Devolver estado comercial, no inventario crudo.
- Manejar productos agotados, bajo pedido, proximos, descontinuados o restringidos.
- Evitar exponer almacenes internos salvo decision explicita.

Endpoint esperado:

- `POST /DistribucionApi/disponibilidad/resolver`

Estados sugeridos:

- `disponible`
- `limitado`
- `bajo_pedido`
- `no_disponible`
- `consultar`

Criterio de terminado:

- Distribucion puede mostrar disponibilidad util.
- No se revela stock exacto.
- No se aparta ni descuenta inventario.

## Bloque 6 - Cotizaciones

Objetivo:

- Validar y registrar solicitudes de cotizacion generadas desde Distribucion.
- Crear seguimiento interno para ventas sin convertir automaticamente a venta.

Tareas:

- Recibir carrito o cotizacion.
- Hacer dry-run antes de registrar.
- Revalidar productos, precios, permisos y disponibilidad.
- Generar folio.
- Crear registro interno para seguimiento.
- Mostrar cotizaciones en una bandeja interna del ERP.
- Permitir atencion manual por ventas.

Endpoints esperados:

- `POST /DistribucionApi/cotizacion/dryrun`
- `POST /DistribucionApi/cotizacion/registrar`

Criterio de terminado:

- Distribucion puede enviar una cotizacion.
- ERP genera folio.
- Ventas puede verla internamente.
- No se crea venta automatica.
- No se descuenta inventario.

## Orden recomendado para ERP

1. API base y contrato.
2. Autenticacion externa y aprobacion de clientes.
3. Catalogo read-only.
4. Resolucion de precios.
5. Disponibilidad comercial.
6. Dry-run de cotizacion.
7. Registro de cotizacion con folio.
8. Bandeja interna para ventas.

## No-go ERP para el MVP

- No permitir que Distribucion cree ventas directas.
- No descontar inventario desde Distribucion.
- No exponer costos, margenes ni proveedores.
- No mezclar sesion ERP interna con clientes externos.
- No aceptar precios finales calculados por frontend como verdad.
