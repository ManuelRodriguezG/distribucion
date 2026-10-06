<?php

class MiCatalogo extends Controlador {
  public function index() {
    $this->requerirAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar', 'Mi catalogo aun no esta habilitado para tu perfil comercial.');
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
    $this->requerirAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar', 'Mi catalogo aun no esta habilitado para tu perfil comercial.');
    $payload = json_decode(file_get_contents('php://input'), true);
    $api = $this->modelo('DistribucionApiCliente');
    return $this->json($api->miCatalogoGuardar(is_array($payload) ? $payload : array()));
  }
}