# Guia de implementacion MVP - Distribucion

## Proposito

Este documento servira como guia cuando comience la implementacion de Distribucion. Por ahora define alcance, limites y dependencias para evitar que la plataforma externa duplique responsabilidades del ERP.

## Principio central

Distribucion es consumidor. El ERP es la fuente de verdad.

Distribucion puede implementar experiencia de usuario, sesion externa, carrito temporal, adaptadores de API y vistas comerciales. No debe implementar como verdad catalogo, precios, inventario, listas, permisos ni aprobacion comercial.

## Alcance MVP

El MVP debe permitir:

1. Solicitud de acceso de cliente.
2. Login de cliente aprobado.
3. Visualizacion de catalogo autorizado.
4. Visualizacion de detalle de producto.
5. Resolucion de precios segun permisos del ERP.
6. Resolucion de disponibilidad comercial.
7. Carrito o lista de cotizacion temporal.
8. Dry-run de cotizacion contra ERP.
9. Registro de cotizacion en ERP con folio.
10. Confirmacion al cliente.

## Fuera del MVP

- Pago en linea.
- Pedido automatico.
- Venta directa.
- Apartado de stock.
- Descuento de inventario.
- Facturacion.
- Promociones complejas.
- Cuenta corriente.
- Administracion comercial completa dentro de Distribucion.
- Catalogo editable fuera del ERP.

## Responsabilidades de Distribucion

### Acceso cliente

Pantallas:

- Login.
- Registro o solicitud de acceso.
- Estado pendiente, rechazado o suspendido.

Tareas:

- Capturar datos de registro.
- Enviar registro al ERP.
- Enviar login al ERP.
- Guardar sesion externa local.
- Mostrar mensajes segun estado devuelto por ERP.

No debe:

- Aprobar clientes.
- Asignar listas de precios.
- Definir permisos comerciales.
- Usar sesion interna del ERP.

### Catalogo visual

Pantallas:

- Listado de productos.
- Detalle de producto.
- Filtros por categoria, marca y atributos.

Tareas:

- Consumir catalogo desde ERP.
- Mostrar productos autorizados.
- Adaptar datos para UI.
- Manejar estados vacios y errores.

No debe:

- Guardar catalogo maestro como verdad.
- Crear SKUs o variantes.
- Cambiar categorias, marcas o imagenes definitivas.

### Precios

Pantallas:

- Precio visible cuando el ERP lo autorice.
- Mensaje alternativo cuando el precio este oculto o pendiente.

Tareas:

- Solicitar resolucion de precios al ERP.
- Mostrar precio devuelto.
- Revalidar precios en cotizacion.

No debe:

- Calcular precio final.
- Aplicar descuentos propios.
- Confiar en precios manipulables desde frontend.

### Disponibilidad

Pantallas:

- Estado comercial de disponibilidad.

Tareas:

- Solicitar disponibilidad al ERP.
- Mostrar estados simples como disponible, limitado, bajo pedido, no disponible o consultar.

No debe:

- Mostrar stock exacto salvo decision explicita.
- Apartar inventario.
- Descontar inventario.

### Cotizaciones

Pantallas:

- Carrito o lista de cotizacion.
- Resumen.
- Confirmacion con folio.

Tareas:

- Mantener seleccion temporal del cliente.
- Enviar dry-run al ERP.
- Mostrar validaciones del dry-run.
- Registrar cotizacion final en ERP.
- Mostrar folio devuelto por ERP.

No debe:

- Crear pedido o venta automatica.
- Confirmar precio sin revalidacion ERP.
- Descontar inventario.

### Cuenta cliente

Pantallas:

- Perfil basico.
- Estado comercial.
- Historial basico de cotizaciones si el ERP lo expone.

Tareas:

- Mostrar informacion autorizada por ERP.

No debe:

- Editar permisos comerciales.
- Editar listas de precios.
- Cambiar aprobacion o suspension.

## Dependencias con ERP

Distribucion puede avanzar con mocks ligeros solo para diseno visual y flujo, pero antes de considerarse listo debe consumir endpoints reales del ERP.

Bloqueos principales:

- Login real depende de `POST /DistribucionApi/auth/login`.
- Registro real depende de `POST /DistribucionApi/auth/registro`.
- Catalogo real depende de `GET /DistribucionApi/catalogo`.
- Detalle real depende de `GET /DistribucionApi/producto/{slug}`.
- Precios reales dependen de `POST /DistribucionApi/precios/resolver`.
- Disponibilidad real depende de `POST /DistribucionApi/disponibilidad/resolver`.
- Cotizacion real depende de `POST /DistribucionApi/cotizacion/dryrun` y `POST /DistribucionApi/cotizacion/registrar`.

## Roadmap paralelo

### Fase A - Base tecnica

ERP:

- Exponer contrato inicial y configuracion.
- Definir formato de error JSON.

Distribucion:

- Confirmar MVC, rutas, layout, ApiClient y sesion externa.
- Preparar vistas base sin logica comercial propia.

Criterio de avance:

- Distribucion puede hacer una llamada simple al ERP.

### Fase B - Acceso externo

ERP:

- Registro, login, estados y aprobacion.

Distribucion:

- Pantallas de registro/login.
- Manejo de sesion externa.
- Mensajes de estado.

Criterio de avance:

- Cliente aprobado puede entrar; cliente pendiente o suspendido queda bloqueado con mensaje claro.

### Fase C - Catalogo

ERP:

- Catalogo, detalle, categorias, marcas y filtros.

Distribucion:

- Listado, filtros, detalle y estados vacios.

Criterio de avance:

- Se puede navegar el catalogo autorizado sin datos locales autoritativos.

### Fase D - Precios y disponibilidad

ERP:

- Resolver precios y disponibilidad.

Distribucion:

- Mostrar precio y estado comercial.
- Manejar productos sin precio visible o no cotizables.

Criterio de avance:

- El cliente ve solo lo que sus permisos permiten.

### Fase E - Cotizaciones

ERP:

- Dry-run, registro, folio y bandeja interna.

Distribucion:

- Carrito temporal, resumen, validacion y confirmacion.

Criterio de avance:

- Cliente registra cotizacion; ventas la recibe en ERP; no hay venta automatica ni descuento de inventario.

## Criterio de MVP listo

El MVP se considera listo cuando un cliente aprobado puede iniciar sesion, navegar catalogo autorizado, ver precio/disponibilidad segun reglas del ERP, armar una cotizacion y registrarla en ERP con folio, sin que Distribucion duplique informacion sensible ni ejecute procesos internos de venta.
