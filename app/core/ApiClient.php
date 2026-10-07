<?php

class ApiClient {
  private $baseUrl;
  private $timeout;

  public function __construct($baseUrl = ERP_API_BASE_URL, $timeout = ERP_API_TIMEOUT) {
    $this->baseUrl = rtrim($baseUrl, '/');
    $this->timeout = $timeout;
  }

  public function get($endpoint, $params = array(), $token = null) {
    $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
    if (!empty($params)) {
      $url .= '?' . http_build_query($params);
    }

    return $this->request('GET', $url, null, $token);
  }

  public function post($endpoint, $payload = array(), $token = null) {
    $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
    return $this->request('POST', $url, $payload, $token);
  }

  private function request($method, $url, $payload = null, $token = null) {
    $headers = array(
      'Accept: application/json',
      'X-Canal-Comercial: ' . ERP_API_CANAL
    );

    $body = null;
    if ($payload !== null) {
      $headers[] = 'Content-Type: application/json';
      $body = json_encode($payload);
    }

    if ($token) {
      $headers[] = 'Authorization: Bearer ' . $token;
    }

    $context = stream_context_create(array(
      'http' => array(
        'method' => $method,
        'header' => implode("\r\n", $headers),
        'content' => $body,
        'timeout' => $this->timeout,
        'ignore_errors' => true
      )
    ));

    $respuesta = @file_get_contents($url, false, $context);
    if ($respuesta === false) {
      return array(
        'error' => true,
        'mensaje' => 'No pudimos conectar con el servicio comercial. Intenta nuevamente.',
        'depurar' => array()
      );
    }

    $json = json_decode($respuesta, true);
    if (!is_array($json)) {
      return array(
        'error' => true,
        'mensaje' => 'No pudimos conectar con el servicio comercial. Intenta nuevamente.',
        'depurar' => array()
      );
    }

    return $json;
  }
}

