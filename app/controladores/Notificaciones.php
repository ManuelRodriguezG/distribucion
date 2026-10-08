<?php

class Notificaciones extends Controlador {
  public function index() {
    $this->requerirCliente();
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Notificaciones',
      'cliente' => SesionCliente::perfil(),
      'notificaciones' => $api->notificacionListar($_GET)
    );
    $this->render('notificaciones', $datos);
  }

  public function resumen() {
    $this->requerirCliente();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->notificacionResumen());
  }

  public function listar() {
    $this->requerirCliente();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->notificacionListar($_GET));
  }

  public function marcar_leida() {
    $this->requerirCliente();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = $this->leerPayloadJson();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->notificacionMarcarLeida($payload));
  }

  public function enviar() {
    $this->requerirCliente();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = $this->leerPayloadJson();
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->notificacionEnviar($payload));
  }

  private function leerPayloadJson() {
    $payload = json_decode(file_get_contents('php://input'), true);
    return is_array($payload) ? $payload : array();
  }
}
