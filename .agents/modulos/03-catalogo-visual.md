# Modulo 03 - Catalogo visual

## Objetivo

Construir la experiencia externa de catalogo para clientes y socios comerciales.

## Archivos actuales

- `app/controladores/Catalogo.php`.
- `app/vistas/paginas/catalogo.php`.
- `app/vistas/paginas/producto.php`.
- `public/assets/css/app.css`.
- `public/assets/js/app.js`.

## Fuente

Los datos vienen del ERP mediante:

- `GET /DistribucionApi/catalogo`.
- `GET /DistribucionApi/producto/{slug}`.
- `GET /DistribucionApi/categorias`.
- `GET /DistribucionApi/marcas`.
- `GET /DistribucionApi/filtros`.

## Debe mostrar

- Categorias.
- Marcas.
- Buscador.
- Filtros.
- Imagenes.
- Variantes.
- Presentaciones.
- Paquetes.
- Atributos tecnicos visibles.
- Estado comercial.
- Acciones disponibles.

## Reglas

- No mostrar campos internos.
- No asumir que todos los productos tienen precio.
- No asumir que todos los usuarios pueden cotizar.
- No romper si la API todavia no existe.
- Mantener mensajes claros: `Solicitar precio`, `Consultar disponibilidad`, `Acceso en revision`.

## Pendientes

- Tarjeta final de producto.
- Filtros dinamicos.
- Paginacion.
- Ordenamiento.
- Detalle con variantes/presentaciones.
- Galeria de imagenes.


## Decision 2026-09-09

- Se descarta la vista inicial tipo landing/demo y se rehace el frontend como portal B2B operativo inspirado en el ERP/Metronic: sidebar, toolbar superior, cards sobrias, tablas densas, badges de estado, filtros compactos y acciones con iconos.
- Catalogo usa tabla como vista principal y cards como alternativa visual; no calcula precios, disponibilidad ni permisos localmente, solo presenta lo que devuelve la API ERP.
- Cotizacion conserva carrito local temporal y usa dry-run/registro del ERP; la interfaz muestra explicitamente que no aparta inventario ni crea pedido automatico.

## Avance 2026-09-09 - Catalogo visual contra API real

- Catalogo consume `catalogo`, `categorias`, `marcas` y `filtros`; se confirmo que API publica 6 SKUs reales y que para sesion publica devuelve precios ocultos y `agregar_cotizacion=false`.
- Buscador `q` y ordenamiento usan parametros de API. Se corrigio ordenamiento a valores reales devueltos por ERP: `relevancia`, `nombre`, `marca`.
- Como `categorias` y `marcas` aun devuelven `items=[]`, la UI muestra facetas informativas derivadas del resultado actual, sin enviarlas como filtro autoritativo.
- Detalle de producto consume ficha real y muestra precio, disponibilidad, presentacion, ID SKU, accion permitida, tabs para atributos/variantes/paquetes y estados vacios cuando la API todavia no entrega esos arreglos.

## Avance 2026-09-28 - Tabla completa y filtro de categoria

- Catalogo visual cambia a tabla completa como vista principal, ocupando todo el ancho disponible; cards queda como vista secundaria.
- Los filtros pasan a una barra superior destacada con busqueda, categoria y ordenamiento.
- Categoria se implementa como `select` con buscador local de opciones; el formulario envia `categoria={id}` al API ERP.
- Se corrigio en ERP la faceta de categorias/marcas para aceptar estatus `activa` ademas de `activo`; `CatalogoCanalesErp::categoriasCanal()` ya devuelve categorias reales del canal Distribucion.
- La pantalla de catalogo actualmente requiere sesion/permisos segun contrato vigente del ERP; la prueba publica redirige a login.

## Ajuste 2026-09-28 - Refinamiento de catalogo operativo

- `Cerrar sesion` se movio del topbar al sidebar para quedar como accion persistente de cuenta.
- Catalogo mantiene tabla como vista principal; acciones usan botones con texto (`Ver detalle`, `Agregar a cotizacion`, `Solicitar precio`) en lugar de iconos aislados.
- `Ver detalle` abre modal con datos del item ya recibido desde API; la ficha completa queda como accion secundaria dentro del modal.
- Categoria se cambio a selector tipo combobox con buscador propio y valor oculto `categoria={id}` para filtrar contra API.
- Se agregaron filtros `precio_min` y `precio_max`; el ERP solo los aplica cuando el contexto tiene permiso de precio, para no filtrar sobre datos no autorizados.

## Ajuste 2026-09-29 - Buscador local de tabla y categorias visibles

- Se agrego un buscador local dentro de la tarjeta de tabla: filtra las filas/cards ya cargadas sin cambiar URL ni consultar API.
- Se conserva el buscador superior como busqueda formal contra API que genera parametros en URL.
- Se subio `z-index` y se libero `overflow` del bloque de filtros para que el combobox de categorias se vea completo encima de la tabla.
- El contador del filtro local muestra cuantas filas visibles quedan despues del filtro instantaneo.

## Ajuste 2026-09-30 - Mi catalogo cliente

- Se implementa la seccion visible `Mi catalogo` como subcatalogo personal del cliente, separada de carrito, cotizacion o pedido.
- La UI usa endpoints nuevos `GET /DistribucionApi/mi_catalogo/listar` y `POST /DistribucionApi/mi_catalogo/guardar`; no usa textos ni rutas visibles de `Surtido`.
- Catalogo general y detalle muestran `Agregar a Mi catalogo` solo con accion `gestionar_mi_catalogo`; al guardar cambia a `En Mi catalogo` sin redirigir.
- Quitar un producto marca `estatus=inactivo`; no genera pedido, no aparta inventario y no modifica existencias ERP.
## Ajuste 2026-09-30 - Paginacion operativa

