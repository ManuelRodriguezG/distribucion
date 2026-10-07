<?php

class Cotizaciones extends Controlador {
  public function index() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
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
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionDetalle($_GET));
  }

  public function guardar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }

    $payload = $this->leerPayloadJson();
    $validacion = $this->validarItemsCotizacion($payload, 'Agrega al menos un producto antes de guardar la cotización.');
    if ($validacion !== true) {
      return $this->json(array('error' => true, 'mensaje' => $validacion), 422);
    }

    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionGuardarBorrador($payload));
  }

  public function cancelar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = $this->leerPayloadJson();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionCancelar($payload));
  }

  public function duplicar() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = $this->leerPayloadJson();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionDuplicar($payload));
  }

  public function enviar_pedido() {
    $this->requerirAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar', 'Las cotizaciones aún no están habilitadas para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }

    $payload = $this->leerPayloadJson();
    if (isset($payload['items'])) {
      $validacion = $this->validarItemsCotizacion($payload, 'Agrega al menos un producto antes de enviar la solicitud.');
      if ($validacion !== true) {
        return $this->json(array('error' => true, 'mensaje' => $validacion), 422);
      }
    }

    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->cotizacionEnviarPedido($payload));
  }

  private function leerPayloadJson() {
    $payload = json_decode(file_get_contents('php://input'), true);
    return is_array($payload) ? $payload : array();
  }

  private function validarItemsCotizacion($payload, $mensajeVacio = 'Agrega al menos un producto antes de revisar la solicitud.') {
    if (!isset($payload['items']) || !is_array($payload['items']) || count($payload['items']) === 0) {
      return $mensajeVacio;
    }

    foreach ($payload['items'] as $item) {
      if (!is_array($item)) {
        return 'Revisa los productos de la solicitud.';
      }

      $idSku = isset($item['id_sku']) ? intval($item['id_sku']) : 0;
      $cantidad = isset($item['cantidad']) && is_numeric($item['cantidad']) ? (float) $item['cantidad'] : 0;

      if ($idSku <= 0) {
        return 'Uno de los productos no es válido.';
      }

      if ($cantidad < 1) {
        return 'Revisa las cantidades. Cada producto debe tener cantidad mínima de 1.';
      }
    }

    return true;
  }
}