<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$nombre = !empty($cliente['nombre']) ? $cliente['nombre'] : 'Cliente';
$estatus = !empty($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$tipoCliente = !empty($cliente['tipo_cliente']) ? $cliente['tipo_cliente'] : 'distribucion';
$puedeCatalogo = SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver');
$puedeMiCatalogo = SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar');
$puedeCotizar = SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar');
$pedidosRespuesta = isset($datos['pedidos']) && is_array($datos['pedidos']) ? $datos['pedidos'] : array();
$cotizacionesRespuesta = isset($datos['cotizaciones']) && is_array($datos['cotizaciones']) ? $datos['cotizaciones'] : array();
$notificacionesRespuesta = isset($datos['notificaciones']) && is_array($datos['notificaciones']) ? $datos['notificaciones'] : array();
$notificacionesResumen = isset($datos['notificaciones_resumen']) && is_array($datos['notificaciones_resumen']) ? $datos['notificaciones_resumen'] : array();
$pedidosItems = isset($pedidosRespuesta['items']) && is_array($pedidosRespuesta['items']) ? $pedidosRespuesta['items'] : array();
$cotizacionesItems = isset($cotizacionesRespuesta['items']) && is_array($cotizacionesRespuesta['items']) ? $cotizacionesRespuesta['items'] : array();
$notificacionesItems = isset($notificacionesRespuesta['items']) && is_array($notificacionesRespuesta['items']) ? $notificacionesRespuesta['items'] : array();
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
if (!function_exists('dist_inicio_money')) {
  function dist_inicio_money($valor, $moneda = 'MXN') {
    if ($valor === null || $valor === '' || !is_numeric($valor)) { return 'Pendiente de revisión'; }
    return '$' . number_format((float) $valor, 2) . ' ' . htmlspecialchars($moneda ? $moneda : 'MXN');
  }
}
if (!function_exists('dist_inicio_pedido_label')) {
  function dist_inicio_pedido_label($estatus) {
    $mapa = array(
      'pedido_solicitado' => 'Pedido recibido',
      'en_revision' => 'En revisión comercial',
      'respondida' => 'Respuesta enviada',
      'requiere_ajuste_cliente' => 'Ajuste solicitado',
      'cliente_acepto' => 'Propuesta aceptada',
      'aceptado' => 'Propuesta aceptada',
      'cliente_rechazo' => 'Propuesta no aceptada',
      'rechazado' => 'Propuesta no aceptada',
      'cancelado' => 'Cancelado',
      'terminado' => 'Completado',
      'completado' => 'Completado',
      'sin_estatus' => 'En revisión'
    );
    return isset($mapa[$estatus]) ? $mapa[$estatus] : 'En revisión';
  }
}
if (!function_exists('dist_inicio_cotizacion_label')) {
  function dist_inicio_cotizacion_label($estatus) {
    $mapa = array(
      'borrador' => 'Borrador',
      'recibida' => 'Recibida por Artiani',
      'enviada_como_pedido' => 'Solicitud enviada',
      'pedido_solicitado' => 'Pedido recibido',
      'cancelada' => 'Cancelada',
      'vencida' => 'Vencida',
      'sin_estatus' => 'En revisión'
    );
    return isset($mapa[$estatus]) ? $mapa[$estatus] : 'En revisión';
  }
}
if (!function_exists('dist_inicio_badge')) {
  function dist_inicio_badge($estatus) {
    $estatus = (string) $estatus;
    if (in_array($estatus, array('cliente_acepto', 'aceptado', 'terminado', 'completado', 'cuenta_aprobada', 'pedido_resuelto'), true)) { return 'badge-success'; }
    if (in_array($estatus, array('cliente_rechazo', 'rechazado', 'cancelado', 'cuenta_rechazada'), true)) { return 'badge-danger'; }
    if (in_array($estatus, array('borrador', 'pedido_solicitado', 'pendiente_respuesta_cliente', 'pedido_requiere_respuesta'), true)) { return 'badge-warning'; }
    return 'badge-info';
  }
}
if (!function_exists('dist_inicio_notificacion_label')) {
  function dist_inicio_notificacion_label($tipo) {
    $mapa = array(
      'pedido_recibido' => 'Pedido recibido',
      'pedido_en_revision' => 'Pedido en revisión',
      'pedido_resuelto' => 'Pedido revisado',
      'pedido_requiere_respuesta' => 'Respuesta requerida',
      'pedido_revisado' => 'Pedido revisado',
      'pedido_respuesta_disponible' => 'Respuesta disponible',
      'cotizacion_actualizada' => 'Cotización actualizada',
      'cuenta_aprobada' => 'Cuenta aprobada',
      'cuenta_rechazada' => 'Cuenta no aprobada',
      'mensaje_comercial' => 'Aviso comercial'
    );
    return isset($mapa[$tipo]) ? $mapa[$tipo] : 'Aviso comercial';
  }
}
if (!function_exists('dist_inicio_total_respuesta')) {
  function dist_inicio_total_respuesta($respuesta, $items) {
    if (isset($respuesta['total']) && is_numeric($respuesta['total'])) { return (int) $respuesta['total']; }
    if (isset($respuesta['paginacion']['total']) && is_numeric($respuesta['paginacion']['total'])) { return (int) $respuesta['paginacion']['total']; }
    return count($items);
  }
}
$pedidosTotal = dist_inicio_total_respuesta($pedidosRespuesta, $pedidosItems);
$cotizacionesTotal = dist_inicio_total_respuesta($cotizacionesRespuesta, $cotizacionesItems);
$notificacionesNoLeidas = 0;
foreach (array('no_leidas', 'pendientes', 'total_no_leidas') as $clave) {
  if (isset($notificacionesResumen[$clave]) && is_numeric($notificacionesResumen[$clave])) { $notificacionesNoLeidas = (int) $notificacionesResumen[$clave]; break; }
}
if ($notificacionesNoLeidas === 0) {
  foreach ($notificacionesItems as $notificacion) {
    $leida = !empty($notificacion['leida']) || (!empty($notificacion['estatus']) && $notificacion['estatus'] === 'leida');
    if (!$leida) { $notificacionesNoLeidas++; }
  }
}
$pedidosEnProceso = 0;
$pedidosConRespuesta = 0;
foreach ($pedidosItems as $pedido) {
  $estatusPedido = isset($pedido['estatus']) ? $pedido['estatus'] : '';
  if (in_array($estatusPedido, array('pedido_solicitado', 'en_revision', 'respondida', 'requiere_ajuste_cliente'), true)) { $pedidosEnProceso++; }
  if (!empty($pedido['respuesta_cliente_estatus']) || in_array($estatusPedido, array('respondida', 'requiere_ajuste_cliente'), true)) { $pedidosConRespuesta++; }
}
$cotizacionesBorrador = 0;
foreach ($cotizacionesItems as $cotizacion) {
  if (!empty($cotizacion['estatus']) && $cotizacion['estatus'] === 'borrador') { $cotizacionesBorrador++; }
}
$accionesPendientes = array();
if ($pedidosConRespuesta > 0) {
  $accionesPendientes[] = array('badge' => 'Respuesta', 'clase' => 'badge-warning', 'titulo' => 'Revisa propuestas comerciales pendientes', 'texto' => 'Tienes pedidos con respuesta o ajuste solicitado por Artiani.', 'url' => '/pedidos', 'accion' => 'Ver pedidos');
}
if ($cotizacionesBorrador > 0) {
  $accionesPendientes[] = array('badge' => 'Borradores', 'clase' => 'badge-info', 'titulo' => 'Continúa cotizaciones guardadas', 'texto' => 'Puedes editar, enviar o duplicar tus cotizaciones pendientes.', 'url' => '/cotizaciones', 'accion' => 'Ver cotizaciones');
}
if ($notificacionesNoLeidas > 0) {
  $accionesPendientes[] = array('badge' => 'Avisos', 'clase' => 'badge-primary', 'titulo' => 'Tienes avisos sin leer', 'texto' => 'Consulta actualizaciones de pedidos, cuenta o mensajes comerciales.', 'url' => '/notificaciones', 'accion' => 'Ver avisos');
}
?>

<section class="card dashboard-welcome">
  <div>
    <span class="badge badge-primary">Portal comercial</span>
    <h2>Hola, <?php echo htmlspecialchars($nombre); ?></h2>
    <p>Este es tu resumen comercial. Desde aquí puedes revisar pedidos en proceso, continuar cotizaciones y atender avisos importantes de Artiani.</p>
  </div>
  <div class="dashboard-status-box">
    <small>Estatus de cuenta</small>
    <strong><?php echo htmlspecialchars(dist_inicio_estado_label($estatus)); ?></strong>
    <span><?php echo htmlspecialchars($tipoCliente); ?></span>
  </div>
</section>

<section class="metrics-grid dashboard-metrics">
  <a class="metric-card dashboard-metric-link" href="<?php echo URL_RAIZ; ?>/pedidos"><span class="metric-icon icon icon-quote"></span><div><small>Pedidos en proceso</small><strong><?php echo htmlspecialchars((string) $pedidosEnProceso); ?></strong><span><?php echo $pedidosConRespuesta > 0 ? htmlspecialchars($pedidosConRespuesta . ' con respuesta por revisar') : 'Seguimiento de solicitudes enviadas'; ?></span></div></a>
  <a class="metric-card dashboard-metric-link" href="<?php echo URL_RAIZ; ?>/cotizaciones"><span class="metric-icon icon icon-quote"></span><div><small>Cotizaciones</small><strong><?php echo htmlspecialchars((string) $cotizacionesTotal); ?></strong><span><?php echo $cotizacionesBorrador > 0 ? htmlspecialchars($cotizacionesBorrador . ' borrador(es) pendiente(s)') : 'Historial y solicitudes guardadas'; ?></span></div></a>
  <a class="metric-card dashboard-metric-link" href="<?php echo URL_RAIZ; ?>/notificaciones"><span class="metric-icon icon icon-bell"></span><div><small>Avisos</small><strong><?php echo htmlspecialchars((string) $notificacionesNoLeidas); ?></strong><span><?php echo $notificacionesNoLeidas > 0 ? 'Notificaciones sin leer' : 'Sin avisos pendientes por ahora'; ?></span></div></a>
</section>

<section class="content-grid dashboard-grid">
  <article class="card card-span-2 dashboard-activity-card">
    <div class="card-header">
      <div><h2>Acciones pendientes</h2><p>Atiende primero lo que requiere revisión o seguimiento.</p></div>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/notificaciones"><span class="icon icon-bell"></span>Ver avisos</a>
    </div>
    <div class="dashboard-action-list">
      <?php if (empty($accionesPendientes)): ?>
        <div class="dashboard-action-item is-empty"><span class="badge badge-success">Al día</span><div><strong>No tienes acciones urgentes por ahora.</strong><small>Cuando una cotización, pedido o aviso requiera atención, aparecerá aquí.</small></div></div>
      <?php endif; ?>
      <?php foreach ($accionesPendientes as $accion): ?>
        <a class="dashboard-action-item" href="<?php echo URL_RAIZ . $accion['url']; ?>"><span class="badge <?php echo htmlspecialchars($accion['clase']); ?>"><?php echo htmlspecialchars($accion['badge']); ?></span><div><strong><?php echo htmlspecialchars($accion['titulo']); ?></strong><small><?php echo htmlspecialchars($accion['texto']); ?></small></div><span class="btn btn-light btn-sm"><?php echo htmlspecialchars($accion['accion']); ?></span></a>
      <?php endforeach; ?>
    </div>
  </article>

  <aside class="card alerts-panel">
    <div class="card-header"><div><h2>Notificaciones recientes</h2><p>Avisos comerciales.</p></div></div>
    <div class="dashboard-mini-list">
      <?php if (!empty($notificacionesRespuesta['error'])): ?>
        <div class="empty-row">No se pudieron consultar avisos por ahora.</div>
      <?php elseif (empty($notificacionesItems)): ?>
        <div class="empty-row">No tienes notificaciones pendientes.</div>
      <?php endif; ?>
      <?php foreach (array_slice($notificacionesItems, 0, 4) as $notificacion): ?>
        <?php
          $tipo = isset($notificacion['tipo']) ? $notificacion['tipo'] : 'mensaje_comercial';
          $titulo = isset($notificacion['titulo']) && $notificacion['titulo'] !== '' ? $notificacion['titulo'] : dist_inicio_notificacion_label($tipo);
          $fecha = isset($notificacion['fecha_registro']) ? $notificacion['fecha_registro'] : (isset($notificacion['fecha']) ? $notificacion['fecha'] : '');
          $url = isset($notificacion['url_accion']) && $notificacion['url_accion'] !== '' ? '/' . ltrim($notificacion['url_accion'], '/') : '/notificaciones';
        ?>
        <a class="dashboard-mini-item" href="<?php echo URL_RAIZ . htmlspecialchars($url); ?>"><span class="badge <?php echo dist_inicio_badge($tipo); ?>"><?php echo htmlspecialchars(dist_inicio_notificacion_label($tipo)); ?></span><strong><?php echo htmlspecialchars($titulo); ?></strong><?php if ($fecha !== ''): ?><small><?php echo htmlspecialchars($fecha); ?></small><?php endif; ?></a>
      <?php endforeach; ?>
    </div>
  </aside>
</section>

<section class="content-grid dashboard-grid">
  <article class="card dashboard-activity-card">
    <div class="card-header"><div><h2>Pedidos recientes</h2><p>Solicitudes enviadas y respuestas comerciales.</p></div><a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/pedidos">Ver todos</a></div>
    <div class="dashboard-mini-list">
      <?php if (!empty($pedidosRespuesta['error'])): ?>
        <div class="empty-row">No se pudieron consultar pedidos por ahora.</div>
      <?php elseif (empty($pedidosItems)): ?>
        <div class="empty-row">Todavía no tienes pedidos. Puedes enviar una solicitud desde cotización.</div>
      <?php endif; ?>
      <?php foreach (array_slice($pedidosItems, 0, 5) as $pedido): ?>
        <?php $moneda = isset($pedido['moneda']) ? $pedido['moneda'] : 'MXN'; $estatusPedido = isset($pedido['estatus']) ? $pedido['estatus'] : 'sin_estatus'; ?>
        <a class="dashboard-mini-item" href="<?php echo URL_RAIZ; ?>/pedidos"><span class="badge <?php echo dist_inicio_badge($estatusPedido); ?>"><?php echo htmlspecialchars(dist_inicio_pedido_label($estatusPedido)); ?></span><strong><?php echo htmlspecialchars(isset($pedido['folio']) ? $pedido['folio'] : 'Pedido sin folio'); ?></strong><small><?php echo htmlspecialchars(dist_inicio_money(isset($pedido['total_estimado']) ? $pedido['total_estimado'] : null, $moneda)); ?> · <?php echo htmlspecialchars(isset($pedido['cantidad_solicitada']) ? (float) $pedido['cantidad_solicitada'] : 0); ?> piezas</small></a>
      <?php endforeach; ?>
    </div>
  </article>

  <article class="card dashboard-activity-card">
    <div class="card-header"><div><h2>Cotizaciones recientes</h2><p>Borradores y solicitudes guardadas.</p></div><a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/cotizaciones">Ver todas</a></div>
    <div class="dashboard-mini-list">
      <?php if (!empty($cotizacionesRespuesta['error'])): ?>
        <div class="empty-row">No se pudieron consultar cotizaciones por ahora.</div>
      <?php elseif (empty($cotizacionesItems)): ?>
        <div class="empty-row">Todavía no tienes cotizaciones guardadas.</div>
      <?php endif; ?>
      <?php foreach (array_slice($cotizacionesItems, 0, 5) as $cotizacion): ?>
        <?php $moneda = isset($cotizacion['moneda']) ? $cotizacion['moneda'] : 'MXN'; $estatusCot = isset($cotizacion['estatus']) ? $cotizacion['estatus'] : 'sin_estatus'; ?>
        <a class="dashboard-mini-item" href="<?php echo URL_RAIZ; ?>/cotizaciones"><span class="badge <?php echo dist_inicio_badge($estatusCot); ?>"><?php echo htmlspecialchars(dist_inicio_cotizacion_label($estatusCot)); ?></span><strong><?php echo htmlspecialchars(isset($cotizacion['folio']) ? $cotizacion['folio'] : (isset($cotizacion['nombre_documento']) ? $cotizacion['nombre_documento'] : 'Cotización')); ?></strong><small><?php echo htmlspecialchars(dist_inicio_money(isset($cotizacion['total_estimado']) ? $cotizacion['total_estimado'] : null, $moneda)); ?> · <?php echo htmlspecialchars(isset($cotizacion['cantidad_total']) ? (float) $cotizacion['cantidad_total'] : 0); ?> piezas</small></a>
      <?php endforeach; ?>
    </div>
  </article>
</section>

<section class="card dashboard-empty-state">
  <div class="card-header">
    <div><h2>Accesos de trabajo</h2><p>Entra directo a la sección que necesitas.</p></div>
  </div>
  <div class="module-state-grid">
    <?php if ($puedeMiCatalogo): ?><a class="module-state dashboard-module-link" href="<?php echo URL_RAIZ; ?>/mi-catalogo"><span class="badge badge-primary">Mi catálogo</span><strong>Productos frecuentes</strong><small>Úsalo para cotizar rápido sin buscar cada SKU en el catálogo completo.</small></a><?php endif; ?>
    <?php if ($puedeCotizar): ?><a class="module-state dashboard-module-link" href="<?php echo URL_RAIZ; ?>/cotizaciones"><span class="badge badge-info">Cotizaciones</span><strong>Historial y borradores</strong><small>Revisa, edita o envía solicitudes guardadas.</small></a><?php endif; ?>
    <a class="module-state dashboard-module-link" href="<?php echo URL_RAIZ; ?>/pedidos"><span class="badge badge-warning">Pedidos</span><strong>Seguimiento</strong><small>Consulta solicitudes enviadas, respuestas comerciales y pedidos confirmados.</small></a>
    <?php if ($puedeCatalogo): ?><a class="module-state dashboard-module-link" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="badge badge-primary">Catálogo</span><strong>Productos autorizados</strong><small>Busca productos disponibles para tu cuenta cuando lo necesites.</small></a><?php endif; ?>
  </div>
</section>
