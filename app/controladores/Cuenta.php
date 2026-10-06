<?php

class Cuenta extends Controlador {
  public function index() {
    $this->requerirCliente();
    $this->refrescarSesionCliente();
    $datos = array(
      'titulo' => 'Mi cuenta',
      'cliente' => SesionCliente::perfil()
    );

    if (!SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver') && !SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar')) {
      $this->renderAuth('estado_acceso', $datos);
      return;
    }

    $this->render('cuenta', $datos);
  }

  public function guardar() {
    $this->requerirCliente();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ' . URL_RAIZ . '/cuenta');
      exit;
    }

    $api = $this->modelo('DistribucionApiCliente');
    $respuesta = $api->perfilSolicitarCambio($this->normalizarCambioPerfil($_POST));
    $this->refrescarSesionCliente();
    $datos = array(
      'titulo' => 'Mi cuenta',
      'cliente' => SesionCliente::perfil(),
      'respuesta' => $respuesta
    );
    $this->render('cuenta', $datos);
  }

  private function normalizarCambioPerfil($datos) {
    $datos = is_array($datos) ? $datos : array();
    $payload = array(
      'telefono' => $this->texto($datos, 'telefono'),
      'whatsapp' => $this->texto($datos, 'whatsapp'),
      'correo_alterno' => strtolower($this->texto($datos, 'correo_alterno')),
      'contacto_principal' => $this->texto($datos, 'contacto_principal'),
      'datos_comerciales' => array(),
      'entrega' => array(),
      'facturacion' => array()
    );

    $comercial = isset($datos['datos_comerciales']) && is_array($datos['datos_comerciales']) ? $datos['datos_comerciales'] : array();
    foreach (array('empresa', 'nombre_negocio', 'tipo_negocio', 'ciudad', 'estado') as $campo) {
      $payload['datos_comerciales'][$campo] = $this->texto($comercial, $campo);
    }

    $entrega = isset($datos['entrega']) && is_array($datos['entrega']) ? $datos['entrega'] : array();
    foreach (array('calle', 'numero_exterior', 'numero_interior', 'colonia', 'ciudad', 'estado', 'codigo_postal', 'referencias') as $campo) {
      $payload['entrega'][$campo] = $this->texto($entrega, $campo);
    }

    $facturacion = isset($datos['facturacion']) && is_array($datos['facturacion']) ? $datos['facturacion'] : array();
    $payload['facturacion'] = array(
      'requiere_factura' => !empty($facturacion['requiere_factura']) ? 1 : 0,
      'rfc' => strtoupper($this->texto($facturacion, 'rfc')),
      'razon_social' => $this->texto($facturacion, 'razon_social'),
      'regimen_fiscal' => $this->texto($facturacion, 'regimen_fiscal'),
      'uso_cfdi' => strtoupper($this->texto($facturacion, 'uso_cfdi')),
      'codigo_postal_fiscal' => preg_replace('/\D+/', '', $this->texto($facturacion, 'codigo_postal_fiscal')),
      'correo_facturacion' => strtolower($this->texto($facturacion, 'correo_facturacion')),
      'comentarios_facturacion' => $this->texto($facturacion, 'comentarios_facturacion')
    );

    return $payload;
  }

  private function texto($datos, $clave) {
    return isset($datos[$clave]) ? trim((string) $datos[$clave]) : '';
  }
}
