<?php

class ActivarCuenta extends Controlador {
  public function index() {
    $token = $this->tokenDesdeUrl();
    $api = $this->modelo('DistribucionApiCliente');
    $datos = array(
      'titulo' => 'Activar cuenta',
      'token' => $token,
      'estado' => 'invalido',
      'consulta' => null,
      'respuesta' => null
    );

    if ($token === '') {
      $this->renderAuth('activar_cuenta', $datos);
      return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $contrasenia = isset($_POST['contrasenia']) ? (string) $_POST['contrasenia'] : '';
      $confirmar = isset($_POST['confirmar_contrasenia']) ? (string) $_POST['confirmar_contrasenia'] : '';
      $error = $this->validarContrasenia($contrasenia, $confirmar);
      if ($error !== null) {
        $datos['estado'] = 'formulario';
        $datos['respuesta'] = array('error' => true, 'tipo' => 'warning', 'mensaje' => $error);
        $this->renderAuth('activar_cuenta', $datos);
        return;
      }

      $respuesta = $api->activarContrasenia($token, $contrasenia);
      $datos['respuesta'] = $respuesta;
      $datos['estado'] = empty($respuesta['error']) && !empty($respuesta['activado']) ? 'activado' : 'formulario';
      $this->renderAuth('activar_cuenta', $datos);
      return;
    }

    $consulta = $api->activarConsultar($token);
    $datos['consulta'] = $consulta;
    $datos['estado'] = empty($consulta['error']) && !empty($consulta['valido']) ? 'formulario' : 'invalido';
    $this->renderAuth('activar_cuenta', $datos);
  }

  private function tokenDesdeUrl() {
    return isset($_GET['token']) ? trim((string) $_GET['token']) : '';
  }

  private function validarContrasenia($contrasenia, $confirmar) {
    if (strlen($contrasenia) < 8) {
      return 'La contrasena debe tener minimo 8 caracteres.';
    }
    if ($contrasenia !== $confirmar) {
      return 'La confirmacion no coincide con la nueva contrasena.';
    }
    return null;
  }
}
