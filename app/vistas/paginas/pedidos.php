<?php
$pedidos = isset($datos['pedidos']) && is_array($datos['pedidos']) ? $datos['pedidos'] : array();
$items = isset($pedidos['items']) && is_array($pedidos['items']) ? $pedidos['items'] : array();
if (!function_exists('dist_pedido_money')) {
  function dist_pedido_money($valor, $moneda = 'MXN') {
    if ($valor === null || $valor === '' || !is_numeric($valor)) { return 'Pendiente de revision'; }
    return '$' . number_format((float) $valor, 2) . ' ' . htmlspecialchars($moneda ? $moneda : 'MXN');
  }
}
if (!function_exists('dist_pedido_badge')) {
  function dist_pedido_badge($estatus) {
    $estatus = (string) $estatus;
    if (in_array($estatus, array('cliente_acepto', 'aceptado', 'terminado', 'completado'), true)) { return 'badge-success'; }
    if (in_array($estatus, array('cliente_rechazo', 'rechazado', 'cancelado'), true)) { return 'badge-danger'; }
    if (in_array($estatus, array('en_revision', 'respondida', 'requiere_ajuste_cliente'), true)) { return 'badge-info'; }
    return 'badge-warning';
  }
}
?>
<section class="card catalog-table-card pedidos-card">
  <div class="card-header">
    <div><span class="badge badge-primary">ERP</span><h2>Mis pedidos</h2><p>Seguimiento de pedidos solicitados, respuestas del ERP y pedidos ya cerrados.</p></div>
    <a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/cotizacion"><span class="icon icon-plus"></span>Nueva cotizacion</a>
  </div>

  <?php if (!empty($pedidos['error'])): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars(isset($pedidos['mensaje']) ? $pedidos['mensaje'] : 'No se pudieron consultar pedidos.'); ?></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="data-table pedidos-table">
        <thead><tr><th>Folio</th><th>Estatus</th><th>Lineas</th><th>Piezas solicitadas</th><th>Total estimado</th><th>Total confirmado</th><th>Entrega</th><th>Fecha</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
          <?php if (empty($items)): ?><tr><td colspan="9" class="empty-row">Todavia no tienes pedidos registrados. Crea una cotizacion y enviala como pedido cuando este lista.</td></tr><?php endif; ?>
          <?php foreach ($items as $pedido): ?>
            <?php $id = isset($pedido['id_cotizacion_distribucion']) ? $pedido['id_cotizacion_distribucion'] : ''; $moneda = isset($pedido['moneda']) ? $pedido['moneda'] : 'MXN'; ?>
            <tr>
              <td><strong><?php echo htmlspecialchars(isset($pedido['folio']) ? $pedido['folio'] : 'Sin folio'); ?></strong><small class="line-muted">ID <?php echo htmlspecialchars($id); ?></small></td>
              <td><span class="badge <?php echo dist_pedido_badge(isset($pedido['estatus']) ? $pedido['estatus'] : ''); ?>"><?php echo htmlspecialchars(isset($pedido['estatus']) ? $pedido['estatus'] : 'sin_estatus'); ?></span><?php if (!empty($pedido['respuesta_cliente_estatus'])): ?><small class="line-muted">Respuesta: <?php echo htmlspecialchars($pedido['respuesta_cliente_estatus']); ?></small><?php endif; ?></td>
              <td><?php echo htmlspecialchars(isset($pedido['partidas']) ? $pedido['partidas'] : 0); ?> productos distintos</td>
              <td><?php echo htmlspecialchars(isset($pedido['cantidad_solicitada']) ? (float) $pedido['cantidad_solicitada'] : 0); ?> piezas</td>
              <td class="fw-bold"><?php echo dist_pedido_money(isset($pedido['total_estimado']) ? $pedido['total_estimado'] : null, $moneda); ?></td>
              <td class="fw-bold"><?php echo dist_pedido_money(isset($pedido['total_confirmado']) ? $pedido['total_confirmado'] : null, $moneda); ?></td>
              <td><?php echo htmlspecialchars(isset($pedido['tipo_entrega']) && $pedido['tipo_entrega'] ? $pedido['tipo_entrega'] : 'por_definir'); ?></td>
              <td><?php echo htmlspecialchars(isset($pedido['fecha_registro']) ? $pedido['fecha_registro'] : ''); ?></td>
              <td class="text-end action-text-buttons"><button class="btn btn-light btn-sm" type="button" data-open-pedido-detalle data-id="<?php echo htmlspecialchars($id); ?>">Ver pedido</button></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<div class="modal-backdrop is-hidden" data-pedido-modal>
  <section class="product-modal pedido-modal" role="dialog" aria-modal="true" aria-labelledby="pedido_modal_titulo">
    <button class="modal-close" type="button" data-close-pedido-modal>Cerrar</button>
    <div class="pedido-modal-header">
      <span class="badge badge-primary" data-pedido-modal-folio>Pedido</span>
      <h2 id="pedido_modal_titulo" data-pedido-modal-title>Detalle de pedido</h2>
      <p class="muted" data-pedido-modal-status></p>
    </div>
    <div data-pedido-modal-alert class="alert alert-warning is-hidden"></div>
    <div class="table-responsive">
      <table class="data-table pedidos-detail-table">
        <thead><tr><th>Producto</th><th>SKU</th><th>Cantidad solicitada</th><th>Cantidad confirmada</th><th>Precio unitario</th><th>Importe estimado</th><th>Revision ERP</th></tr></thead>
        <tbody data-pedido-modal-items></tbody>
      </table>
    </div>
    <label class="field-block pedido-response-comment">Comentario para el ERP<textarea rows="3" data-pedido-response-comment placeholder="Opcional: ajuste, dudas o condiciones"></textarea></label>
    <div class="summary-actions pedido-modal-actions">
      <button class="btn btn-primary" type="button" data-repeat-pedido>Volver a cotizar</button>
      <button class="btn btn-light" type="button" data-pedido-responder="aceptado">Aceptar respuesta</button>
      <button class="btn btn-light" type="button" data-pedido-responder="requiere_ajuste">Pedir ajuste</button>
      <button class="btn btn-light-danger" type="button" data-pedido-responder="rechazado">Rechazar</button>
    </div>
  </section>
</div>




