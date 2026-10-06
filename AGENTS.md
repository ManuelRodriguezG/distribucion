# AGENTS.md - Proyecto Distribucion

## Proyecto activo y ruta canonica

- Ruta activa: `C:\xampp\htdocs\distribucion`.
- Host local canonico: `http://distribucion.artiani.com.local`.
- Dominio productivo previsto: `https://distribucion.artiani.com.mx`.
- ERP fuente de verdad: `C:\xampp\htdocs\panel_de_control`.
- API ERP prevista: `http://localhost/panel_de_control/DistribucionApi`.
- No crear logica principal de catalogo, precios, inventario, listas o clientes autorizados dentro de Distribucion si pertenece al ERP.

Este proyecto es una plataforma externa para clientes, revendedores, mayoristas y distribuidores autorizados. Debe sentirse parte del ecosistema Artiani/ERP, pero estar separado del panel interno.

## Lectura inicial recomendada para cualquier chat

1. Lee este archivo completo.
2. Lee `.agents/README.md` para elegir el modulo de trabajo.
3. Lee solo el archivo de modulo necesario en `.agents/modulos/`.
4. Si el trabajo toca contratos con ERP, lee `docs/API_DISTRIBUCION_INSTRUCCIONES.md`.
5. Si el trabajo requiere comparar con el ERP, revisar primero `C:\xampp\htdocs\panel_de_control\AGENTS.md` y despues buscar archivos puntuales.

## Arquitectura base

- Proyecto PHP MVC propio bajo XAMPP/cPanel.
- `index.php` carga `app/inicio.php`.
- `app/inicio.php` carga configuracion, sesion externa, cliente API, controlador base y router.
- `app/core/Core.php` resuelve rutas con formato `/Controlador/metodo/parametros`.
- `app/core/Controlador.php` carga modelos, vistas y layout base.
- `app/core/ApiClient.php` concentra llamadas al ERP.
- `app/core/SesionCliente.php` gestiona sesion del cliente externo.
- `app/modelos/DistribucionApiCliente.php` encapsula endpoints del ERP.
- Vistas en `app/vistas/paginas`.
- Layout en `app/vistas/layouts/base.php`.
- Assets en `public/assets`.

## Regla central

Distribucion es consumidor. El ERP es fuente de verdad.

No duplicar:

- Catalogo maestro.
- SKUs.
- Variantes.
- Presentaciones.
- Paquetes.
- Imagenes.
- Categorias.
- Marcas.
- Atributos.
- Listas de precios.
- Inventario.
- Estados operativos.
- Permisos comerciales definitivos.
- Aprobacion comercial.

Distribucion puede tener:

- Sesion externa.
- UI comercial.
- Cache ligera no autoritativa.
- Carrito o cotizacion local temporal.
- Adaptadores de API.
- Vistas y experiencia del cliente.

## Modulos de trabajo por chat

Usar estos contextos para dividir avances:

- `.agents/modulos/00-arquitectura-y-roadmap.md`
- `.agents/modulos/01-api-erp-contratos.md`
- `.agents/modulos/02-autenticacion-clientes.md`
- `.agents/modulos/03-catalogo-visual.md`
- `.agents/modulos/04-precios-permisos-listas.md`
- `.agents/modulos/05-inventario-disponibilidad.md`
- `.agents/modulos/06-cotizaciones-prepedidos.md`
- `.agents/modulos/07-admin-erp-distribucion.md`
- `.agents/modulos/08-ui-ux-assets.md`
- `.agents/modulos/09-pruebas-deploy-seguridad.md`

## Rutas iniciales del proyecto

- `/` inicio.
- `/auth/login`.
- `/auth/registro`.
- `/auth/salir`.
- `/activar-cuenta?token={token}`.
- `/catalogo`.
- `/catalogo/producto/{slug}`.
- `/cotizacion`.
- `/cotizacion/dryrun`.
- `/cotizacion/registrar`.
- `/cuenta`.

## Contratos iniciales esperados del ERP

- `GET /DistribucionApi/contratos`.
- `GET /DistribucionApi/configuracion_inicial`.
- `POST /DistribucionApi/auth/registro`.
- `POST /DistribucionApi/auth/login`.
- `POST /DistribucionApi/auth/activar_consultar`.
- `POST /DistribucionApi/auth/activar_contrasenia`.
- `GET /DistribucionApi/catalogo`.
- `GET /DistribucionApi/producto/{slug}`.
- `GET /DistribucionApi/categorias`.
- `GET /DistribucionApi/marcas`.
- `GET /DistribucionApi/filtros`.
- `POST /DistribucionApi/precios/resolver`.
- `POST /DistribucionApi/disponibilidad/resolver`.
- `POST /DistribucionApi/cotizacion/dryrun`.
- `POST /DistribucionApi/cotizacion/registrar`.

## Convencion de rutas

- Todas las URLs publicas y links internos deben escribirse en minusculas.
- Evitar rutas tipo `/Auth`, `/Catalogo`, `/Cotizacion`, `catalogoErp` o mezclas camelCase/PascalCase en URLs.
- Si el nombre PHP del controlador usa mayusculas por convencion de clase, la ruta publicada debe seguir siendo minuscula.
- `Core.php` redirige controlador/metodo a minusculas como ruta canonica; no depender de rutas con mayusculas en produccion.
## Seguridad

- El login externo no debe usar la sesion interna del ERP.
- No exponer costos, margenes, proveedores ni stock exacto.
- No confiar en precios enviados por frontend.
- No crear pedidos ni ventas automaticas en MVP.
- No descontar inventario desde Distribucion.
- CORS autorizado local: `http://distribucion.artiani.com.local`.
- CORS autorizado produccion: `https://distribucion.artiani.com.mx`.

## Comandos utiles

- Validar PHP: `C:\xampp\php\php.exe -l ruta\archivo.php`.
- Buscar archivos: `rg --files`.
- Buscar texto: `rg -n "texto"`.

## Reglas para agentes

- Antes de editar, identifica el modulo y lee su archivo en `.agents/modulos/`.
- Mantener cambios pequeÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â±os y enfocados.
- Actualizar el archivo de modulo cuando se tome una decision relevante.
- Si un chat se satura, continuar leyendo `AGENTS.md` y el modulo correspondiente.
- No modificar `panel_de_control` desde este proyecto salvo que la tarea indique trabajar la API del ERP.
- Si se modifica ERP, respetar su `AGENTS.md`.
- No inventar reglas de precios, permisos o inventario sin documentarlas primero.
