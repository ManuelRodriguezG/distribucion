<?php

class Catalogo extends Controlador {
  public function index() {
    $this->requerirAccion('ver_catalogo', 'distribucion.catalogo.ver', 'Tu acceso ya existe, pero el catalogo aun no esta habilitado para tu perfil.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Catalogo',
      'catalogo' => $api->catalogo($_GET),
      'mi_catalogo' => SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar') ? $api->miCatalogoListar(array('limite' => 300)) : array('items' => array()),
      'categorias' => $api->categorias(),
      'cliente' => SesionCliente::perfil()
    );
    $this->render('catalogo', $datos);
  }

  public function producto($slug = '') {
    $this->requerirAccion('ver_detalle', 'distribucion.catalogo.ver', 'El detalle de producto aun no esta habilitado para tu perfil.');
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Producto',
      'producto' => $api->producto($slug),
      'mi_catalogo' => SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar') ? $api->miCatalogoListar(array('limite' => 300)) : array('items' => array()),
      'cliente' => SesionCliente::perfil()
    );
    $this->render('producto', $datos);
  }
}

