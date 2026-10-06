# Alcance funcional MVP por pantalla - Distribucion

## Proposito

Definir que debe mostrar y permitir cada pantalla del MVP de Distribucion, manteniendo al ERP como fuente de verdad.

Este documento describe comportamiento funcional, no implementacion tecnica.

## Pantalla Inicio

Objetivo:

- Presentar la entrada comercial a Distribucion.
- Guiar al cliente hacia login, registro o catalogo segun permisos.

Debe mostrar:

- Identidad Artiani/Distribucion.
- Acceso a login.
- Acceso a registro o solicitud de acceso.
- Acceso a catalogo si se permite vista publica o semi-publica.
- Mensaje breve para clientes pendientes o no autenticados.

Depende del ERP:

- Configuracion inicial.
- Estado de sesion.
- Permisos basicos.

No debe:

- Mostrar precios si el ERP no autoriza.
- Prometer disponibilidad o condiciones comerciales no validadas.

## Pantalla Login

Objetivo:

- Permitir entrada de clientes externos autorizados.

Debe mostrar:

- Formulario de correo y contrasenia.
- Mensajes de error claros.
- Link a registro.
- Mensaje especial para estados `pendiente`, `en_revision`, `rechazado` o `suspendido`.

Depende del ERP:

- `POST /DistribucionApi/auth/login`.
- Perfil del cliente.
- Estado comercial.
- Permisos.
- Token externo.

No debe:

- Usar sesion interna del ERP.
- Aprobar clientes.
- Asignar permisos.

## Pantalla Registro

Objetivo:

- Capturar solicitudes de acceso comercial.

Debe mostrar:

- Nombre de contacto.
- Empresa.
- Correo.
- Telefono.
- Tipo de interes.
- Mensaje opcional.
- Confirmacion con folio si el ERP lo devuelve.

Depende del ERP:

- `POST /DistribucionApi/auth/registro`.
- Folio de solicitud.
- Estado inicial de solicitud.

No debe:

- Aprobar automaticamente.
- Mostrar lista de precios asignada.
- Crear cliente comercial definitivo sin revision ERP.

## Pantalla Estado De Solicitud

Objetivo:

- Informar al cliente que su acceso esta pendiente, en revision, rechazado o suspendido.

Debe mostrar:

- Estado actual devuelto por ERP.
- Mensaje comercial simple.
- Acciones permitidas, por ejemplo salir o contactar ventas.

Depende del ERP:

- Estado del cliente.
- Mensaje o razon comercial si se decide exponerla.

No debe:

- Dar acceso a funciones privadas si el estado no es `aprobado`.

## Pantalla Catalogo

Objetivo:

- Permitir explorar productos visibles para el cliente/canal.

Debe mostrar:

- Busqueda.
- Filtros.
- Categorias.
- Marcas.
- Tarjetas de producto.
- Imagen principal.
- Nombre.
- SKU o presentacion, si aplica.
- Precio solo si viene visible.
- Disponibilidad comercial.
- Acciones permitidas.

Depende del ERP:

- `GET /DistribucionApi/catalogo`.
- `GET /DistribucionApi/categorias`.
- `GET /DistribucionApi/marcas`.
- `GET /DistribucionApi/filtros`.
- Permisos de catalogo, precio y cotizacion.

No debe:

- Mostrar productos no autorizados.
- Calcular precios.
- Mostrar stock exacto.
- Guardar catalogo maestro local.

## Pantalla Detalle De Producto

Objetivo:

- Mostrar ficha comercial autorizada del producto.

Debe mostrar:

- Galeria de imagenes.
- Nombre.
- Marca.
- Categoria.
- Descripcion comercial.
- SKUs, variantes o presentaciones disponibles.
- Atributos visibles.
- Precio resuelto por SKU si aplica.
- Disponibilidad comercial.
- Boton para agregar a cotizacion si el ERP lo permite.
- Mensaje de solicitar precio o consultar disponibilidad si aplica.

Depende del ERP:

- `GET /DistribucionApi/producto/{slug}`.
- Precio resuelto.
- Disponibilidad comercial.
- Acciones permitidas.

No debe:

- Exponer costos.
- Exponer proveedores.
- Exponer margenes.
- Exponer stock exacto.
- Permitir cotizar items no autorizados.

## Pantalla Cotizacion

Objetivo:

- Permitir construir, validar y registrar una solicitud de cotizacion.

Debe mostrar:

- Lista de productos agregados.
- Cantidades editables.
- Comentarios del cliente.
- Validacion de dry-run.
- Totales estimados si el ERP devuelve precios visibles.
- Mensajes por item cuando haya restricciones.
- Boton para registrar cotizacion.
- Confirmacion con folio.

Depende del ERP:

- `POST /DistribucionApi/cotizacion/dryrun`.
- `POST /DistribucionApi/cotizacion/registrar`.
- Revalidacion de precios.
- Revalidacion de disponibilidad.
- Permiso `distribucion.cotizacion.solicitar`.
- Folio generado por ERP.

No debe:

- Registrar cotizacion sin dry-run o validacion ERP.
- Inventar folio.
- Crear pedido.
- Crear venta.
- Apartar inventario.
- Descontar inventario.

## Pantalla Cuenta

Objetivo:

- Mostrar informacion basica del cliente externo.

Debe mostrar:

- Nombre o empresa.
- Tipo de cliente.
- Estado comercial.
- Permisos o acciones disponibles de forma amigable.
- Historial basico de cotizaciones si el ERP lo expone.
- Opcion de cerrar sesion.

Depende del ERP:

- Perfil del cliente.
- Estado comercial.
- Permisos.
- Historial de cotizaciones si se implementa.

No debe:

- Editar lista de precios.
- Editar permisos.
- Aprobar o suspender cliente.
- Mostrar datos internos del ERP.

## Estados transversales obligatorios

Cada pantalla que consuma ERP debe contemplar:

- Cargando.
- Error recuperable.
- Servicio no disponible.
- Sin resultados.
- Sin permiso.
- Sesion expirada.

## Criterio de MVP funcional

El MVP funcional queda listo cuando:

- Cliente puede solicitar acceso.
- Cliente aprobado puede iniciar sesion.
- Cliente no aprobado ve estado claro.
- Cliente aprobado navega catalogo autorizado.
- Cliente ve precio y disponibilidad solo si ERP lo permite.
- Cliente arma cotizacion temporal.
- ERP valida cotizacion.
- ERP registra cotizacion y devuelve folio.

## Mejoras posteriores

Quedan fuera del primer MVP:

- Comparador de productos.
- Descarga de catalogo.
- Historial avanzado.
- Reordenar cotizaciones anteriores.
- Direcciones multiples.
- Condiciones comerciales avanzadas.
- Pedido preliminar.
- Pago en linea.
