# Modulo 08 - UI, UX y assets

## Objetivo

Definir la experiencia visual de Distribucion para clientes externos, manteniendo coherencia con el ecosistema ERP.

## Archivos actuales

- `app/vistas/layouts/base.php`.
- `public/assets/css/app.css`.
- `public/assets/js/app.js`.
- Vistas en `app/vistas/paginas`.

## Linea visual

- Profesional.
- Comercial.
- Clara para clientes externos.
- Menos densa que ERP interno.
- Mas enfocada en catalogo, productos y solicitud.

## Pantallas MVP

- Inicio.
- Registro.
- Login.
- Catalogo.
- Detalle de producto.
- Cotizacion.
- Cuenta/estatus.

## Reglas

- No crear landing generica sin funcion.
- Primera pantalla debe dirigir a catalogo o solicitud de acceso.
- Botones y mensajes deben reflejar permisos reales.
- La UI debe funcionar aunque precio este oculto.
- La UI debe funcionar aunque API ERP no este lista.

## Pendientes

- Branding final Artiani Distribucion.
- Paleta final.
- Componentes de producto.
- Estados vacios.
- Estados de carga.
- Mensajes de error.
- Adaptacion movil.


## Decision 2026-09-09

- Se descarta la vista inicial tipo landing/demo y se rehace el frontend como portal B2B operativo inspirado en el ERP/Metronic: sidebar, toolbar superior, cards sobrias, tablas densas, badges de estado, filtros compactos y acciones con iconos.
- Catalogo usa tabla como vista principal y cards como alternativa visual; no calcula precios, disponibilidad ni permisos localmente, solo presenta lo que devuelve la API ERP.
- Cotizacion conserva carrito local temporal y usa dry-run/registro del ERP; la interfaz muestra explicitamente que no aparta inventario ni crea pedido automatico.

## Decision 2026-10-06 - Home publica sobria

- Se agrega una home publica tipo portal de acceso comercial, no landing larga de marketing, para explicar el acceso B2B antes del login.
- El layout `publico.php` usa header simple con marca, acciones de login/registro y footer compacto; el dashboard autenticado sigue usando `base.php`.
- La home mantiene tono sobrio y operativo, sin productos reales, precios, stock, carrusel ni promesas de aprobacion automatica.
- Se refuerza la home con banner principal visual, seccion "Elige tu mascota" adaptada al contexto B2B y componente para tiendas/revendedores; todos funcionan como orientacion comercial, no como catalogo publico.
- Se ajusta el copy visible para clientes: un solo hero, tono comercial, sin menciones a ERP, B2B privado, pedidos preliminares ni lenguaje defensivo; el cierre ofrece dos caminos claros, iniciar sesion o solicitar acceso comercial.
- Se incorpora el logo real de Artiani en el header publico y se agrega "Distribucion" como etiqueta de contexto, sin alterar el archivo de marca original.
- Se integran assets WebP para la home publica: banner principal, bloque para tiendas/revendedores y tarjetas visuales para acuario, perros, gatos, aves, reptiles/tortugas y pequenos mamiferos.

## Ajuste 2026-10-06 - Auth publico consistente

- `auth.php`, login y registro usan lenguaje comercial publico: Artiani Distribucion, acceso comercial para negocios y solicitud de acceso.
- Se retiran de pantallas publicas terminos internos como ERP, B2B privado, permisos/listas y lenguaje tecnico.
- El registro exitoso se presenta como estado final con folio, siguiente paso y acciones claras, sin dejar el formulario visible por defecto.
- Los errores de API se muestran con etiquetas humanas para que validaciones fiscales o de campos faltantes no aparezcan con nombres tecnicos.
