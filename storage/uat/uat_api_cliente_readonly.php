<?php

require_once __DIR__ . "/../../app/inicio.php";
require_once RUTA_APP . "/modelos/DistribucionApiCliente.php";

$api = new DistribucionApiCliente();
$configuracion = $api->configuracionInicial();
$contratos = $api->contratos();
$catalogo = $api->catalogo(array("limite" => 3));

echo json_encode(array(
  "configuracion" => array(
    "error" => isset($configuracion["error"]) ? $configuracion["error"] : null,
    "mensaje" => isset($configuracion["mensaje"]) ? $configuracion["mensaje"] : null,
    "api" => isset($configuracion["api"]) ? $configuracion["api"] : null
  ),
  "contratos" => array(
    "error" => isset($contratos["error"]) ? $contratos["error"] : null,
    "mensaje" => isset($contratos["mensaje"]) ? $contratos["mensaje"] : null,
    "endpoints" => isset($contratos["endpoints"]) && is_array($contratos["endpoints"]) ? count($contratos["endpoints"]) : 0
  ),
  "catalogo" => array(
    "error" => isset($catalogo["error"]) ? $catalogo["error"] : null,
    "mensaje" => isset($catalogo["mensaje"]) ? $catalogo["mensaje"] : null,
    "items" => isset($catalogo["items"]) && is_array($catalogo["items"]) ? count($catalogo["items"]) : 0,
    "configurado" => isset($catalogo["configurado"]) ? $catalogo["configurado"] : null
  ),
  "base_url" => ERP_API_BASE_URL
), JSON_PRETTY_PRINT);
