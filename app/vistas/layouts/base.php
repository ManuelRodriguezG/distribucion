<?php
$titulo = isset($datos['titulo']) ? $datos['titulo'] : NOMBRE_SITIO;
$cliente = isset($datos['cliente']) ? $datos['cliente'] : SesionCliente::perfil();
$perfilNombre = is_array($cliente) && !empty($cliente['nombre']) ? $cliente['nombre'] : 'Invitado';
$perfilTipo = is_array($cliente) && !empty($cliente['tipo_cliente']) ? $cliente['tipo_cliente'] : 'publico';
$perfilEstatus = is_array($cliente) && !empty($cliente['estatus']) ? $cliente['estatus'] : 'sin sesion';
$rutaActual = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$rutaSegmento = $rutaActual !== '' ? explode('/', $rutaActual)[0] : '';
$assetCss = '/public/assets/css/app.css';
$assetJs = '/public/assets/js/app.js';
$assetCssVersion = file_exists(RUTA_APP . '/../public/assets/css/app.css') ? filemtime(RUTA_APP . '/../public/assets/css/app.css') : time();
$assetJsVersion = file_exists(RUTA_APP . '/../public/assets/js/app.js') ? filemtime(RUTA_APP . '/../public/assets/js/app.js') : time();
$navItems = array(
  array('titulo' => 'Resumen', 'ruta' => '', 'icono' => 'speedometer', 'autenticado' => true),
  array('titulo' => 'Catálogo', 'ruta' => 'catalogo', 'icono' => 'boxes', 'accion' => 'ver_catalogo', 'permiso' => 'distribucion.catalogo.ver'),
  array('titulo' => 'Mi catálogo', 'ruta' => 'mi-catalogo', 'icono' => 'star', 'accion' => 'gestionar_mi_catalogo', 'permiso' => 'distribucion.mi_catalogo.gestionar'),
  array('titulo' => 'Cotizaciones', 'ruta' => 'cotizaciones', 'icono' => 'quote', 'accion' => 'agregar_cotizacion', 'permiso' => 'distribucion.cotizacion.solicitar'),
  array('titulo' => 'Nueva solicitud', 'ruta' => 'cotizacion', 'icono' => 'plus', 'accion' => 'agregar_cotizacion', 'permiso' => 'distribucion.cotizacion.solicitar'),
  array('titulo' => 'Pedidos', 'ruta' => 'pedidos', 'icono' => 'quote', 'accion' => 'agregar_cotizacion', 'permiso' => 'distribucion.pedido.preliminar'),
  array('titulo' => 'Notificaciones', 'ruta' => 'notificaciones', 'icono' => 'bell', 'autenticado' => true),
  array('titulo' => 'Cuenta', 'ruta' => 'cuenta', 'icono' => 'user', 'autenticado' => true)
);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($titulo); ?> | <?php echo NOMBRE_SITIO; ?></title>
  <link rel="stylesheet" href="<?php echo URL_RAIZ . $assetCss; ?>?v=<?php echo htmlspecialchars((string) $assetCssVersion); ?>">
</head>
<body data-url-raiz="<?php echo htmlspecialchars(URL_RAIZ); ?>">
  <div class="app-shell">
    <aside class="app-sidebar">
      <a class="app-brand" href="<?php echo URL_RAIZ; ?>">
        <span class="brand-mark">A</span>
        <span><strong>Artiani</strong><small>Distribución</small></span>
      </a>

      <nav class="side-nav" aria-label="Navegación principal">
        <?php foreach ($navItems as $item): ?>
          <?php
            $visible = empty($item['autenticado']) || SesionCliente::autenticado();
            if ($visible && !empty($item['accion'])) { $visible = SesionCliente::tieneAccion($item['accion'], isset($item['permiso']) ? $item['permiso'] : ''); }
            if (!$visible) { continue; }
            $activo = ($item['ruta'] === '' && $rutaActual === '') || ($item['ruta'] !== '' && $rutaSegmento === $item['ruta']);
          ?>
          <a class="<?php echo $activo ? 'is-active' : ''; ?>" href="<?php echo URL_RAIZ . ($item['ruta'] === '' ? '' : '/' . $item['ruta']); ?>">
            <span class="icon icon-<?php echo htmlspecialchars($item['icono']); ?>" aria-hidden="true"></span>
            <span><?php echo htmlspecialchars($item['titulo']); ?></span>
          </a>
        <?php endforeach; ?>
      </nav>

      <div class="sidebar-status">
        <span class="badge badge-info"><?php echo htmlspecialchars($perfilTipo); ?></span>
        <strong><?php echo htmlspecialchars($perfilNombre); ?></strong>
        <small><?php echo htmlspecialchars($perfilEstatus); ?></small>
      </div>
      <?php if (SesionCliente::autenticado()): ?>
        <a class="sidebar-logout" href="<?php echo URL_RAIZ; ?>/auth/salir"><span class="icon icon-logout" aria-hidden="true"></span><span>Cerrar sesión</span></a>
      <?php endif; ?>
    </aside>

    <div class="app-main">
      <header class="topbar">
        <button class="icon-button mobile-menu" type="button" data-toggle-sidebar title="Menú">
          <span class="icon icon-menu" aria-hidden="true"></span>
        </button>
        <div>
          <p class="topbar-kicker">Portal comercial</p>
          <h1><?php echo htmlspecialchars($titulo); ?></h1>
        </div>
        <div class="topbar-actions">
          <?php if (SesionCliente::autenticado()): ?>
            <a class="btn btn-light notification-topbar-link" href="<?php echo URL_RAIZ; ?>/notificaciones" title="Notificaciones">
              <span class="icon icon-bell" aria-hidden="true"></span>
              Avisos
              <span class="counter is-muted" data-notification-count>0</span>
            </a>
          <?php endif; ?>
          <?php if (SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar')): ?>
            <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/cotizacion">
              <span class="icon icon-quote" aria-hidden="true"></span>
              Cotizacion
              <span class="counter" data-cart-count>0</span>
            </a>
          <?php endif; ?>          <?php if (!SesionCliente::autenticado()): ?>
            <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/auth/login">Ingresar</a>
            <a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/auth/registro">Solicitar acceso</a>          <?php endif; ?>
        </div>
      </header>

      <main class="page">
        <?php require $contenido; ?>
      </main>
    </div>
  </div>

  <script src="<?php echo URL_RAIZ . $assetJs; ?>?v=<?php echo htmlspecialchars((string) $assetJsVersion); ?>"></script>
</body>
</html>



