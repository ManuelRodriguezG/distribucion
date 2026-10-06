<?php
$estado = isset($datos['estado']) ? $datos['estado'] : 'invalido';
$respuesta = isset($datos['respuesta']) && is_array($datos['respuesta']) ? $datos['respuesta'] : array();
$consulta = isset($datos['consulta']) && is_array($datos['consulta']) ? $datos['consulta'] : array();
$mensajeErrorLink = 'El link no es valido o ya vencio. Solicita uno nuevo por WhatsApp.';
?>
<article class="card auth-card auth-card-standalone">
  <div class="card-header">
    <div><h2>Activar cuenta</h2><p>Crea tu contrasena para ingresar al portal de Distribucion.</p></div>
    <span class="badge badge-info">Link seguro</span>
  </div>

  <?php if ($estado === 'invalido'): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars(isset($consulta['mensaje']) && !empty($consulta['mensaje']) ? $consulta['mensaje'] : $mensajeErrorLink); ?></div>
    <ul class="status-list compact">
      <li><span class="badge badge-warning">72h</span><span>El link vence automaticamente.</span></li>
      <li><span class="badge badge-info">WhatsApp</span><span>Solicita un nuevo enlace al equipo Artiani.</span></li>
    </ul>
    <div class="auth-secondary-action">
      <span>Ya tienes contrasena?</span>
      <a href="<?php echo URL_RAIZ; ?>/auth/login">Iniciar sesion</a>
    </div>
  <?php elseif ($estado === 'activado'): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'Contrasenia creada. Ya puedes iniciar sesion.'); ?></div>
    <ul class="status-list compact">
      <li><span class="badge badge-success">OK</span><span>Tu contrasena fue creada correctamente.</span></li>
      <li><span class="badge badge-info">Login</span><span>Por seguridad, inicia sesion con tu correo y contrasena.</span></li>
    </ul>
    <div class="auth-secondary-action">
      <span>Continua al portal.</span>
      <a href="<?php echo URL_RAIZ; ?>/auth/login">Iniciar sesion</a>
    </div>
  <?php else: ?>
    <?php if (!empty($respuesta)): ?><div class="alert alert-warning"><?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'No fue posible crear la contrasena'); ?></div><?php endif; ?>
    <form method="post" class="form-grid" autocomplete="off">
      <label class="field-block">Nueva contrasena<input type="password" name="contrasenia" minlength="8" autocomplete="new-password" required></label>
      <label class="field-block">Confirmar contrasena<input type="password" name="confirmar_contrasenia" minlength="8" autocomplete="new-password" required></label>
      <div class="password-rule-list">
        <span>Minimo 8 caracteres.</span>
        <span>No compartas tu contrasena por WhatsApp.</span>
        <span>Crear contrasena no inicia sesion automaticamente.</span>
      </div>
      <button class="btn btn-primary w-100" type="submit"><span class="icon icon-login"></span>Crear contrasena</button>
    </form>
  <?php endif; ?>
</article>
