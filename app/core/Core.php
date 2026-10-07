<?php

class Core {
  protected $controladorActual = 'Inicio';
  protected $metodoActual = 'index';
  protected $parametros = array();

  public function __construct() {
    $url = $this->getUrl();
    $this->redirigirRutaCanonica($url);

    if (!empty($url[0])) {
      $controlador = $this->resolverControlador($url[0]);
      if ($controlador !== null) {
        $this->controladorActual = $controlador;
        unset($url[0]);
      } else {
        $this->responder404();
        return;
      }
    }

    require_once RUTA_APP . '/controladores/' . $this->controladorActual . '.php';
    $this->controladorActual = new $this->controladorActual;

    if (!empty($url[1])) {
      if (method_exists($this->controladorActual, $url[1])) {
        $this->metodoActual = $url[1];
        unset($url[1]);
      } else {
        $this->responder404();
        return;
      }
    }

    if (!method_exists($this->controladorActual, $this->metodoActual)) {
      $this->responder404();
      return;
    }

    $this->parametros = $url ? array_values($url) : array();
    $resultado = call_user_func_array(array($this->controladorActual, $this->metodoActual), $this->parametros);
    if ($resultado !== null) {
      echo $resultado;
    }
  }

  private function responder404() {
    http_response_code(404);
    echo 'Página no encontrada';
  }

  public function getUrl() {
    if (!isset($_GET['url'])) {
      return array();
    }

    $url = rtrim($_GET['url'], '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    return explode('/', $url);
  }

  private function redirigirRutaCanonica($url) {
    if (empty($url)) { return; }
    $canonica = $url;
    if (isset($canonica[0])) { $canonica[0] = strtolower($canonica[0]); }
    if (isset($canonica[1])) { $canonica[1] = strtolower($canonica[1]); }
    if ($canonica === $url) { return; }

    $ruta = implode('/', array_map('rawurlencode', $canonica));
    $query = $_GET;
    unset($query['url']);
    $destino = URL_RAIZ . '/' . $ruta;
    if (!empty($query)) {
      $destino .= '?' . http_build_query($query);
    }
    header('Location: ' . $destino, true, 301);
    exit;
  }

  private function resolverControlador($segmento) {
    $segmento = is_string($segmento) ? trim($segmento) : '';
    if ($segmento === '') {
      return null;
    }

    $directorio = RUTA_APP . '/controladores/';
    $candidato = str_replace(' ', '', ucwords(str_replace('-', ' ', $segmento)));
    if (file_exists($directorio . $candidato . '.php')) {
      return $candidato;
    }

    foreach (glob($directorio . '*.php') as $archivo) {
      $nombre = pathinfo($archivo, PATHINFO_FILENAME);
      if (strtolower($nombre) === strtolower($segmento)) {
        return $nombre;
      }
    }

    return null;
  }
}
