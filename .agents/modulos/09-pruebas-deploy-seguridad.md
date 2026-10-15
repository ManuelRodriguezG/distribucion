# Modulo 09 - Pruebas, deploy y seguridad

## Objetivo

Definir verificaciones para que Distribucion sea estable localmente y segura en produccion.

## Local

- Host: `http://distribucion.artiani.com.local`.
- Ruta: `C:\xampp\htdocs\distribucion`.
- ERP local: `C:\xampp\htdocs\panel_de_control`.
- API base local: `http://panel.com.local/DistribucionApi`.

## Produccion futura

- Dominio: `https://distribucion.artiani.com.mx`.
- ERP/API productiva: `https://sys.artiani.com.mx/DistribucionApi`.
- Requiere HTTPS.
- CORS debe incluir solo dominio productivo.

## Pruebas minimas

- Sintaxis PHP en archivos editados.
- Navegacion de rutas MVC.
- Registro con API no disponible debe fallar amigablemente.
- Login con API no disponible debe fallar amigablemente.
- Catalogo con API no disponible debe mostrar estado vacio/error.
- Cotizacion dry-run no debe aceptar GET.
- Cotizacion registrar debe requerir cliente autenticado.

## Seguridad

- No guardar secretos en repo.
- No exponer tokens en vistas.
- No mostrar errores SQL.
- No confiar en precios del frontend.
- No mostrar costos.
- No mostrar stock exacto.
- No mezclar cookie/sesion interna ERP con cliente externo.

## Pendientes

- Pruebas automatizadas basicas.
- Logs de errores.
- Configuracion por ambiente.
- Endurecer headers.
- Rate limiting via ERP/API.
- CSRF para formularios si se mantienen POST server-side.

## Validacion 2026-10-14

- `https://distribucion.artiani.com.mx` responde HTTP 200.
- `https://sys.artiani.com.mx/DistribucionApi/contratos` responde HTTP 200 con JSON valido.
- `https://sys.artiani.com.mx/DistribucionApi/configuracion_inicial` responde HTTP 200 con JSON valido.
- Hallazgo: el HTML productivo aun referencia assets con `https://distribucion.artiani.com.local/...`; se corrigio `app/config/configuracion.php` para detectar host y usar `https://distribucion.artiani.com.mx` en produccion.
- Accion necesaria: subir a produccion los cambios de `app/config/configuracion.php` y archivos relacionados para que `URL_RAIZ` y `ERP_API_BASE_URL` apunten al ambiente correcto.

## Diagnostico local/productivo 2026-10-14

- La ruta publica `/cuenta/diagnostico` exige sesion y redirige a `/auth/login?motivo=ruta_protegida` sin autenticar; no se obtuvieron perfiles ni tokens.
- `GET /DistribucionApi/contratos` responde igual en `panel.com.local` y `sys.artiani.com.mx`: HTTP 200, version `fase1-2026-09-08`, 38 endpoints y los mismos origenes CORS.
- El HTML publico ya genera assets con `https://distribucion.artiani.com.mx`, mientras local genera `http://distribucion.artiani.com.local`; esto es correcto por ambiente, pero las versiones observadas de assets son distintas: productivo `1791959926`, local `1791439732`/`1791437776`.
- Apache local usa `DocumentRoot C:/xampp/htdocs/distribucion` para `distribucion.artiani.com.local`; el `.htaccess` enruta correctamente a `index.php`.
- PHP local es 8.0.30 y su `date.timezone` global era `Europe/Berlin`; se fija en `app/config/configuracion.php` a `America/Mexico_City` para evitar fechas distintas entre entornos.
- Los logs locales conservan errores históricos de versiones anteriores y un timeout del cliente API; antes de comparar comportamiento actual se debe reproducir la ruta y revisar solo entradas nuevas.
- Siguiente accion: publicar el mismo commit en ambos frentes y repetir `/cuenta/diagnostico` con la misma cuenta, comparando `ambiente`, `sesion_frontend`, `perfil_api` y `resumen_endpoints` sin mostrar tokens.
- Correccion: `panel.com.local` no usa actualmente `artianilocal`; `app/config/mysql.local.php` esta habilitado y lo sobrescribe para usar `artianicom_sys`, igual que el bloque productivo de `sys.artiani.com.mx`.
- Con ambas instalaciones sobre `artianicom_sys`, la hipotesis prioritaria cambia a: el frontend productivo ejecuta otra revision de `ApiClient`/PHP, el servidor productivo no entrega `Authorization` a PHP, o la API productiva carga codigo/configuracion distinto aunque el contrato publico coincida.

