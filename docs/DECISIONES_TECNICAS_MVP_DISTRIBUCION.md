# Decisiones tecnicas MVP - Distribucion

## Proposito

Este documento concentra decisiones tecnicas generales para implementar el MVP sin mezclar responsabilidades entre Distribucion y ERP.

## Decision 1 - Autenticacion externa

Distribucion debe manejar una sesion externa de cliente, separada de la sesion interna del ERP.

Decision:

- El ERP autentica al cliente externo.
- El ERP devuelve un token seguro.
- Distribucion guarda el token en su sesion externa.
- Las llamadas privadas al ERP usan `Authorization: Bearer {token}`.
- El token no debe servir para entrar al panel interno del ERP.

Pendiente tecnico:

- Definir si el token sera JWT firmado o token opaco guardado en tabla del ERP.

Recomendacion MVP:

- Usar token opaco si el ERP ya maneja sesiones/tokens simples y se quiere revocacion facil.
- Usar JWT solo si el ERP ya tiene infraestructura clara para firma, expiracion, rotacion y revocacion.

## Decision 2 - Estados de cliente

El ERP decide el estado comercial del cliente.

Estados aceptados:

- `pendiente`
- `en_revision`
- `aprobado`
- `rechazado`
- `suspendido`

Regla:

- Solo `aprobado` puede acceder a funciones privadas completas.
- `pendiente` y `en_revision` pueden ver una pantalla de estado.
- `rechazado` y `suspendido` deben ver mensaje claro sin acceso a catalogo privado.

## Decision 3 - Permisos por accion

Distribucion no debe inferir permisos solo por tipo de cliente.

Decision:

- El ERP devuelve permisos granulares en login/configuracion.
- Distribucion habilita o deshabilita acciones segun permisos.
- El ERP revalida permisos en cada endpoint privado.

Permisos base:

- `distribucion.catalogo.ver`
- `distribucion.catalogo.ver_detalle`
- `distribucion.precio.ver_publico`
- `distribucion.precio.ver_mayoreo`
- `distribucion.precio.ver_lista_asignada`
- `distribucion.inventario.ver_disponibilidad`
- `distribucion.cotizacion.solicitar`
- `distribucion.catalogo.descargar`
- `distribucion.cuenta.editar`

## Decision 4 - Mocks temporales

Distribucion puede usar mocks ligeros para avanzar UI, pero no como fuente de verdad.

Reglas:

- Los mocks deben vivir claramente separados de modelos reales.
- Cada mock debe indicar el endpoint ERP que reemplaza temporalmente.
- Ninguna fase se considera lista hasta conectarse con ERP real o quedar marcada como pendiente de integracion.

Uso permitido:

- Maquetas visuales de catalogo.
- Flujo de carrito temporal.
- Estados vacios, errores y mensajes.
- Pruebas de layout.

Uso no permitido:

- Definir reglas finales de precios.
- Definir permisos comerciales definitivos.
- Definir disponibilidad real.
- Simular aprobaciones como si fueran definitivas.

## Decision 5 - Cache ligera

Distribucion puede tener cache ligera no autoritativa para mejorar experiencia, pero el ERP sigue siendo verdad.

Permitido:

- Cache temporal de configuracion inicial.
- Cache temporal de catalogo visible.
- Cache temporal de filtros, categorias y marcas.
- Cache de imagenes servidas desde URLs autorizadas.

No permitido:

- Cachear precios como verdad.
- Cachear disponibilidad como verdad.
- Usar cache para registrar cotizaciones sin revalidacion.
- Mantener una copia local completa del catalogo maestro.

Regla:

- Antes de registrar cotizacion, Distribucion siempre debe pedir dry-run o registro al ERP para recalcular todo.

## Decision 6 - Errores y fallbacks

Distribucion debe fallar de forma clara y segura cuando el ERP no responda.

Reglas UI:

- Si falla configuracion inicial: mostrar error de servicio no disponible.
- Si falla catalogo: mostrar estado de carga fallida y opcion de reintentar.
- Si falla precios: ocultar monto y mostrar `Solicitar precio` o mensaje equivalente.
- Si falla disponibilidad: mostrar `Consultar disponibilidad`.
- Si falla dry-run: no permitir registrar cotizacion.
- Si falla registro: no mostrar folio inventado.

Reglas tecnicas:

- No mostrar errores SQL.
- No mostrar trazas internas al cliente.
- Registrar errores tecnicos del lado servidor si existe mecanismo de logs.
- Mantener mensajes comerciales simples.

## Decision 7 - Cotizacion temporal

Distribucion puede guardar el carrito o lista de cotizacion de forma temporal.

Permitido:

- Guardar `id_sku`.
- Guardar cantidad.
- Guardar seleccion visual.
- Guardar comentarios del cliente.

No usar como verdad:

- Precio unitario.
- Subtotal.
- Disponibilidad.
- Permiso para cotizar.

Regla:

- En dry-run y registro, ERP recalcula y valida todo.

## Decision 8 - Folios y trazabilidad

El folio de cotizacion debe generarlo el ERP.

Decision:

- Distribucion muestra el folio devuelto.
- Distribucion no inventa folios finales.
- ERP debe guardar origen `distribucion`.
- ERP debe guardar snapshot comercial suficiente para seguimiento interno.

## Decision 9 - Seguridad minima

Para MVP:

- HTTPS obligatorio en produccion.
- CORS limitado a dominios autorizados.
- Rate limit en login, registro y cotizacion.
- Validacion servidor-servidor en ERP.
- No confiar en payload del frontend.
- No exponer costos, margenes, proveedores ni stock exacto.
- Tokens con expiracion.
- Logout debe destruir sesion externa de Distribucion.

## Decision 10 - Compatibilidad con cPanel/XAMPP

El MVP debe mantenerse compatible con PHP MVC propio bajo XAMPP/cPanel.

Decision:

- Evitar dependencias pesadas si no son necesarias.
- Mantener rutas en formato `/Controlador/metodo/parametros`.
- Centralizar llamadas ERP en `ApiClient` y `DistribucionApiCliente`.
- Mantener vistas en `app/vistas/paginas`.
- Mantener layout base en `app/vistas/layouts/base.php`.

## Criterio de decisiones listas

Estas decisiones se consideran listas para implementar cuando:

- ERP acepta la frontera de responsabilidades.
- Distribucion consume API por adaptadores, no por consultas directas.
- La sesion externa queda separada.
- Precios, disponibilidad y cotizacion se revalidan siempre en ERP.
- Los fallbacks no inventan informacion comercial.
