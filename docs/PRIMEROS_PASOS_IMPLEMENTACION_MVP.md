# Primeros pasos reales de implementacion MVP

## Proposito

Definir con que chats iniciar la implementacion, que debe entregar cada uno y cuando conviene avanzar al siguiente.

## Regla base

Primero debe avanzar el ERP lo suficiente para que Distribucion no construya logica comercial falsa. Distribucion puede avanzar UI con mocks temporales, pero la integracion real depende de endpoints del ERP.

## Paso 1 - ERP contratos base

Chat recomendado:

- `ERP 01 - Crear contratos base de DistribucionApi`

Objetivo:

- Crear o confirmar `DistribucionApi`.
- Entregar `contratos`.
- Entregar `configuracion_inicial`.
- Definir estructura JSON comun.
- Definir errores seguros.

Resultado esperado:

- URL real consultable desde Distribucion.
- Respuesta JSON estable.
- Version de API visible.
- Lista de endpoints esperados.

Pasar al siguiente cuando:

- `GET /DistribucionApi/contratos` responda.
- `GET /DistribucionApi/configuracion_inicial` responda.
- Distribucion pueda consumir al menos uno de esos endpoints.

## Paso 2 - ERP clientes externos

Chat recomendado:

- `ERP 02 - Clientes externos, aprobacion y permisos`

Objetivo:

- Crear registro/login externo.
- Definir estados de cliente.
- Definir aprobacion interna.
- Definir permisos y lista asignada.

Resultado esperado:

- Registro devuelve folio y estado.
- Login devuelve token externo para clientes aprobados.
- Clientes pendientes/suspendidos quedan bloqueados.
- ERP puede asignar tipo/lista/permisos.

Pasar al siguiente cuando:

- Exista al menos un cliente aprobado de prueba.
- Exista al menos un cliente pendiente/suspendido de prueba.
- Login externo no dependa de la sesion interna ERP.

## Paso 3 - Distribucion autenticacion

Chat recomendado:

- `Distribucion 01 - Autenticacion y sesion externa`

Objetivo:

- Conectar registro/login contra ERP.
- Guardar token externo.
- Proteger rutas privadas.
- Mostrar estados de cliente.

Resultado esperado:

- Cliente puede solicitar acceso desde Distribucion.
- Cliente aprobado puede iniciar sesion.
- Cliente no aprobado ve pantalla de estado.
- Logout destruye sesion externa.

Pasar al siguiente cuando:

- La sesion externa funcione de punta a punta.
- No haya dependencia con sesion interna del ERP.

## Paso 4 - ERP catalogo read-only

Chat recomendado:

- `ERP 03 - Catalogo, precios, disponibilidad y cotizaciones`

Objetivo inicial:

- Exponer catalogo.
- Exponer detalle.
- Exponer categorias, marcas y filtros.

Resultado esperado:

- Catalogo devuelve productos visibles por canal/cliente.
- Detalle por slug devuelve ficha comercial.
- No se exponen datos internos sensibles.

Pasar al siguiente cuando:

- Hay productos reales visibles para Distribucion.
- El ERP filtra productos por canal/permisos.

## Paso 5 - Distribucion catalogo visual

Chat recomendado:

- `Distribucion 02 - Catalogo visual`

Objetivo:

- Construir listado, filtros y detalle consumiendo ERP.

Resultado esperado:

- Cliente navega catalogo real.
- Filtros funcionan con datos del ERP.
- Detalle muestra informacion autorizada.
- Estados de error/sin resultados estan cubiertos.

Pasar al siguiente cuando:

- Catalogo y detalle no dependan de mocks.

## Paso 6 - ERP precios y disponibilidad

Chat recomendado:

- `ERP 03 - Catalogo, precios, disponibilidad y cotizaciones`

Objetivo:

- Resolver precios por cliente/lista/permisos.
- Resolver disponibilidad comercial.

Resultado esperado:

- `precios/resolver` devuelve precio visible u oculto.
- `disponibilidad/resolver` devuelve estado comercial.
- No se expone stock exacto.

Pasar al siguiente cuando:

- Hay al menos casos de prueba con precio visible, precio oculto, disponible y no disponible/consultar.

## Paso 7 - Distribucion precios y disponibilidad

Chat recomendado:

- `Distribucion 03 - Precios, permisos y disponibilidad`

Objetivo:

- Mostrar precios, mensajes y CTAs segun respuesta del ERP.

Resultado esperado:

- Precio visible solo cuando ERP lo permite.
- Precio oculto muestra mensaje comercial.
- Disponibilidad se muestra como estado comercial.
- Botones respetan permisos y disponibilidad.

Pasar al siguiente cuando:

- Distribucion no calcule precios ni disponibilidad local.

## Paso 8 - ERP cotizaciones

Chat recomendado:

- `ERP 03 - Catalogo, precios, disponibilidad y cotizaciones`

Objetivo:

- Crear dry-run.
- Registrar cotizacion.
- Generar folio.
- Crear bandeja interna para ventas.

Resultado esperado:

- Dry-run valida sin guardar.
- Registro guarda cotizacion real.
- ERP genera folio.
- Ventas puede ver la solicitud.
- No se crea venta/pedido ni se descuenta inventario.

Pasar al siguiente cuando:

- Exista una cotizacion registrada visible en ERP.

## Paso 9 - Distribucion cotizaciones

Chat recomendado:

- `Distribucion 04 - Cotizaciones`

Objetivo:

- Armar carrito/lista temporal.
- Validar con dry-run.
- Registrar cotizacion.
- Mostrar folio real.

Resultado esperado:

- Cliente agrega productos.
- Cliente revisa resumen.
- ERP valida.
- ERP registra.
- Distribucion muestra confirmacion.

Pasar al siguiente cuando:

- Flujo completo funciona con endpoints reales.

## Paso 10 - UI/UX y cierre

Chats recomendados:

- `Distribucion 05 - UI/UX y estados visuales`
- `Distribucion 06 - Pruebas, seguridad y cierre MVP`

Objetivo:

- Pulir experiencia visual.
- Revisar responsive.
- Verificar errores y seguridad.
- Confirmar criterios de aceptacion.

Resultado esperado:

- MVP navegable y claro.
- Estados transversales cubiertos.
- Sin datos sensibles expuestos.
- Criterios de aceptacion revisados por fase.

## Ruta corta recomendada

Para avanzar sin dispersarse:

1. ERP contratos base.
2. ERP registro/login/aprobacion.
3. Distribucion autenticacion.
4. ERP catalogo.
5. Distribucion catalogo.
6. ERP precios/disponibilidad.
7. Distribucion precios/disponibilidad.
8. ERP cotizaciones.
9. Distribucion cotizaciones.
10. UI, pruebas y cierre.

## Primer demo interno

El primer demo interno debe probar solo esto:

- Cliente aprobado inicia sesion.
- Abre catalogo.
- Ve un producto.
- Agrega producto a cotizacion.
- Envía cotizacion.
- ERP devuelve folio.
- Ventas puede ver la cotizacion.

No incluir en el primer demo:

- Pago.
- Pedido.
- Venta.
- Factura.
- Apartado de inventario.
- Historial avanzado.
