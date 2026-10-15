<?php

define('RUTA_APP', dirname(__DIR__));
define('RUTA_PROYECTO', dirname(__DIR__, 2));
define('NOMBRE_SITIO', 'Distribucion');

date_default_timezone_set('America/Mexico_City');

define('DISTRIBUCION_DOMINIO_LOCAL', 'distribucion.artiani.com.local');
define('DISTRIBUCION_DOMINIO_PRODUCCION', 'distribucion.artiani.com.mx');
define('ERP_DOMINIO_LOCAL', 'panel.com.local');
define('ERP_DOMINIO_PRODUCCION', 'sys.artiani.com.mx');

$hostActual = isset($_SERVER['HTTP_HOST']) ? strtolower((string) $_SERVER['HTTP_HOST']) : DISTRIBUCION_DOMINIO_LOCAL;
$hostActual = explode(':', $hostActual)[0];
$esProduccion = $hostActual === DISTRIBUCION_DOMINIO_PRODUCCION;

define('URL_RAIZ', $esProduccion ? 'https://' . DISTRIBUCION_DOMINIO_PRODUCCION : 'http://' . DISTRIBUCION_DOMINIO_LOCAL);
define('ERP_URL_RAIZ', $esProduccion ? 'https://' . ERP_DOMINIO_PRODUCCION : 'http://' . ERP_DOMINIO_LOCAL);
define('ERP_API_BASE_URL', ERP_URL_RAIZ . '/DistribucionApi');
define('ERP_API_TIMEOUT', 15);
define('ERP_API_CANAL', 'distribucion');

define('DISTRIBUCION_TESTER_API_HABILITADO', false);
