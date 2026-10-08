<?php
$titulo = isset($datos['titulo']) ? $datos['titulo'] : NOMBRE_SITIO;
$rutaActual = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$assetCss = '/public/assets/css/app.css';
$assetJs = '/public/assets/js/app.js';
$assetCssVersion = file_exists(RUTA_APP . '/../public/assets/css/app.css') ? filemtime(RUTA_APP . '/../public/assets/css/app.css') : time();
$assetJsVersion = file_exists(RUTA_APP . '/../public/assets/js/app.js') ? filemtime(RUTA_APP . '/../public/assets/js/app.js') : time();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($titulo); ?> | <?php echo NOMBRE_SITIO; ?></title>
  <link rel="stylesheet" href="<?php echo URL_RAIZ . $assetCss; ?>?v=<?php echo htmlspecialchars((string) $assetCssVersion); ?>">
</head>
<body class="public-page" data-url-raiz="<?php echo htmlspecialchars(URL_RAIZ); ?>">
  <header class="public-header">
    <a class="public-brand" href="<?php echo URL_RAIZ; ?>">
      <img src="<?php echo URL_RAIZ; ?>/public/assets/media/brand/artiani-logo.png" alt="Artiani Articulos para Animales">
      <span class="public-brand-tag">Distribucion</span>
    </a>
    <nav class="public-nav" aria-label="Navegacion publica">
      <a class="<?php echo $rutaActual === '' ? 'is-active' : ''; ?>" href="<?php echo URL_RAIZ; ?>">Inicio</a>
      <a href="<?php echo URL_RAIZ; ?>/auth/registro">Solicitar acceso</a>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/auth/login"><span class="icon icon-login" aria-hidden="true"></span>Iniciar sesion</a>
    </nav>
  </header>

  <main class="public-main">
    <?php require $contenido; ?>
  </main>

  <footer class="public-footer">
    <div>
      <strong>Artiani Distribucion</strong>
      <span>Acceso comercial para negocios</span>
    </div>
    <div class="public-footer-actions">
      <a href="<?php echo URL_RAIZ; ?>/auth/login">Iniciar sesion</a>
      <a href="<?php echo URL_RAIZ; ?>/auth/registro">Solicitar acceso</a>
    </div>
  </footer>
  <script src="<?php echo URL_RAIZ . $assetJs; ?>?v=<?php echo htmlspecialchars((string) $assetJsVersion); ?>"></script>
</body>
</html>
