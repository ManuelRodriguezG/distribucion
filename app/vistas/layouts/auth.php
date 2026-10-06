<?php
$titulo = isset($datos['titulo']) ? $datos['titulo'] : NOMBRE_SITIO;
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($titulo); ?> | <?php echo NOMBRE_SITIO; ?></title>
  <link rel="stylesheet" href="<?php echo URL_RAIZ; ?>/public/assets/css/app.css">
</head>
<body class="auth-page" data-url-raiz="<?php echo htmlspecialchars(URL_RAIZ); ?>">
  <main class="auth-root">
    <section class="auth-brand-panel">
      <a class="auth-brand" href="<?php echo URL_RAIZ; ?>/auth/login">
        <span class="brand-mark">A</span>
        <span><strong>Artiani</strong><small>Distribucion</small></span>
      </a>
      <div class="auth-visual-copy">
        <span class="badge badge-primary">Portal B2B privado</span>
        <h1>Acceso comercial autorizado</h1>
        <p>Catalogo, precios, disponibilidad y cotizaciones se habilitan desde el ERP segun tu perfil comercial.</p>
      </div>
    </section>
    <section class="auth-form-panel">
      <?php require $contenido; ?>
    </section>
  </main>
  <script src="<?php echo URL_RAIZ; ?>/public/assets/js/app.js"></script>
</body>
</html>
