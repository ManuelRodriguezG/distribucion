# Modulo 06 - Cotizaciones y prepedidos

## Objetivo

Permitir que el cliente arme una solicitud comercial sin crear venta ni afectar inventario.

## Archivos actuales

- `app/controladores/Cotizacion.php`.
- `app/vistas/paginas/cotizacion.php`.
- `app/modelos/DistribucionApiCliente.php`.

## Endpoints

- `POST /DistribucionApi/cotizacion/dryrun`.
- `POST /DistribucionApi/cotizacion/registrar`.

## Reglas MVP

- Dry-run recalcula precios en ERP.
- Registro requiere cliente autenticado.
- Registro requiere permiso `distribucion.cotizacion.solicitar`.
- Genera folio.
- Guarda snapshot comercial.
- No aparta inventario.
- No crea venta.
- No crea pedido automatico.

## Flujo

1. Cliente agrega SKUs.
2. Frontend ejecuta dry-run.
3. ERP valida visibilidad, precio y disponibilidad.
4. Cliente confirma solicitud.
5. ERP registra folio.
6. Admin interno revisa.

## Pendientes

- Carrito local.
- Cantidades.
- Comentarios.
- Confirmacion.
- Historial de cotizaciones del cliente.
- Bandeja interna ERP.


## Decisiones 2026-10-01 - Pedido preliminar con facturacion

- La pantalla de cotizacion registra pedido preliminar contra `POST /DistribucionApi/pedido/registrar`, conservando la ruta local `/cotizacion/registrar` como adaptador de frontend.
- El pedido envia `requiere_factura` y `facturacion`; esto solo guarda intencion/datos para revision del ERP, no genera factura automaticamente.
- La preferencia fiscal nace en el registro del cliente, pero cada pedido conserva la opcion operativa de facturar o no facturar antes de enviarse.

## Avance 2026-10-01 - Pedidos cliente y borradores

- Se agrega pantalla `/pedidos` para listar pedidos reales del cliente usando `GET /DistribucionApi/pedido/listar`.
- `Ver pedido` consulta `GET /DistribucionApi/pedido/detalle` en modal, muestra partidas y permite aceptar, rechazar o pedir ajuste con `POST /DistribucionApi/pedido/responder`.
- Desde un pedido se puede usar `Volver a cotizar`, que copia sus partidas al carrito/cotizacion local para crear una nueva solicitud sin modificar el pedido anterior.
- La pantalla `/cotizacion` agrega borradores locales de cotizacion en `localStorage`, con guardar/cargar/eliminar. Estos borradores ayudan al cliente a organizar compras por linea, frecuencia o tipo de producto antes de generar un pedido real.
- Pendiente futuro: persistir cotizaciones/borradores en ERP para analitica comercial multi-dispositivo y para que administracion vea como se organizan los clientes antes de pedir.

## Decision 2026-10-02 - Historial separado de cotizaciones y pedidos

- La API ya separa cotizaciones y pedidos; el frontend consume `cotizacion/listar`, `cotizacion/detalle`, `cotizacion/guardar_borrador`, `cotizacion/cancelar`, `cotizacion/duplicar` y `cotizacion/enviar_pedido`.
- `/cotizaciones` es el historial real del cliente con acciones por estatus; `/cotizacion` queda como editor/nueva cotizacion.
- Los borradores dejan de ser locales como fuente principal y se guardan en ERP; `localStorage` solo conserva el carrito temporal y el borrador que el cliente esta editando.
- Enviar una cotizacion crea un pedido separado; si el cliente olvida productos debe crear otra cotizacion/pedido, no modificar el pedido anterior.
- `/pedidos` sigue mostrando solo pedidos formales y no mezcla borradores.
