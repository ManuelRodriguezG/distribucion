# Prompts listos para dividir chats

## Orden recomendado de implementacion

1. Chat 00 - Arquitectura y roadmap.
2. Chat 01 - API ERP y contratos.
3. Chat 07 - Administracion ERP.
4. Chat 02 - Autenticacion de clientes.
5. Chat 03 - Catalogo visual.
6. Chat 04 - Precios, permisos y listas.
7. Chat 05 - Inventario y disponibilidad.
8. Chat 06 - Cotizaciones y prepedidos.
9. Chat 08 - UI/UX y assets.
10. Chat 09 - Pruebas, deploy y seguridad.

## Documentos guia creados

- `docs/RESPONSABILIDADES_ERP_DISTRIBUCION.md`: leer en chats del ERP.
- `docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md`: leer antes de implementar en Distribucion.
- `docs/ROADMAP_PARALELO_ERP_DISTRIBUCION.md`: leer para coordinar avances ERP/Distribucion.
- `docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md`: leer cuando se trabaje integracion API.
- `docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md`: leer antes de decisiones de sesion, cache, mocks o errores.
- `docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md`: leer antes de implementar pantallas.
- `docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md`: leer antes de cerrar una fase como terminada.
- `docs/PRIMEROS_PASOS_IMPLEMENTACION_MVP.md`: leer para saber con que chat iniciar y que resultado esperar.
- `docs/CIERRE_ARQUITECTURA_CHAT_00.md`: leer como resumen final antes de comenzar implementacion.

## Chats que tocan ERP

- Chat 01 - API ERP y contratos.
- Chat 07 - Administracion ERP.

Estos chats deben leer tambien:

```text
C:\xampp\htdocs\panel_de_control\AGENTS.md
```

Y deben respetar:

- ERP es fuente de verdad.
- No mezclar sesion interna ERP con cliente externo.
- No exponer costos, margenes, proveedores ni stock exacto.
- No crear ventas ni descontar inventario desde Distribucion en MVP.

## Chats que trabajan Distribucion

- Chat 02 - Autenticacion de clientes.
- Chat 03 - Catalogo visual.
- Chat 04 - Precios, permisos y listas.
- Chat 05 - Inventario y disponibilidad.
- Chat 06 - Cotizaciones y prepedidos.
- Chat 08 - UI/UX y assets.
- Chat 09 - Pruebas, deploy y seguridad.

Estos chats deben trabajar sobre:

```text
C:\xampp\htdocs\distribucion
```

Y deben respetar:

- Consumir ERP por API.
- No consultar tablas internas del ERP.
- No duplicar catalogo, precios, inventario ni permisos.
- Usar mocks solo como temporales y marcados.

## Chat 00 - Arquitectura y roadmap

```text
Estamos trabajando en el proyecto Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/00-arquitectura-y-roadmap.md.
Quiero trabajar solo arquitectura, roadmap, alcance MVP y decisiones tecnicas generales. No programes todavia salvo que te lo pida.
```

## Chat 01 - API ERP y contratos

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion y la API vive en el ERP C:\xampp\htdocs\panel_de_control.
Lee AGENTS.md, .agents/modulos/01-api-erp-contratos.md y docs/API_DISTRIBUCION_INSTRUCCIONES.md.
Tambien lee C:\xampp\htdocs\panel_de_control\AGENTS.md antes de tocar el ERP.
Quiero construir/ajustar los contratos de DistribucionApi sin duplicar logica del ERP.
```

## Chat 02 - Autenticacion de clientes

```text
Estamos trabajando en el proyecto Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/02-autenticacion-clientes.md.
Quiero trabajar registro, login, sesion externa, perfil de cliente y estados de aprobacion. Si necesitas API, revisa docs/API_DISTRIBUCION_INSTRUCCIONES.md.
```

## Chat 03 - Catalogo visual

```text
Estamos trabajando en el proyecto Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/03-catalogo-visual.md.
Quiero trabajar el catalogo visual, tarjetas, filtros, buscador y detalle de producto consumiendo la API del ERP.
```

## Chat 04 - Precios, permisos y listas

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/04-precios-permisos-listas.md.
Quiero trabajar la logica de visualizacion de precios por permisos, cliente, grupo y lista asignada. Recuerda que el calculo real vive en el ERP.
```

