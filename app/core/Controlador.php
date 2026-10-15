<?php

class Controlador {
  public function modelo($modelo) {
    require_once RUTA_APP . '/modelos/' . $modelo . '.php';
    return new $modelo();
  }

  public function vista($vista, $datos = array()) {
    $archivo = RUTA_APP . '/vistas/paginas/' . $vista . '.php';
    if (!file_exists($archivo)) {
      http_response_code(404);
      die('La vista no existe');
    }

    require $archivo;
  }

  protected function render($vista, $datos = array()) {
    $contenido = RUTA_APP . '/vistas/paginas/' . $vista . '.php';
    if (!file_exists($contenido)) {
      http_response_code(404);
      die('La vista no existe');
    }

    require RUTA_APP . '/vistas/layouts/base.php';
  }

  protected function renderAuth($vista, $datos = array()) {
    $contenido = RUTA_APP . '/vistas/paginas/' . $vista . '.php';
    if (!file_exists($contenido)) {
      http_response_code(404);
      die('La vista no existe');
    }

    require RUTA_APP . '/vistas/layouts/auth.php';
  }

  protected function renderPublico($vista, $datos = array()) {
    $contenido = RUTA_APP . '/vistas/paginas/' . $vista . '.php';
    if (!file_exists($contenido)) {
      http_response_code(404);
      die('La vista no existe');
    }

    require RUTA_APP . '/vistas/layouts/publico.php';
  }

  protected function requerirCliente() {
    if (!SesionCliente::autenticado()) {
      $_SESSION['distribucion_destino_post_login'] = $this->rutaSolicitadaActual();
      header('Location: ' . URL_RAIZ . '/auth/login?motivo=ruta_protegida');
      exit;
    }
  }

  protected function requerirPermiso($permiso, $mensaje = 'Tu acceso aun no tiene habilitado este modulo.') {
    $this->requerirCliente();
    if (!SesionCliente::tienePermiso($permiso)) {
      $this->refrescarSesionCliente();
    }
    if (!SesionCliente::tienePermiso($permiso)) {
      $this->denegarAcceso($permiso, $mensaje);
    }
  }

  protected function requerirAccion($accion, $permiso = '', $mensaje = 'Tu acceso aun no tiene habilitado este modulo.') {
    $this->requerirCliente();
    if (!SesionCliente::tieneAccion($accion, $permiso)) {
      $this->refrescarSesionCliente();
    }
    if (!SesionCliente::tieneAccion($accion, $permiso)) {
      $this->denegarAcceso($permiso, $mensaje);
    }
  }

  protected function refrescarSesionCliente() {
    if (!SesionCliente::autenticado()) {
      return;
    }
    $api = $this->modelo('DistribucionApiCliente');
    $respuesta = $api->perfil();
    $perfil = !empty($respuesta['perfil']) && is_array($respuesta['perfil']) ? $respuesta['perfil'] : array();
    if (!empty($respuesta['permisos']) && is_array($respuesta['permisos'])) {
      $perfil['permisos'] = $respuesta['permisos'];
    }
    if (!empty($respuesta['acciones']) && is_array($respuesta['acciones'])) {
      $perfil['acciones'] = $respuesta['acciones'];
    }
    if (!empty($perfil)) {
      SesionCliente::actualizarPerfil($perfil);
    }
  }

  private function denegarAcceso($permiso, $mensaje) {
    http_response_code(403);
    $this->render('acceso_denegado', array(
      'titulo' => 'Acceso en revision',
      'cliente' => SesionCliente::perfil(),
      'permiso' => $permiso,
      'mensaje' => $mensaje
    ));
    exit;
  }

  protected function json($datos, $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    return json_encode($datos);
  }

  private function rutaSolicitadaActual() {
    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '/';
    $partes = parse_url($uri);
    $path = isset($partes['path']) ? $partes['path'] : '/';
    $query = isset($partes['query']) && $partes['query'] !== '' ? '?' . $partes['query'] : '';
    if ($path === '' || $path[0] !== '/' || stripos($path, '/auth/') === 0) {
      return '/';
    }
    return strtolower($path) . $query;
  }
}
