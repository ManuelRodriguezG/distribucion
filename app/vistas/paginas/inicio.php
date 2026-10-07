<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$nombre = !empty($cliente['nombre']) ? $cliente['nombre'] : 'Cliente';
$estatus = !empty($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$tipoCliente = !empty($cliente['tipo_cliente']) ? $cliente['tipo_cliente'] : 'distribucion';
$puedeCatalogo = SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver');
$puedeMiCatalogo = SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar');
$puedeCotizar = SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar');
if (!function_exists('dist_inicio_estado_label')) {
  function dist_inicio_estado_label($estatus) {
    $mapa = array(
      'aprobado' => 'Aprobada',
      'pendiente' => 'Pendiente de revisión',
      'en_revision' => 'En revisión',
      'rechazado' => 'No aprobada',
      'suspendido' => 'Suspendida'
    );
    return isset($mapa[$estatus]) ? $mapa[$estatus] : ucfirst(str_replace('_', ' ', (string) $estatus));
  }
}
?>

<section class="card dashboard-welcome">
  <div>
    <span class="badge badge-primary">Portal comercial</span>
    <h2>Hola, <?php echo htmlspecialchars($nombre); ?></h2>
    <p>Este es tu resumen comercial. Desde aquí puedes consultar productos, preparar solicitudes y revisar el estado de tu cuenta.</p>
  </div>
  <div class="dashboard-status-box">
    <small>Estatus de cuenta</small>
    <strong><?php echo htmlspecialchars(dist_inicio_estado_label($estatus)); ?></strong>
    <span><?php echo htmlspecialchars($tipoCliente); ?></span>
  </div>
</section>

<section class="metrics-grid dashboard-metrics">
  <article class="metric-card"><span class="metric-icon icon icon-star"></span><div><small>Mi catálogo</small><strong><?php echo $puedeMiCatalogo ? 'Listo' : 'Pendiente'; ?></strong><span>Productos conocidos a la mano</span></div></article>
  <article class="metric-card"><span class="metric-icon icon icon-quote"></span><div><small>Cotizaciones</small><strong><?php echo $puedeCotizar ? 'Activo' : 'En revisión'; ?></strong><span>Solicitudes revisadas por el equipo Artiani</span></div></article>
  <article class="metric-card"><span class="metric-icon icon icon-shield"></span><div><small>Alertas</small><strong>0</strong><span>Sin avisos pendientes por ahora</span></div></article>
</section>

<section class="content-grid dashboard-grid">
  <article class="card card-span-2">
    <div class="card-header">
      <div><h2>Accesos de trabajo</h2><p>Entra solo a la sección que necesitas para evitar cargas innecesarias.</p></div>
      <span class="badge badge-info">Resumen ligero</span>
    </div>
    <div class="action-board dashboard-actions">
      <?php if ($puedeMiCatalogo): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/mi-catalogo"><span class="icon icon-star"></span><strong>Mi catálogo</strong><small>Productos frecuentes con cantidad y comentario para cotizar rápido.</small></a>
      <?php endif; ?>
      <?php if ($puedeCotizar): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/cotizacion"><span class="icon icon-quote"></span><strong>Cotizaciones</strong><small>Revisa partidas, cantidades y comentarios antes de enviar.</small></a>
      <?php endif; ?>
      <?php if ($puedeCatalogo): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-search"></span><strong>Catálogo completo</strong><small>Buscar productos disponibles para tu cuenta cuando lo necesites.</small></a>
      <?php endif; ?>
      <a class="action-tile" href="<?php echo URL_RAIZ; ?>/cuenta"><span class="icon icon-user"></span><strong>Mi cuenta</strong><small>Consulta los datos y accesos de tu cuenta.</small></a>
    </div>
  </article>

  <aside class="card alerts-panel">
    <div class="card-header"><div><h2>Alertas</h2><p>Seguimiento comercial.</p></div></div>
    <ul class="status-list">
      <li><span class="badge badge-light">Sin avisos</span><span>Cuando validemos una solicitud, aparecerán aquí los cambios importantes.</span></li>
      <li><span class="badge badge-info">Próximo</span><span>Notificaciones de cotizaciones, pedidos y solicitudes en revisión.</span></li>
    </ul>
  </aside>
</section>

<section class="card dashboard-empty-state">
  <div class="card-header">
    <div><h2>Resumen operativo</h2><p>Esta sección irá mostrando información conforme existan cotizaciones, pedidos y alertas conectadas.</p></div>
  </div>
  <div class="module-state-grid">
    <div class="module-state"><span class="badge badge-primary">Mi catálogo</span><strong>Productos frecuentes</strong><small>Úsalo para cotizar rápido sin buscar cada SKU en el catálogo completo.</small></div>
    <div class="module-state"><span class="badge badge-info">Cotizaciones</span><strong>Revisión comercial</strong><small>Las solicitudes se revisan antes de confirmarse y no reservan inventario hasta confirmación.</small></div>
    <div class="module-state"><span class="badge badge-warning">Pedidos</span><strong>Seguimiento</strong><small>Aquí podrás ver solicitudes enviadas, respuestas comerciales y pedidos confirmados.</small></div>
  </div>
</section>