## Chat 05 - Inventario y disponibilidad

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/05-inventario-disponibilidad.md.
Quiero trabajar disponibilidad comercial sin exponer stock exacto ni afectar inventario.
```

## Chat 06 - Cotizaciones y prepedidos

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/06-cotizaciones-prepedidos.md.
Quiero trabajar carrito/lista de cotizacion, dry-run y registro de solicitud sin crear venta ni descontar inventario.
```

## Chat 07 - Administracion ERP

```text
Estamos trabajando en Distribucion, pero este modulo toca el ERP.
Lee C:\xampp\htdocs\distribucion\AGENTS.md, C:\xampp\htdocs\distribucion\.agents\modulos\07-admin-erp-distribucion.md y C:\xampp\htdocs\panel_de_control\AGENTS.md.
Quiero crear/ajustar la administracion interna para aprobar clientes, asignar tipo/lista/permisos y revisar cotizaciones.
```

## Chat 08 - UI/UX y assets

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/08-ui-ux-assets.md.
Quiero trabajar interfaz, estilos, experiencia movil, componentes visuales y estados de UI.
```

## Chat 09 - Pruebas, deploy y seguridad

```text
Estamos trabajando en Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y .agents/modulos/09-pruebas-deploy-seguridad.md.
Quiero revisar pruebas, seguridad local/productiva, configuracion por ambiente y preparacion de despliegue.
```



## Prompts detallados para ERP

### ERP 01 - Crear contratos base de DistribucionApi

```text
Estamos trabajando en dos proyectos:

- Distribucion: C:\xampp\htdocs\distribucion
- ERP fuente de verdad: C:\xampp\htdocs\panel_de_control

Quiero trabajar dentro del ERP para crear/ajustar la API `DistribucionApi` que consumira Distribucion.

Lee primero:

1. C:\xampp\htdocs\panel_de_control\AGENTS.md
2. C:\xampp\htdocs\distribucion\AGENTS.md
3. C:\xampp\htdocs\distribucion\.agents\modulos\01-api-erp-contratos.md
4. C:\xampp\htdocs\distribucion\docs\RESPONSABILIDADES_ERP_DISTRIBUCION.md
5. C:\xampp\htdocs\distribucion\docs\CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
6. C:\xampp\htdocs\distribucion\docs\API_DISTRIBUCION_INSTRUCCIONES.md

Objetivo de este chat:

Crear o ajustar los endpoints base de `DistribucionApi` para el MVP, empezando por:

- `GET /DistribucionApi/contratos`
- `GET /DistribucionApi/configuracion_inicial`

Reglas importantes:

- ERP es fuente de verdad.
- Distribucion no debe consultar tablas internas del ERP.
- No exponer costos, margenes, proveedores ni stock exacto.
- No mezclar sesion interna del ERP con clientes externos.
- Mantener respuestas JSON consistentes.
- Antes de programar, revisa patrones existentes del ERP y dime el plan corto.
```

### ERP 02 - Clientes externos, aprobacion y permisos

```text
Estamos trabajando en el ERP `C:\xampp\htdocs\panel_de_control` para soportar clientes externos de Distribucion.

Lee primero:

1. C:\xampp\htdocs\panel_de_control\AGENTS.md
2. C:\xampp\htdocs\distribucion\AGENTS.md
3. C:\xampp\htdocs\distribucion\.agents\modulos\02-autenticacion-clientes.md
4. C:\xampp\htdocs\distribucion\.agents\modulos\07-admin-erp-distribucion.md
5. C:\xampp\htdocs\distribucion\docs\RESPONSABILIDADES_ERP_DISTRIBUCION.md
6. C:\xampp\htdocs\distribucion\docs\DECISIONES_TECNICAS_MVP_DISTRIBUCION.md
7. C:\xampp\htdocs\distribucion\docs\CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md

Objetivo de este chat:

Implementar o ajustar el soporte ERP para:

- Solicitudes de acceso de clientes externos.
- Estados `pendiente`, `en_revision`, `aprobado`, `rechazado`, `suspendido`.
- Login externo separado de sesion interna ERP.
- Asignacion de tipo de cliente.
- Asignacion de lista de precios.
- Asignacion de permisos comerciales.

Endpoints externos esperados:

