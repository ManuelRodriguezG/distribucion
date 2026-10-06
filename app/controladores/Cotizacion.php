<?php

class Cotizacion extends Controlador {
  public function index() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'La cotizacion aun no esta habilitada para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Pedido',
      'cliente' => SesionCliente::perfil(),
      'contratos' => $api->contratos()
    );
    $this->render('cotizacion', $datos);
  }

  public function dryrun() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'La cotizacion aun no esta habilitada para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionDryRun(is_array($payload) ? $payload : array()));
  }

  public function registrar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'La cotizacion aun no esta habilitada para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->pedidoRegistrar(is_array($payload) ? $payload : array()));
  }
}
