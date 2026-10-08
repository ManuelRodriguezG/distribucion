<?php
$respuesta = isset($datos['notificaciones']) && is_array($datos['notificaciones']) ? $datos['notificaciones'] : array();
$items = isset($respuesta['items']) && is_array($respuesta['items']) ? $respuesta['items'] : array();
if (!function_exists('dist_notificacion_badge')) {
  function dist_notificacion_badge($tipo) {
    $tipo = (string) $tipo;
    if (in_array($tipo, array('pedido_resuelto', 'cotizacion_aprobada', 'cuenta_aprobada'), true)) { return 'badge-success'; }
    if (in_array($tipo, array('pedido_requiere_respuesta', 'ajuste_solicitado'), true)) { return 'badge-warning'; }
    if (in_array($tipo, array('pedido_rechazado', 'cuenta_rechazada'), true)) { return 'badge-danger'; }
    return 'badge-info';
  }
}
if (!function_exists('dist_notificacion_tipo_label')) {
  function dist_notificacion_tipo_label($tipo) {
    $mapa = array(
      'pedido_recibido' => 'Pedido recibido',
      'pedido_en_revision' => 'Pedido en revision',
      'pedido_resuelto' => 'Pedido resuelto',
      'pedido_requiere_respuesta' => 'Respuesta requerida',
      'cotizacion_actualizada' => 'Cotizacion actualizada',
      'cuenta_aprobada' => 'Cuenta aprobada',
      'cuenta_rechazada' => 'Cuenta rechazada',
      'mensaje_comercial' => 'Mensaje comercial'
    );
    return isset($mapa[$tipo]) ? $mapa[$tipo] : ucfirst(str_replace('_', ' ', (string) $tipo));
  }
}
if (!function_exists('dist_notificacion_canales')) {
  function dist_notificacion_canales($notificacion) {
    $canales = isset($notificacion['canales_disponibles']) && is_array($notificacion['canales_disponibles']) ? $notificacion['canales_disponibles'] : array();
    if (empty($canales)) { $canales = array('correo', 'whatsapp'); }
    return array_values(array_intersect($canales, array('correo', 'whatsapp', 'sms', 'llamada')));
  }
}
?>
<section class="card notifications-card">
  <div class="card-header">
    <div>
      <span class="badge badge-primary">Avisos comerciales</span>
      <h2>Notificaciones</h2>
      <p>Actualizaciones de pedidos, cotizaciones, cuenta y mensajes del equipo Artiani.</p>
    </div>
    <a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/pedidos"><span class="icon icon-quote"></span>Ver pedidos</a>
  </div>

  <div data-notificaciones-feedback class="alert alert-warning is-hidden"></div>

  <?php if (!empty($respuesta['error'])): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'No se pudieron consultar notificaciones.'); ?></div>
  <?php else: ?>
    <div class="notifications-list" data-notificaciones-list>
      <?php if (empty($items)): ?>
        <div class="empty-row">No tienes notificaciones pendientes.</div>
      <?php endif; ?>
      <?php foreach ($items as $notificacion): ?>
        <?php
          $id = isset($notificacion['id_notificacion']) ? $notificacion['id_notificacion'] : (isset($notificacion['id']) ? $notificacion['id'] : '');
          $tipo = isset($notificacion['tipo']) ? $notificacion['tipo'] : 'mensaje_comercial';
          $leida = !empty($notificacion['leida']) || (!empty($notificacion['estatus']) && $notificacion['estatus'] === 'leida');
          $titulo = isset($notificacion['titulo']) ? $notificacion['titulo'] : dist_notificacion_tipo_label($tipo);
          $mensaje = isset($notificacion['mensaje']) ? $notificacion['mensaje'] : '';
          $folio = isset($notificacion['folio_referencia']) ? $notificacion['folio_referencia'] : (isset($notificacion['folio']) ? $notificacion['folio'] : '');
          $fecha = isset($notificacion['fecha_registro']) ? $notificacion['fecha_registro'] : (isset($notificacion['fecha']) ? $notificacion['fecha'] : '');
          $url = isset($notificacion['url_accion']) ? $notificacion['url_accion'] : '';
          $canales = dist_notificacion_canales($notificacion);
        ?>
          <article class="notification-item <?php echo $leida ? 'is-read' : 'is-unread'; ?>" data-notificacion-id="<?php echo htmlspecialchars($id); ?>">
            <div class="notification-main">
              <div class="notification-meta">
                <span class="badge <?php echo dist_notificacion_badge($tipo); ?>"><?php echo htmlspecialchars(dist_notificacion_tipo_label($tipo)); ?></span>
                <?php if ($folio !== ''): ?><span><?php echo htmlspecialchars($folio); ?></span><?php endif; ?>
                <?php if ($fecha !== ''): ?><span><?php echo htmlspecialchars($fecha); ?></span><?php endif; ?>
              </div>
              <h3><?php echo htmlspecialchars($titulo); ?></h3>
              <?php if ($mensaje !== ''): ?><p><?php echo htmlspecialchars($mensaje); ?></p><?php endif; ?>
            </div>
            <div class="notification-actions">
              <?php if (!$leida): ?><button class="btn btn-light btn-sm" type="button" data-notificacion-leida>Marcar leida</button><?php endif; ?>
              <?php if ($url !== ''): ?><a class="btn btn-light btn-sm" href="<?php echo htmlspecialchars(URL_RAIZ . '/' . ltrim($url, '/')); ?>">Ver detalle</a><?php endif; ?>
              <?php foreach ($canales as $canal): ?>
                <button class="btn btn-light btn-sm" type="button" data-notificacion-enviar="<?php echo htmlspecialchars($canal); ?>"><?php echo htmlspecialchars($canal === 'correo' ? 'Enviar correo' : ($canal === 'whatsapp' ? 'Enviar WhatsApp' : 'Enviar ' . $canal)); ?></button>
              <?php endforeach; ?>
            </div>
          </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
