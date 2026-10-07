<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$permisos = isset($cliente['permisos']) && is_array($cliente['permisos']) ? $cliente['permisos'] : array();
$estatus = isset($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$camposPendientes = isset($cliente['campos_pendientes']) && is_array($cliente['campos_pendientes']) ? $cliente['campos_pendientes'] : array();
$estadoAcceso = isset($cliente['estado_acceso']) && is_array($cliente['estado_acceso']) ? $cliente['estado_acceso'] : array();
$tituloEstado = !empty($estadoAcceso['titulo']) ? $estadoAcceso['titulo'] : 'Acceso en revisión';
$mensajeEstado = !empty($estadoAcceso['mensaje']) ? $estadoAcceso['mensaje'] : 'Tu solicitud ya está registrada. Los accesos se habilitan después de la revisión comercial.';
$siguientePaso = !empty($estadoAcceso['siguiente_paso']) ? $estadoAcceso['siguiente_paso'] : 'Espera la autorización comercial de Artiani.';
$contactoSoporte = !empty($estadoAcceso['contacto_soporte']) ? $estadoAcceso['contacto_soporte'] : '';
?>
<article class="card auth-card auth-card-standalone auth-card-wide">
  <div class="card-header">
    <div><h2><?php echo htmlspecialchars($tituloEstado); ?></h2><p><?php echo htmlspecialchars($mensajeEstado); ?></p></div>
    <span class="badge badge-warning"><?php echo htmlspecialchars($estatus); ?></span>
  </div>

  <div class="access-state">
    <span class="metric-icon icon icon-shield"></span>
    <div>
      <strong><?php echo htmlspecialchars(isset($cliente['nombre']) ? $cliente['nombre'] : 'Cliente'); ?></strong>
      <p><?php echo htmlspecialchars($siguientePaso); ?></p>
    </div>
  </div>

  <div class="module-state-grid">
    <div class="module-state"><span class="badge badge-warning">Pendiente</span><strong>Catálogo</strong><small>Visible cuando tu catálogo esté habilitado.</small></div>
    <div class="module-state"><span class="badge badge-warning">Pendiente</span><strong>Precios</strong><small>Requiere lista comercial asignada.</small></div>
    <div class="module-state"><span class="badge badge-warning">Pendiente</span><strong>Cotización</strong><small>Disponible al habilitar solicitud de cotización.</small></div>
  </div>

  <div class="auth-status-block">
    <h3>Datos pendientes</h3>
    <ul class="status-list">
      <?php if (empty($camposPendientes)): ?><li><span class="badge badge-info">Revisión</span><span>Tu cuenta no tiene campos pendientes. Espera la autorización comercial.</span></li><?php endif; ?>
      <?php foreach ($camposPendientes as $campo): ?><li><span class="badge badge-warning">Pendiente</span><span><?php echo htmlspecialchars($campo); ?></span></li><?php endforeach; ?>
      <?php if (empty($permisos)): ?><li><span class="badge badge-warning">Acceso</span><span>Tu acceso comercial sigue en revisión.</span></li><?php endif; ?>
      <?php if ($contactoSoporte !== ''): ?><li><span class="badge badge-info">Contacto</span><span><?php echo htmlspecialchars($contactoSoporte); ?></span></li><?php endif; ?>
    </ul>
  </div>

  <div class="auth-secondary-action">
    <span>¿Quieres salir de esta sesión?</span>
    <a href="<?php echo URL_RAIZ; ?>/auth/salir">Cerrar sesión</a>
  </div>
</article>