- `POST /DistribucionApi/auth/registro`
- `POST /DistribucionApi/auth/login`

Administracion interna esperada:

- Bandeja de solicitudes.
- Aprobar cliente.
- Rechazar cliente.
- Suspender cliente.
- Asignar tipo/lista/permisos.

Reglas importantes:

- No aprobar automaticamente salvo decision documentada.
- No usar la sesion interna del ERP para el cliente externo.
- El token externo no debe abrir el panel ERP.
- No exponer informacion sensible en respuestas externas.
- Antes de programar, revisa si ya existen entidades de clientes, prospectos, permisos o listas para reutilizarlas.
```

### ERP 03 - Catalogo, precios, disponibilidad y cotizaciones

```text
Estamos trabajando en el ERP `C:\xampp\htdocs\panel_de_control` para exponer datos comerciales a Distribucion sin duplicar logica.

Lee primero:

1. C:\xampp\htdocs\panel_de_control\AGENTS.md
2. C:\xampp\htdocs\distribucion\AGENTS.md
3. C:\xampp\htdocs\distribucion\.agents\modulos\01-api-erp-contratos.md
4. C:\xampp\htdocs\distribucion\docs\RESPONSABILIDADES_ERP_DISTRIBUCION.md
5. C:\xampp\htdocs\distribucion\docs\CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
6. C:\xampp\htdocs\distribucion\docs\CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Crear o ajustar endpoints ERP para:

- Catalogo visible por canal/cliente.
- Detalle de producto por slug.
- Categorias, marcas y filtros.
- Resolucion de precios.
- Disponibilidad comercial sin stock exacto.
- Dry-run de cotizacion.
- Registro de cotizacion con folio.

Endpoints esperados:

- `GET /DistribucionApi/catalogo`
- `GET /DistribucionApi/producto/{slug}`
- `GET /DistribucionApi/categorias`
- `GET /DistribucionApi/marcas`
- `GET /DistribucionApi/filtros`
- `POST /DistribucionApi/precios/resolver`
- `POST /DistribucionApi/disponibilidad/resolver`
- `POST /DistribucionApi/cotizacion/dryrun`
- `POST /DistribucionApi/cotizacion/registrar`

Reglas importantes:

- No exponer costos, margenes, proveedores ni stock exacto.
- No aceptar precios del frontend como verdad.
- Recalcular todo en ERP antes de cotizar.
- No crear venta automatica.
- No crear pedido automatico.
- No apartar ni descontar inventario desde Distribucion.
- Antes de programar, ubica modelos/servicios existentes de catalogo, precios e inventario para reutilizar logica del ERP.
```
## Prompts detallados para Distribucion

### Distribucion 01 - Autenticacion y sesion externa

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/02-autenticacion-clientes.md
3. docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md
4. docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md
5. docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
6. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Implementar o ajustar en Distribucion:

- Registro de cliente externo.
- Login de cliente externo.
- Logout.
- Sesion externa local.
- Manejo de token devuelto por ERP.
- Estados `pendiente`, `en_revision`, `aprobado`, `rechazado`, `suspendido`.
- Proteccion de rutas privadas.

Endpoints ERP relacionados:

- `POST /DistribucionApi/auth/registro`
- `POST /DistribucionApi/auth/login`
- `GET /DistribucionApi/configuracion_inicial`

Reglas importantes:

- No usar sesion interna del ERP.
- No aprobar clientes desde Distribucion.
- No asignar permisos ni listas desde Distribucion.
- Guardar solo la sesion/token externo necesario.
- Si el ERP no responde, mostrar error claro sin inventar estado.
- Antes de programar, revisa los patrones actuales de controladores, vistas, SesionCliente y ApiClient.
```

### Distribucion 02 - Catalogo visual

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/03-catalogo-visual.md
3. docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md
4. docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md
5. docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
6. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Implementar o ajustar la experiencia de catalogo:

- Listado de productos.
- Busqueda.
- Filtros por categoria/marca/atributos.
- Tarjetas de producto.
- Detalle de producto.
- Estados de carga, error, sin resultados y sin permiso.

Endpoints ERP relacionados:

- `GET /DistribucionApi/catalogo`
- `GET /DistribucionApi/producto/{slug}`
- `GET /DistribucionApi/categorias`
- `GET /DistribucionApi/marcas`
- `GET /DistribucionApi/filtros`

