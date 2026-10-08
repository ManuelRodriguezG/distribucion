<?php

class DistribucionApiCliente {
  private $api;

  public function __construct() {
    $this->api = new ApiClient();
  }

  public function configuracionInicial() {
    return $this->normalizar($this->api->get('configuracion_inicial', array(), SesionCliente::token()));
  }

  public function contratos() {
    return $this->normalizar($this->api->get('contratos'));
  }

  public function catalogo($filtros = array()) {
    return $this->normalizar($this->api->get('catalogo', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function catalogoIndice($filtros = array()) {
    $base = $this->limpiarFiltros($filtros);
    unset($base['q'], $base['pagina']);
    $base['limite'] = 300;
    $items = array();
    $vistos = array();
    $total = 0;
    $limiteIndice = 300;
    for ($pagina = 1; $pagina <= 100; $pagina++) {
      $base['pagina'] = $pagina;
      $respuesta = $this->catalogo($base);
      if (!empty($respuesta['error'])) {
        return array('error' => true, 'mensaje' => isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'No se pudo cargar indice de catalogo', 'items' => $items, 'total' => $total);
      }
      $paginaItems = isset($respuesta['items']) && is_array($respuesta['items']) ? $respuesta['items'] : array();
      foreach ($paginaItems as $item) {
        $id = isset($item['id_sku']) ? (string) $item['id_sku'] : md5(json_encode($item));
        if (isset($vistos[$id])) { continue; }
        $vistos[$id] = true;
        $items[] = $item;
      }
      $paginacion = isset($respuesta['paginacion']) && is_array($respuesta['paginacion']) ? $respuesta['paginacion'] : array();
      $limiteReal = max(1, intval(isset($paginacion['limite']) ? $paginacion['limite'] : $limiteIndice));
      $total = max($total, intval(isset($paginacion['total']) ? $paginacion['total'] : count($items)));
      if ($total > 0 && count($items) >= $total) { break; }
      if (count($paginaItems) < $limiteReal) { break; }
    }
    return array('error' => false, 'items' => $items, 'total' => $total, 'cargados' => count($items));
  }

  public function producto($slug) {
    return $this->normalizar($this->api->get('producto/' . rawurlencode($slug), array(), SesionCliente::token()));
  }

  public function categorias($filtros = array()) {
    return $this->normalizar($this->api->get('categorias', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function categoriasInteres($filtros = array()) {
    return $this->normalizar($this->api->get('categorias_interes', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }
  public function marcas($filtros = array()) {
    return $this->normalizar($this->api->get('marcas', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function filtros($filtros = array()) {
    return $this->normalizar($this->api->get('filtros', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function registrar($datos) {
    return $this->normalizar($this->api->post('auth/registro', $datos));
  }

  public function login($datos) {
    return $this->normalizar($this->api->post('auth/login', $datos));
  }

  public function perfil() {
    return $this->normalizar($this->api->get('auth/perfil', array(), SesionCliente::token()));
  }

  public function perfilSolicitarCambio($datos) {
    return $this->normalizar($this->api->post('auth/perfil/solicitar_cambio', $datos, SesionCliente::token()));
  }

  public function activarConsultar($token) {
    return $this->normalizar($this->api->post('auth/activar_consultar', array('token' => $token)));
  }

  public function activarContrasenia($token, $contrasenia) {
    return $this->normalizar($this->api->post('auth/activar_contrasenia', array(
      'token' => $token,
      'contrasenia' => $contrasenia
    )));
  }

  public function cotizacionDryRun($datos) {
    return $this->normalizar($this->api->post('cotizacion/dryrun', $datos, SesionCliente::token()));
  }

  public function resolverPrecios($datos) {
    return $this->normalizar($this->api->post('precios/resolver', $datos, SesionCliente::token()));
  }

  public function resolverDisponibilidad($datos) {
    return $this->normalizar($this->api->post('disponibilidad/resolver', $datos, SesionCliente::token()));
  }

  public function cotizacionRegistrar($datos) {
    return $this->normalizar($this->api->post('cotizacion/registrar', $datos, SesionCliente::token()));
  }


  public function cotizacionListar($filtros = array()) {
    return $this->normalizar($this->api->get('cotizacion/listar', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function cotizacionDetalle($filtros = array()) {
    return $this->normalizar($this->api->get('cotizacion/detalle', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function cotizacionGuardarBorrador($datos) {
    return $this->normalizar($this->api->post('cotizacion/guardar_borrador', $datos, SesionCliente::token()));
  }

  public function cotizacionCancelar($datos) {
    return $this->normalizar($this->api->post('cotizacion/cancelar', $datos, SesionCliente::token()));
  }

  public function cotizacionDuplicar($datos) {
    return $this->normalizar($this->api->post('cotizacion/duplicar', $datos, SesionCliente::token()));
  }

  public function cotizacionEnviarPedido($datos) {
    return $this->normalizar($this->api->post('cotizacion/enviar_pedido', $datos, SesionCliente::token()));
  }
  public function pedidoRegistrar($datos) {
    return $this->normalizar($this->api->post('pedido/registrar', $datos, SesionCliente::token()));
  }

  public function pedidoListar($filtros = array()) {
    return $this->normalizar($this->api->get('pedido/listar', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function pedidoDetalle($filtros = array()) {
    return $this->normalizar($this->api->get('pedido/detalle', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function pedidoResponder($datos) {
    return $this->normalizar($this->api->post('pedido/responder', $datos, SesionCliente::token()));
  }

  public function notificacionResumen() {
    return $this->normalizar($this->api->get('notificacion/resumen', array(), SesionCliente::token()));
  }

  public function notificacionListar($filtros = array()) {
    return $this->normalizar($this->api->get('notificacion/listar', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function notificacionMarcarLeida($datos) {
    return $this->normalizar($this->api->post('notificacion/marcar_leida', $datos, SesionCliente::token()));
  }

  public function notificacionEnviar($datos) {
    return $this->normalizar($this->api->post('notificacion/enviar', $datos, SesionCliente::token()));
  }

  public function miCatalogoListar($filtros = array()) {
    return $this->normalizar($this->api->get('mi_catalogo/listar', $this->limpiarFiltros($filtros), SesionCliente::token()));
  }

  public function miCatalogoGuardar($datos) {
    return $this->normalizar($this->api->post('mi_catalogo/guardar', $datos, SesionCliente::token()));
  }
  private function normalizar($respuesta) {
    if (!is_array($respuesta)) {
      return array('error' => true, 'mensaje' => 'Respuesta invalida del ERP', 'depurar' => array());
    }
    $depurar = isset($respuesta['depurar']) && is_array($respuesta['depurar']) ? $respuesta['depurar'] : array();
    foreach ($depurar as $clave => $valor) {
      if (!array_key_exists($clave, $respuesta)) {
        $respuesta[$clave] = $valor;
      }
    }
    return $respuesta;
  }

  private function limpiarFiltros($filtros) {
    $filtros = is_array($filtros) ? $filtros : array();
    unset($filtros['url']);
    return $filtros;
  }
}













