<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$permiso = isset($datos['permiso']) ? $datos['permiso'] : '';
$mensaje = isset($datos['mensaje']) ? $datos['mensaje'] : 'Tu acceso aun no tiene habilitado este modulo.';
$estatus = isset($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$estadoAcceso = isset($cliente['estado_acceso']) && is_array($cliente['estado_acceso']) ? $cliente['estado_acceso'] : array();
$siguientePaso = !empty($estadoAcceso['siguiente_paso']) ? $estadoAcceso['siguiente_paso'] : 'El equipo Artiani valida tu perfil y el ERP asigna permisos y lista si aplica.';
?>
<section class="content-grid">
  <article class="card card-span-2">
    <div class="card-header">
      <div><h2>Acceso en revision</h2><p><?php echo htmlspecialchars($mensaje); ?></p></div>
      <span class="badge badge-warning"><?php echo htmlspecialchars($estatus); ?></span>
    </div>
    <div class="access-state">
      <span class="metric-icon icon icon-shield"></span>
      <div>
        <strong>Modulo pendiente de autorizacion</strong>
        <p><?php echo htmlspecialchars($siguientePaso); ?></p>
      </div>
    </div>
    <?php if ($permiso !== ''): ?><div class="permission-note"><span>Permiso requerido</span><strong><?php echo htmlspecialchars($permiso); ?></strong></div><?php endif; ?>
    <div class="actions">
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/cuenta"><span class="icon icon-user"></span>Ver mi cuenta</a>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/auth/salir"><span class="icon icon-logout"></span>Salir</a>
    </div>
  </article>

  <aside class="card">
    <div class="card-header"><div><h2>Siguientes pasos</h2><p>Revision comercial interna.</p></div></div>
    <ul class="status-list">
      <li><span class="badge badge-info">1</span><span>Completa tus datos principales.</span></li>
      <li><span class="badge badge-info">2</span><span>El equipo Artiani valida tu perfil.</span></li>
      <li><span class="badge badge-info">3</span><span>El ERP asigna permisos y lista si aplica.</span></li>
    </ul>
  </aside>
</section>
