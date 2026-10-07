<?php
$contexto = isset($datos['contexto']) && is_array($datos['contexto']) ? $datos['contexto'] : array();
$respuesta = isset($datos['respuesta']) && is_array($datos['respuesta']) ? $datos['respuesta'] : array();
$siguientePaso = isset($respuesta['siguiente_paso']) ? trim((string) $respuesta['siguiente_paso']) : '';
?>
<article class="card auth-card auth-card-standalone">
  <div class="card-header">
    <div><h2>Iniciar sesion</h2><p>Entra si ya tienes acceso aprobado o contrasena activa.</p></div>
    <span class="badge badge-info">Artiani Distribucion</span>
  </div>
  <?php if (!empty($contexto['mensaje'])): ?><div class="alert alert-warning"><?php echo htmlspecialchars($contexto['mensaje']); ?></div><?php endif; ?>
  <?php if (!empty($respuesta)): ?><div class="alert alert-warning"><?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'No fue posible iniciar sesion'); ?><?php if ($siguientePaso !== ''): ?><br><strong><?php echo htmlspecialchars($siguientePaso); ?></strong><?php endif; ?></div><?php endif; ?>
  <form method="post" class="form-grid">
    <label class="field-block">Correo<input type="email" name="correo" autocomplete="email" required></label>
    <label class="field-block">Contrasenia<input type="password" name="contrasenia" autocomplete="current-password" required></label>
    <button class="btn btn-primary w-100" type="submit"><span class="icon icon-login"></span>Ingresar</button>
  </form>
  <div class="access-state">
    <span class="metric-icon icon icon-shield"></span>
    <div>
      <strong>Que necesito para entrar?</strong>
      <p>Si aun no tienes cuenta, solicita acceso comercial. Si tu solicitud esta en revision, Artiani te avisara cuando el catalogo, precios y cotizacion esten disponibles para tu negocio.</p>
    </div>
  </div>
  <ul class="status-list compact">
    <li><span class="badge badge-info">Ayuda</span><span>Olvide mi contrasena o no recibi mi acceso: contacta a Artiani para reactivar o reenviar instrucciones.</span></li>
  </ul>
  <div class="auth-secondary-action">
    <span>Aun no tienes cuenta?</span>
    <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/auth/registro">Solicitar acceso mayorista</a>
  </div>
</article>
