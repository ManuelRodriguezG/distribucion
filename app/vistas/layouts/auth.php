<?php
$titulo = isset($datos['titulo']) ? $datos['titulo'] : NOMBRE_SITIO;
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
<body class="auth-page" data-url-raiz="<?php echo htmlspecialchars(URL_RAIZ); ?>">
  <main class="auth-root">
    <section class="auth-brand-panel">
      <a class="auth-brand" href="<?php echo URL_RAIZ; ?>/auth/login">
        <span class="brand-mark">A</span>
        <span><strong>Artiani</strong><small>Distribucion</small></span>
      </a>
      <div class="auth-visual-copy">
        <span class="badge badge-primary">Artiani Distribucion</span>
        <h1>Acceso comercial para negocios</h1>
        <p>Solicita acceso o entra con tu cuenta autorizada para consultar productos para mascotas, acuario y comercios especializados.</p>
      </div>
    </section>
    <section class="auth-form-panel">
      <?php require $contenido; ?>
    </section>
  </main>
  <script src="<?php echo URL_RAIZ . $assetJs; ?>?v=<?php echo htmlspecialchars((string) $assetJsVersion); ?>"></script>
</body>
</html>
