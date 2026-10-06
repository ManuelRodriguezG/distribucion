# Modulo 02 - Autenticacion de clientes

## Objetivo

Construir registro, login, sesion externa, recuperacion futura y perfil de cliente de Distribucion.

## Archivos actuales

- `app/controladores/Auth.php`.
- `app/core/`SesionCliente`.php`.
- `app/vistas/paginas/login.php`.
- `app/vistas/paginas/registro.php`.
- `app/vistas/paginas`/cuenta`.php`.
- `app/modelos/DistribucionApiCliente.php`.

## Estados de cliente

- `pendiente`.
- `en_revision`.
- `aprobado`.
- `rechazado`.
- `suspendido`.

## Tipos de cliente

- `publico`.
- `registrado`.
- `revendedor`.
- `mayorista`.
- `distribuidor_autorizado`.
- `administrador_interno`.

## Reglas

- No usar sesion interna del ERP.
- Login externo debe recibir token desde ERP.
- Cliente pendiente puede ver acceso limitado si el negocio lo permite.
- Cliente suspendido no debe ver precios ni registrar cotizaciones.
- Password/token se resuelve del lado ERP.

## Pendientes

- Definir expiracion de token.
- Definir refresh token o relogin.
- Definir recuperacion de contrasenia.
- Definir validacion de correo.
- Definir pantalla de estatus pendiente.


## Decisiones 2026-09-10

- Se agrego la ruta publica `/activar-cuenta?token={token}` para crear contrasena desde link generado por el ERP.
- La pantalla consulta `POST /DistribucionApi/auth/activar_consultar` al cargar y solo muestra formulario si `depurar.valido` es verdadero.
- La creacion de contrasena usa `POST /DistribucionApi/auth/activar_contrasenia` con `token` y `contrasenia`.
- Crear contrasena no inicia sesion automaticamente; despues se muestra accion para ir a `/auth/login`.
- El frontend no guarda contrasenas ni tokens de activacion en localStorage/sessionStorage y no muestra el token completo en pantalla.
- Se normalizan permisos en `SesionCliente` y se refresca la sesion desde `configuracion_inicial` despues de login, en Cuenta y antes de negar permisos; evita bloqueos por permisos recien asignados o formatos distintos del ERP.

## Decisiones 2026-09-11

- El login externo conserva `depurar.token` como token de sesion y normaliza `depurar.perfil` en `SesionCliente`.
- La sesion ahora conserva y normaliza `perfil.permisos` y `perfil.acciones`; las acciones son la capa preferida para navegacion y guardas de UI.
- Se agrega refresco de sesion contra `GET /DistribucionApi/auth/perfil` con Bearer token despues del login, al entrar a Cuenta y antes de negar accesos.
- La redireccion posterior al login usa `acciones.ver_catalogo`; si no esta habilitada, envia a `/cuenta`.

## Decisiones 2026-10-01 - Registro con preferencia fiscal

- El registro de acceso mayoreo captura una preferencia inicial de facturacion: `requiere_factura` y `facturacion` con RFC, razon social, regimen fiscal, uso CFDI, codigo postal fiscal, correo y comentarios fiscales.
- Estos datos no generan factura ni aprueban acceso; se envian al ERP dentro de `POST /DistribucionApi/auth/registro` para que la solicitud comercial los conserve durante revision interna.
- El pedido mantiene la opcion de confirmar si se factura o no; si el cliente ya indico que requiere factura, la politica esperada es facturar cada pedido entregado y pagado, salvo que en el pedido se elija no facturar.

## Decision 2026-10-04 - Mi cuenta editable y cambios revisables

- La vista `/cuenta` queda preparada para mostrar datos de contacto, datos comerciales, direccion/entrega principal, facturacion y solicitudes de cambio si `auth/perfil` los envia.
- El perfil actual de API solo devuelve datos minimos (`id_cliente_distribucion`, nombre, tipo, estatus, lista, permisos y acciones), por lo que muchos campos aparecen como pendientes hasta ampliar el contrato.
- Para edicion real se requiere endpoint API dedicado que separe cambios inmediatos de contacto contra cambios sensibles que requieren revision interna del ERP.
- Campos de contacto como telefono, WhatsApp, correo alterno y contacto principal podrian actualizarse directo con auditoria; direccion, datos fiscales, empresa, razon social, RFC, tipo de negocio o ciudad/estado deben generar solicitud de cambio en revision.

## Avance 2026-10-04 - Perfil editable y categorias de interes

- `GET /DistribucionApi/auth/perfil` ahora se espera con `contacto`, `datos_comerciales`, `entrega`, `facturacion`, `catalogo_modo`, `categorias_interes` y `solicitudes_cambio`.
- La pantalla `/cuenta` se convierte en formulario editable y envia cambios mediante `POST /DistribucionApi/auth/perfil/solicitar_cambio` usando el adaptador local `Cuenta::guardar`.
- El frontend no permite editar lista de precios, permisos, tipo de cliente, estatus ni modo de catalogo.
- Cambios de contacto pueden aplicarse directo por API; cambios comerciales, entrega y facturacion pueden quedar en revision interna del ERP.
- El registro de acceso mayoreo consulta categorias desde API y envia `categorias_interes` como objetos con `id_categoria_erp`, `nombre` y `ruta`.
- Si el catalogo autorizado viene vacio, la UI muestra que el catalogo esta siendo configurado y no intenta decidir visibilidad localmente.

## Ajuste 2026-10-05 - Categorias de interes para registro

- El registro de acceso mayoreo usa `GET /DistribucionApi/categorias_interes` para pintar el selector de intereses, no el catalogo general de categorias.
- La vista puede usar `depurar.jerarquia` para mostrar padres e hijos y `depurar.items` como respaldo plano.
- Cada categoria seleccionada se envia en `categorias_interes` con `id_categoria_erp`, `nombre`, `ruta` e `incluye_descendientes`.
- Si el cliente selecciona una categoria padre, el frontend no necesita enviar todos sus hijos; el ERP interpreta la seleccion con descendientes.

## Ajuste 2026-10-06 - Consumo de estados ampliados de API

- El login normaliza codigos seguros devueltos por API (`cuenta_en_revision`, `cuenta_rechazada`, `cuenta_suspendida`, `requiere_activacion`, `sin_permisos_comerciales`, `credenciales_invalidas`) a mensajes utiles sin decidir estados localmente.
- Las rutas protegidas guardan la ruta solicitada y redirigen a `/auth/login?motivo=ruta_protegida`; despues del login se intenta volver a esa ruta si es segura.
- El registro ya envia `requiere_factura` y el objeto `facturacion` estructurado, conservando tambien campos fiscales sueltos por compatibilidad con contrato anterior.
- Las pantallas de estado/acceso consumen `perfil.estado_acceso`, `campos_pendientes` y `contacto_soporte` cuando la API los entrega.

## Ajuste 2026-10-06 - Entrada publica antes del login

- `/` queda como home publica para usuarios sin sesion, con acciones claras hacia `/auth/login` y `/auth/registro`.
- Las rutas protegidas siguen usando `requerirCliente()` y redirigen a `/auth/login?motivo=ruta_protegida`.
- El dashboard privado se mantiene para usuarios autenticados y no se mezcla con el layout publico.
