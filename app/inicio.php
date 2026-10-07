<?php

$secureSessionCookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
if (PHP_VERSION_ID >= 70300) {
  session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => '/',
    'secure' => $secureSessionCookie,
    'httponly' => true,
    'samesite' => 'Lax'
  ));
} else {
  session_set_cookie_params(0, '/; samesite=Lax', '', $secureSessionCookie, true);
}
session_start();

require_once __DIR__ . '/config/configuracion.php';
require_once __DIR__ . '/core/SesionCliente.php';
require_once __DIR__ . '/core/ApiClient.php';
require_once __DIR__ . '/core/Controlador.php';
require_once __DIR__ . '/core/Core.php';