- Catalogo agrega controles reales de paginacion con primera/anterior/paginas cercanas/siguiente/ultima.
- Los enlaces conservan filtros vigentes (`q`, `categoria`, `precio_min`, `precio_max`, `orden`, `limite`) y solo cambian `pagina`.
- El formulario de filtros reinicia `pagina=1` y permite elegir `limite` de 24, 40 o 60 productos por pagina, respetando el maximo del API ERP.
## Ajuste 2026-09-30 - Busqueda global desde tabla

- El buscador dentro de la tabla deja de filtrar solo las filas cargadas y ahora envia `q` al API para buscar en todo el catalogo paginado.
- La busqueda de tabla conserva categoria, rango de precio, orden y limite, reiniciando `pagina=1` para evitar paginas vacias.
## Ajuste 2026-09-30 - Mi catalogo con detalle modal

- `GET /DistribucionApi/mi_catalogo/listar` ahora debe entregar imagen principal, marca, categoria y presentacion para que la seccion use la misma informacion visual del catalogo ERP.
- La pantalla `Mi catalogo` conserva busqueda propia contra el subcatalogo guardado del cliente, filtro por categoria, filtro por marca y selector de limite; no consulta tablas locales ni calcula datos comerciales.
- `Ver detalle` abre un modal informativo dentro de la misma pantalla con SKU, imagen, categoria, marca, presentacion, alias, ubicacion, prioridad y notas, evitando sacar al usuario de su lista.
## Ajuste 2026-09-30 - Filtro instantaneo de tabla

- La busqueda superior del catalogo sigue siendo la busqueda autoritativa contra API y puede cambiar URL, pagina y resultados totales.
- El buscador dentro de la tarjeta de tabla vuelve a ser filtro local instantaneo sobre los productos ya cargados en la pagina actual; no hace peticiones al ERP.
- El contador local muestra productos visibles y se mantiene separado del total del catalogo paginado.
## Ajuste 2026-09-30 - Busqueda de tabla en todo el catalogo

- El filtro dentro de la tabla deja de limitarse a las filas de la pagina actual cuando hay texto: consulta `Catalogo::buscar_tabla()` por AJAX, conserva los filtros vigentes de URL y actualiza filas/cards sin recargar la pantalla.
- Si el campo queda vacio, se restaura la pagina original con su paginacion normal.
- Para evitar consultas excesivas, la busqueda usa espera corta mientras se escribe y agrupa resultados paginados del API ERP hasta un limite operativo.
## Ajuste 2026-09-30 - Mi catalogo simplificado

- `Mi catalogo` queda como apartado simple de productos conocidos/frecuentes para tenerlos a la mano.
- Se retiran de la UI campos de organizacion futura como alias, ubicacion, prioridad y notas; esas capacidades se reservan para mejoras posteriores de gestion del negocio del cliente.
- El filtro dentro de la tabla del catalogo general vuelve a ser local e instantaneo sobre la pagina actual; la busqueda global autoritativa queda en los filtros superiores para evitar peticiones pesadas mientras se escribe.
## Ajuste 2026-09-30 - Mi catalogo hacia cotizacion

- `Mi catalogo` permite capturar cantidad y comentario por producto antes de enviarlo a cotizacion.
- El carrito local de cotizacion conserva cantidad y comentario por partida; en la pantalla de cotizacion ambos campos pueden editarse antes de revisar o enviar.
- Al registrar, los comentarios por partida viajan en `items[].comentario` y tambien se consolidan dentro de `comentarios` para que el ERP los conserve aunque la tabla de partidas no tenga columna dedicada de comentario cliente.
## Ajuste 2026-09-30 - Inicio ligero y resumen

- El login y la ruta raiz ya no redirigen automaticamente a catalogo para evitar cargar el endpoint mas pesado al iniciar sesion.
- `Inicio` muestra un resumen ligero con accesos a Mi catalogo, Cotizaciones, Catalogo completo, Cuenta y un bloque preparado para alertas futuras.
- Las alertas de validacion de pedidos/cotizaciones quedan como espacio reservado para conectarse cuando el ERP exponga esos estados al cliente externo.
## Ajuste 2026-10-01 - Pedido con datos fiscales

- La pantalla de cotizacion funciona como armado de pedido comercial y registra contra `POST /DistribucionApi/pedido/registrar` mediante el controlador local `Cotizacion::registrar()`.
- El payload conserva `items`, `entrega` y `comentarios`, agrega `requiere_factura` + `facturacion` segun el contrato `payloads.pedido_facturacion`, y duplica `tipo_entrega`/`direccion_envio` en raiz para que el ERP persista columnas operativas actuales.
- El bloque fiscal solo se muestra cuando el cliente marca `Quiero factura`; se piden RFC, razon social, regimen fiscal, uso CFDI, codigo postal fiscal y correo de facturacion.
- Frontend solo normaliza RFC en mayusculas y correo fiscal en minusculas; no valida SAT ni genera factura. El ERP conserva la informacion para revision interna antes de venta/facturacion.


## Ajuste 2026-10-01 - Busqueda de tabla sobre indice local

- El catalogo precarga un indice local de productos publicados usando paginas del API ERP al cargar la pantalla, respetando que Distribucion solo consume API y no consulta tablas directas.
- El buscador dentro de la tabla ya no dispara consultas mientras se escribe ni se limita a la pagina visible; filtra en navegador sobre el indice completo cargado.
- La paginacion sigue mostrando la vista operativa normal, pero al escribir en el filtro de tabla se repintan coincidencias de todos los productos cargados.