Reglas importantes:

- No guardar catalogo maestro como verdad.
- No consultar tablas internas del ERP.
- No mostrar productos no autorizados.
- No exponer costos, proveedores, margenes ni stock exacto.
- Usar mocks solo si el endpoint ERP aun no existe y marcarlos como temporales.
- Antes de programar, revisa layout, vistas existentes y assets disponibles.
```

### Distribucion 03 - Precios, permisos y disponibilidad

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/04-precios-permisos-listas.md
3. .agents/modulos/05-inventario-disponibilidad.md
4. docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md
5. docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md
6. docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
7. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Implementar o ajustar la visualizacion de:

- Precio autorizado por ERP.
- Precio oculto con mensaje comercial.
- Permisos por accion.
- Disponibilidad comercial.
- CTAs segun permiso y disponibilidad.

Endpoints ERP relacionados:

- `POST /DistribucionApi/precios/resolver`
- `POST /DistribucionApi/disponibilidad/resolver`
- Tambien los campos `precio`, `disponibilidad` y `acciones` devueltos por catalogo/detalle.

Reglas importantes:

- Distribucion no calcula precios finales.
- Distribucion no aplica descuentos propios.
- Distribucion no muestra stock exacto.
- Si falla precio, mostrar `Solicitar precio`.
- Si falla disponibilidad, mostrar `Consultar disponibilidad`.
- ERP debe revalidar todo al cotizar.
```

### Distribucion 04 - Cotizaciones

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/06-cotizaciones-prepedidos.md
3. docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md
4. docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md
5. docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md
6. docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md
7. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Implementar o ajustar:

- Carrito/lista de cotizacion temporal.
- Agregar productos autorizados.
- Editar cantidades.
- Comentarios del cliente.
- Dry-run contra ERP.
- Registro final contra ERP.
- Confirmacion con folio real.

Endpoints ERP relacionados:

- `POST /DistribucionApi/cotizacion/dryrun`
- `POST /DistribucionApi/cotizacion/registrar`

Reglas importantes:

- No registrar cotizacion sin validacion ERP.
- No inventar folio.
- No crear pedido.
- No crear venta.
- No apartar ni descontar inventario.
- Guardar localmente solo seleccion temporal: `id_sku`, cantidad y comentarios.
- El ERP recalcula precios, disponibilidad y permisos.
```

### Distribucion 05 - UI/UX y estados visuales

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/08-ui-ux-assets.md
3. docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md
4. docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md
5. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md

Objetivo de este chat:

Pulir la experiencia visual del MVP:

- Layout general.
- Navegacion.
- Responsive movil/escritorio.
- Componentes de catalogo.
- Estados vacios.
- Estados de error.
- Estados sin permiso.
- Mensajes comerciales.
- Consistencia visual con Artiani/ERP.

Reglas importantes:

- Construir experiencia usable, no landing page decorativa.
- No usar texto visible para explicar funcionalidades obvias.
- Priorizar claridad comercial y confianza.
- No ocultar fallas del ERP inventando datos.
- Verificar que textos no se encimen ni rompan en movil.
```

### Distribucion 06 - Pruebas, seguridad y cierre MVP

```text
Estamos trabajando en Distribucion en `C:\xampp\htdocs\distribucion`.

Lee primero:

1. AGENTS.md
2. .agents/modulos/09-pruebas-deploy-seguridad.md
3. docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md
4. docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md
5. docs/ROADMAP_PARALELO_ERP_DISTRIBUCION.md
6. docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md

Objetivo de este chat:

Revisar que el MVP este listo o detectar pendientes:

- Rutas principales.
- Sesion externa.
- Integracion API.
- Manejo de errores.
- Seguridad minima.
- CORS/configuracion por ambiente.
- No exposicion de datos sensibles.
- Flujo completo registro-login-catalogo-cotizacion.

Reglas importantes:

- No declarar listo si depende de mocks sin marcar.
- No declarar listo si el ERP no genera folio real.
- No declarar listo si hay venta/pedido/descuento de inventario automatico.
- Validar PHP con `C:\xampp\php\php.exe -l ruta\archivo.php` cuando toque codigo.
- Reportar pendientes por fase usando criterios de aceptacion.
```


