<?php

class Auth extends Controlador {
  public function login() {
    if (SesionCliente::autenticado()) {
      $this->refrescarSesionCliente();
      header('Location: ' . $this->destinoPosteriorLogin());
      exit;
    }

    $contexto = $this->contextoLogin();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $api = $this->modelo('DistribucionApiCliente');
      $respuesta = $this->normalizarRespuestaLogin($api->login($_POST));
      if (empty($respuesta['error']) && !empty($respuesta['token'])) {
        $perfil = isset($respuesta['perfil']) && is_array($respuesta['perfil']) ? $respuesta['perfil'] : array();
        if (isset($respuesta['permisos']) && is_array($respuesta['permisos'])) {
          $perfil['permisos'] = $respuesta['permisos'];
        }
        if (isset($respuesta['acciones']) && is_array($respuesta['acciones'])) {
          $perfil['acciones'] = $respuesta['acciones'];
        }
        SesionCliente::iniciar($perfil, $respuesta['token']);
        $this->refrescarSesionCliente();
        header('Location: ' . $this->destinoPosteriorLogin());
        exit;
      }
      $this->renderAuth('login', array('titulo' => 'Iniciar sesion', 'respuesta' => $respuesta, 'contexto' => $contexto));
      return;
    }

    $this->renderAuth('login', array('titulo' => 'Iniciar sesion', 'contexto' => $contexto));
  }

  public function registro() {
    if (SesionCliente::autenticado()) {
      $this->refrescarSesionCliente();
      header('Location: ' . $this->destinoPosteriorLogin());
      exit;
    }

    $api = $this->modelo('DistribucionApiCliente');
    $categorias = $api->categoriasInteres();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $solicitud = $this->normalizarSolicitudRegistro($_POST);
      $respuesta = $this->normalizarRespuestaRegistro($api->registrar($solicitud));
      $this->renderAuth('registro', array('titulo' => 'Solicitud de acceso', 'respuesta' => $respuesta, 'categorias' => $categorias, 'solicitud' => $solicitud));
      return;
    }

    $this->renderAuth('registro', array('titulo' => 'Solicitud de acceso', 'categorias' => $categorias));
  }

  public function salir() {
    SesionCliente::cerrar();
    header('Location: ' . URL_RAIZ . '/auth/login');
    exit;
  }

  private function normalizarSolicitudRegistro($datos) {
    $datos = is_array($datos) ? $datos : array();
    $campos = array(
      'nombre',
      'nombre_negocio',
      'empresa',
      'correo',
      'telefono',
      'whatsapp',
      'rfc',
      'ciudad',
      'estado',
      'tipo_interes',
      'tipo_negocio',
      'calle',
      'numero_exterior',
      'numero_interior',
      'colonia',
      'codigo_postal',
      'referencias',
      'razon_social',
      'regimen_fiscal',
      'uso_cfdi',
      'codigo_postal_fiscal',
      'correo_facturacion',
      'comentarios_facturacion',
      'intereses_comerciales',
      'mensaje'
    );

    $payload = array();
    foreach ($campos as $campo) {
      $payload[$campo] = isset($datos[$campo]) ? trim((string) $datos[$campo]) : '';
    }

    $payload['tipo_interes'] = 'mayorista';
    $payload['empresa'] = $payload['nombre_negocio'];
    $payload['correo'] = strtolower($payload['correo']);
    $payload['rfc'] = strtoupper($payload['rfc']);
    $payload['requiere_factura'] = !empty($datos['requiere_factura']) ? 1 : 0;
    $payload['facturacion'] = array(
      'requiere_factura' => $payload['requiere_factura'],
      'rfc' => $payload['rfc'],
      'razon_social' => $payload['razon_social'],
      'regimen_fiscal' => $payload['regimen_fiscal'],
      'uso_cfdi' => strtoupper($payload['uso_cfdi']),
      'codigo_postal_fiscal' => preg_replace('/\D+/', '', $payload['codigo_postal_fiscal']),
      'correo_facturacion' => strtolower($payload['correo_facturacion']),
      'comentarios_facturacion' => $payload['comentarios_facturacion']
    );
    $payload['categorias_interes'] = $this->normalizarCategoriasInteres(isset($datos['categorias_interes']) ? $datos['categorias_interes'] : array());

    return $payload;
  }

  private function normalizarRespuestaLogin($respuesta) {
    $respuesta = is_array($respuesta) ? $respuesta : array('error' => true);
    if (empty($respuesta['error'])) {
      return $respuesta;
    }

    $codigo = isset($respuesta['codigo']) ? trim((string) $respuesta['codigo']) : '';
    $estatus = isset($respuesta['estatus']) ? trim((string) $respuesta['estatus']) : '';
    $mensajes = array(
      'cuenta_pendiente' => 'Tu solicitud fue recibida y sigue pendiente de revision comercial.',
      'cuenta_en_revision' => 'Tu solicitud sigue en revision. Te avisaremos cuando el acceso este habilitado.',
      'cuenta_rechazada' => 'Tu solicitud no fue aprobada. Contacta a Artiani para revisar alternativas.',
      'cuenta_suspendida' => 'Tu acceso esta suspendido. Contacta a Artiani para revisar tu cuenta.',
      'requiere_activacion' => 'Necesitas activar tu acceso o crear contrasena con el enlace enviado.',
      'contrasenia_no_creada' => 'Necesitas activar tu acceso o crear contrasena con el enlace enviado.',
      'cliente_existente' => 'Este correo ya tiene cuenta. Inicia sesion o solicita ayuda si no recuerdas tu acceso.',
      'sin_permisos_comerciales' => 'Tu acceso comercial aun esta en revision.',
      'credenciales_invalidas' => 'No pudimos iniciar sesion. Revisa correo y contrasena.'
    );

    if ($codigo !== '' && isset($mensajes[$codigo])) {
      $respuesta['mensaje'] = $mensajes[$codigo];
    } elseif ($estatus !== '') {
      $respuesta['mensaje'] = $this->mensajePorEstatus($estatus);
    } elseif (empty($respuesta['mensaje']) || strtolower((string) $respuesta['mensaje']) === 'credenciales invalidas') {
      $respuesta['mensaje'] = $mensajes['credenciales_invalidas'];
    }

    return $respuesta;
  }

  private function mensajePorEstatus($estatus) {
    $estatus = strtolower(trim((string) $estatus));
    if ($estatus === 'pendiente' || $estatus === 'en_revision') {
      return 'Tu solicitud sigue en revision. Te avisaremos cuando el acceso este habilitado.';
    }
    if ($estatus === 'rechazado') {
      return 'Tu solicitud no fue aprobada. Contacta a Artiani para revisar alternativas.';
    }
    if ($estatus === 'suspendido') {
      return 'Tu acceso esta suspendido. Contacta a Artiani para revisar tu cuenta.';
    }
    return 'No pudimos iniciar sesion. Revisa correo y contrasena.';
  }

  private function normalizarRespuestaRegistro($respuesta) {
    $respuesta = is_array($respuesta) ? $respuesta : array('error' => true);
    $codigo = isset($respuesta['codigo']) ? trim((string) $respuesta['codigo']) : '';
    $mensajes = array(
      'solicitud_existente' => 'Ya tenemos una solicitud asociada a este correo.',
      'cliente_existente' => 'Este correo ya tiene cuenta. Inicia sesion o solicita ayuda si no recuerdas tu acceso.',
      'solicitud_rechazada' => 'La solicitud asociada a este correo no fue aprobada. Contacta a Artiani para revisar alternativas.',
      'cuenta_suspendida' => 'El acceso asociado a este correo esta suspendido. Contacta a Artiani para revisar tu cuenta.',
      'cuenta_en_revision' => 'Tu solicitud sigue en revision. Te contactaremos cuando el acceso este habilitado.',
      'requiere_activacion' => 'Necesitas activar tu acceso o crear contrasena con el enlace enviado.',
      'campos_fiscales_requeridos' => 'Completa los datos fiscales requeridos para guardar tu preferencia de facturacion.',
      'campos_requeridos' => 'Completa los campos requeridos para enviar tu solicitud.'
    );
    if ($codigo !== '' && isset($mensajes[$codigo])) {
      $respuesta['mensaje'] = $mensajes[$codigo];
    }
    return $respuesta;
  }

  private function contextoLogin() {
    $contexto = array();
    if (!empty($_GET['motivo']) && $_GET['motivo'] === 'ruta_protegida') {
      $contexto['mensaje'] = 'Inicia sesion para continuar. El catalogo, cotizaciones y cuenta requieren acceso comercial autorizado o una solicitud en revision.';
    }
    if (!empty($_SESSION['distribucion_destino_post_login'])) {
      $contexto['ruta'] = $_SESSION['distribucion_destino_post_login'];
    }
    return $contexto;
  }

  private function normalizarCategoriasInteres($categorias) {
    if (!is_array($categorias)) {
      return array();
    }
    $salida = array();
    foreach ($categorias as $categoria) {
      if (is_string($categoria)) {
        $decodificada = json_decode($categoria, true);
        if (is_array($decodificada)) {
          $categoria = $decodificada;
        } else {
          $categoria = array('nombre' => trim($categoria));
        }
      }
      if (!is_array($categoria)) { continue; }
      $id = isset($categoria['id_categoria_erp']) ? intval($categoria['id_categoria_erp']) : 0;
      $nombre = isset($categoria['nombre']) ? trim((string) $categoria['nombre']) : '';
      $ruta = isset($categoria['ruta']) ? trim((string) $categoria['ruta']) : $nombre;
      if ($id <= 0 && $nombre === '') { continue; }
      $incluyeDescendientes = false;
      if (isset($categoria['incluye_descendientes'])) {
        $incluyeDescendientes = filter_var($categoria['incluye_descendientes'], FILTER_VALIDATE_BOOLEAN);
      } elseif (isset($categoria['seleccion_incluye_descendientes'])) {
        $incluyeDescendientes = filter_var($categoria['seleccion_incluye_descendientes'], FILTER_VALIDATE_BOOLEAN);
      }
      $salida[] = array(
        'id_categoria_erp' => $id,
        'nombre' => $nombre,
        'ruta' => $ruta,
        'incluye_descendientes' => $incluyeDescendientes
      );
    }
    return $salida;
  }

  private function destinoPosteriorLogin() {
    if (!empty($_SESSION['distribucion_destino_post_login'])) {
      $destino = (string) $_SESSION['distribucion_destino_post_login'];
      unset($_SESSION['distribucion_destino_post_login']);
      if ($this->rutaRetornoSegura($destino)) {
        return URL_RAIZ . $destino;
      }
    }
    return URL_RAIZ;
  }

  private function rutaRetornoSegura($ruta) {
    $ruta = (string) $ruta;
    if ($ruta === '' || $ruta[0] !== '/') { return false; }
    if (strpos($ruta, '//') === 0 || stripos($ruta, '/auth/') === 0) { return false; }
    return true;
  }
}


