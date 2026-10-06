# Modulo 09 - Pruebas, deploy y seguridad

## Objetivo

Definir verificaciones para que Distribucion sea estable localmente y segura en produccion.

## Local

- Host: `http://distribucion.artiani.com.local`.
- Ruta: `C:\xampp\htdocs\distribucion`.
- ERP local: `C:\xampp\htdocs\panel_de_control`.
- API base actual: `http://localhost/panel_de_control/DistribucionApi`.

## Produccion futura

- Dominio: `https://distribucion.artiani.com.mx`.
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

