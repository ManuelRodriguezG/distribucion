<?php

class Pedidos extends Controlador {
  public function index() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.pedido.preliminar', 'Los pedidos aun no estan habilitados para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Pedidos',
      'cliente' => SesionCliente::perfil(),
      'pedidos' => $api->pedidoListar($_GET)
    );
    $this->render('pedidos', $datos);
  }

  public function detalle() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.pedido.preliminar', 'Los pedidos aun no estan habilitados para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->pedidoDetalle($_GET));
  }

  public function responder() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.pedido.preliminar', 'Los pedidos aun no estan habilitados para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->pedidoResponder(is_array($payload) ? $payload : array()));
  }
}
