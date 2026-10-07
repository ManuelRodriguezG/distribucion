<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$permiso = isset($datos['permiso']) ? $datos['permiso'] : '';
$mensaje = isset($datos['mensaje']) ? $datos['mensaje'] : 'Tu acceso aún no tiene habilitada esta sección.';
$estatus = isset($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$estadoAcceso = isset($cliente['estado_acceso']) && is_array($cliente['estado_acceso']) ? $cliente['estado_acceso'] : array();
$siguientePaso = !empty($estadoAcceso['siguiente_paso']) ? $estadoAcceso['siguiente_paso'] : 'El equipo Artiani valida tu perfil y habilita tus accesos comerciales.';
?>
<section class="content-grid">
  <article class="card card-span-2">
    <div class="card-header">
      <div><h2>Acceso en revisión</h2><p><?php echo htmlspecialchars($mensaje); ?></p></div>
      <span class="badge badge-warning"><?php echo htmlspecialchars($estatus); ?></span>
    </div>
    <div class="access-state">
      <span class="metric-icon icon icon-shield"></span>
      <div>
        <strong>Sección pendiente de autorización</strong>
        <p><?php echo htmlspecialchars($siguientePaso); ?></p>
      </div>
    </div>
    <div class="actions">
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/cuenta"><span class="icon icon-user"></span>Ver mi cuenta</a>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/auth/salir"><span class="icon icon-logout"></span>Salir</a>
    </div>
  </article>

  <aside class="card">
    <div class="card-header"><div><h2>Siguientes pasos</h2><p>Revisión comercial.</p></div></div>
    <ul class="status-list">
      <li><span class="badge badge-info">1</span><span>Completa tus datos principales.</span></li>
      <li><span class="badge badge-info">2</span><span>El equipo Artiani valida tu perfil.</span></li>
      <li><span class="badge badge-info">3</span><span>Artiani habilita tus accesos comerciales cuando aplique.</span></li>
    </ul>
  </aside>
</section>
