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

  /**
   * IA: Codex GPT-5 | Fecha: 2026-10-14
   * Proposito: exponer diagnostico protegido para comparar sesion frontend contra respuestas reales de la API ERP.
   * Impacto: Distribucion; ayuda a detectar si faltan permisos, lista, asociacion de cliente o datos productivos.
   * Contrato: GET autenticado, JSON; no muestra token ni escribe datos.
   */
  public function diagnostico() {
    $this->requerirCliente();
    $this->refrescarSesionCliente();

    $api = $this->modelo('DistribucionApiCliente');
    $perfilSesion = SesionCliente::perfil();
    $perfilApi = $api->perfil();
    $catalogo = $api->catalogo(array('limite' => 5));
    $miCatalogo = $api->miCatalogoListar(array('limite' => 5));
    $cotizaciones = $api->cotizacionListar(array('limite' => 5));
    $pedidos = $api->pedidoListar(array('limite' => 5));
    $notificaciones = $api->notificacionListar(array('limite' => 5));

    return $this->json(array(
      'error' => false,
      'mensaje' => 'Diagnostico de cuenta Distribucion',
      'ambiente' => array(
        'url_raiz' => URL_RAIZ,
        'erp_url_raiz' => defined('ERP_URL_RAIZ') ? ERP_URL_RAIZ : null,
        'erp_api_base_url' => ERP_API_BASE_URL,
        'host' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null
      ),
      'sesion_frontend' => array(
        'autenticado' => SesionCliente::autenticado(),
        'perfil' => $this->sanitizarPerfilDiagnostico($perfilSesion),
        'permisos' => SesionCliente::permisos(),
        'acciones' => SesionCliente::acciones()
      ),
      'perfil_api' => $this->sanitizarRespuestaDiagnostico($perfilApi),
      'checks' => array(
        'puede_ver_catalogo' => SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver'),
        'puede_cotizar' => SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar'),
        'puede_mi_catalogo' => SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar'),
        'tiene_lista_precio' => SesionCliente::tieneListaPrecio()
      ),
      'resumen_endpoints' => array(
        'catalogo' => $this->resumenRespuestaLista($catalogo),
        'mi_catalogo' => $this->resumenRespuestaLista($miCatalogo),
        'cotizaciones' => $this->resumenRespuestaLista($cotizaciones),
        'pedidos' => $this->resumenRespuestaLista($pedidos),
        'notificaciones' => $this->resumenRespuestaLista($notificaciones)
      )
    ));
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

  private function resumenRespuestaLista($respuesta) {
    $respuesta = is_array($respuesta) ? $respuesta : array('error' => true, 'mensaje' => 'Respuesta invalida');
    $items = array();
    if (isset($respuesta['items']) && is_array($respuesta['items'])) {
      $items = $respuesta['items'];
    } elseif (isset($respuesta['registros']) && is_array($respuesta['registros'])) {
      $items = $respuesta['registros'];
    } elseif (isset($respuesta['cotizaciones']) && is_array($respuesta['cotizaciones'])) {
      $items = $respuesta['cotizaciones'];
    } elseif (isset($respuesta['pedidos']) && is_array($respuesta['pedidos'])) {
      $items = $respuesta['pedidos'];
    } elseif (isset($respuesta['notificaciones']) && is_array($respuesta['notificaciones'])) {
      $items = $respuesta['notificaciones'];
    }

    return array(
      'error' => !empty($respuesta['error']),
      'mensaje' => isset($respuesta['mensaje']) ? $respuesta['mensaje'] : '',
      'codigo' => isset($respuesta['codigo']) ? $respuesta['codigo'] : null,
      'total_items_detectados' => count($items),
      'total_reportado' => isset($respuesta['total']) ? $respuesta['total'] : (isset($respuesta['paginacion']['total']) ? $respuesta['paginacion']['total'] : null),
      'primer_item_claves' => !empty($items[0]) && is_array($items[0]) ? array_keys($items[0]) : array(),
      'requiere_permiso' => isset($respuesta['requiere_permiso']) ? $respuesta['requiere_permiso'] : null,
      'requiere_autenticacion' => isset($respuesta['requiere_autenticacion']) ? $respuesta['requiere_autenticacion'] : null
    );
  }

  private function sanitizarRespuestaDiagnostico($respuesta) {
    $respuesta = is_array($respuesta) ? $respuesta : array('error' => true);
    unset($respuesta['token'], $respuesta['access_token'], $respuesta['refresh_token']);
    if (isset($respuesta['depurar']['token'])) { unset($respuesta['depurar']['token']); }
    if (isset($respuesta['depurar']['access_token'])) { unset($respuesta['depurar']['access_token']); }
    if (isset($respuesta['depurar']['refresh_token'])) { unset($respuesta['depurar']['refresh_token']); }
    return $respuesta;
  }

  private function sanitizarPerfilDiagnostico($perfil) {
    $perfil = is_array($perfil) ? $perfil : array();
    unset($perfil['token'], $perfil['access_token'], $perfil['refresh_token']);
    return $perfil;
  }
}
