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

