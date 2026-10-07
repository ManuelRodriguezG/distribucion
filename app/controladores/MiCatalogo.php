<?php

class MiCatalogo extends Controlador {
  public function index() {
    $this->requerirAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar', 'Mi catálogo aún no está habilitado para tu perfil comercial.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Mi catálogo',
      'mi_catalogo' => $api->miCatalogoListar($_GET),
      'categorias' => $api->categorias(),
      'marcas' => $api->marcas(),
      'cliente' => SesionCliente::perfil()
    );
    $this->render('mi_catalogo', $datos);
  }

  public function guardar() {
    $this->requerirAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar', 'Mi catálogo aún no está habilitado para tu perfil comercial.');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      return $this->json(array('error' => true, 'mensaje' => 'Usa POST'), 405);
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->miCatalogoGuardar(is_array($payload) ? $payload : array()));
  }
}