<?php

class Inicio extends Controlador {
  public function index() {
    if (!SesionCliente::autenticado()) {
      $this->renderPublico('home_publica', array(
        'titulo' => 'Portal de Distribucion Artiani'
      ));
      return;
    }

    $this->requerirCliente();
    $this->refrescarSesionCliente();
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Resumen',
      'cliente' => SesionCliente::perfil(),
      'pedidos' => $api->pedidoListar(array('limite' => 5)),
      'cotizaciones' => $api->cotizacionListar(array('limite' => 5)),
      'notificaciones' => $api->notificacionListar(array('limite' => 5)),
      'notificaciones_resumen' => $api->notificacionResumen()
    );
    $this->render('inicio', $datos);
  }
}
