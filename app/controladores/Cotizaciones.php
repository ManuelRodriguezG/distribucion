<?php

class Cotizaciones extends Controlador {
  public function index() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Cotizaciones',
      'cliente' => SesionCliente::perfil(),
      'cotizaciones' => $api->cotizacionListar($_GET),
      'contratos' => $api->contratos()
    );
    $this->render('cotizaciones', $datos);
  }

  public function detalle() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionDetalle($_GET));
  }

  public function guardar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionGuardarBorrador(is_array($payload) ? $payload : array()));
  }

  public function cancelar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionCancelar(is_array($payload) ? $payload : array()));
  }

  public function duplicar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionDuplicar(is_array($payload) ? $payload : array()));
  }

  public function enviar_pedido() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aun no estan habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionEnviarPedido(is_array($payload) ? $payload : array()));
  }
}
