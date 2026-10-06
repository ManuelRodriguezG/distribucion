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
    $datos = array(
      'titulo' => 'Resumen',
      'cliente' => SesionCliente::perfil()
    );
    $this->render('inicio', $datos);
  }
}
