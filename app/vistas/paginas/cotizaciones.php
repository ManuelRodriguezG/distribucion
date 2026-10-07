<?php
$cotizaciones = isset($datos['cotizaciones']) && is_array($datos['cotizaciones']) ? $datos['cotizaciones'] : array();
$items = isset($cotizaciones['items']) && is_array($cotizaciones['items']) ? $cotizaciones['items'] : array();
$estatusActual = isset($_GET['estatus']) ? trim((string) $_GET['estatus']) : '';
if (!function_exists('dist_cot_money')) {
  function dist_cot_money($valor, $moneda = 'MXN') {
    if ($valor === null || $valor === '' || !is_numeric($valor)) { return 'Pendiente de revision'; }
    return '$' . number_format((float) $valor, 2) . ' ' . htmlspecialchars($moneda ? $moneda : 'MXN');
  }
}
if (!function_exists('dist_cot_badge')) {
  function dist_cot_badge($estatus) {
    $estatus = (string) $estatus;
    if ($estatus === 'borrador') { return 'badge-warning'; }
    if ($estatus === 'enviada_como_pedido') { return 'badge-info'; }
    if ($estatus === 'cancelada') { return 'badge-danger'; }
    if ($estatus === 'vencida') { return 'badge-light'; }
    return 'badge-primary';
  }
}
if (!function_exists('dist_cot_estado_label')) {
  function dist_cot_estado_label($estatus) {
    $mapa = array('borrador' => 'Borrador', 'recibida' => 'Recibida por Artiani', 'enviada_como_pedido' => 'Solicitud enviada', 'pedido_solicitado' => 'Pedido recibido', 'cancelada' => 'Cancelada', 'vencida' => 'Vencida', 'sin_estatus' => 'En revisión');
    return isset($mapa[$estatus]) ? $mapa[$estatus] : 'En revisión';
  }
}
if (!function_exists('dist_cot_acciones')) {
  function dist_cot_acciones($cotizacion) {
    if (isset($cotizacion['acciones']) && is_array($cotizacion['acciones'])) {
      $acciones = $cotizacion['acciones'];
      if (array_keys($acciones) !== range(0, count($acciones) - 1)) {
        return array_keys(array_filter($acciones));
      }
      return $acciones;
    }
    $estatus = isset($cotizacion['estatus']) ? $cotizacion['estatus'] : '';
    if ($estatus === 'borrador') { return array('ver', 'editar', 'imprimir', 'enviar_pedido', 'cancelar', 'duplicar'); }
    if ($estatus === 'enviada_como_pedido') { return array('ver', 'imprimir', 'duplicar', 'ver_pedido'); }
    return array('ver', 'duplicar');
  }
}
?>
<section class="card catalog-table-card cotizaciones-card">
  <div class="card-header">
    <div><span class="badge badge-primary">Historial</span><h2>Mis cotizaciones</h2><p>Borradores y solicitudes guardadas para dar seguimiento.</p></div>
    <a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/cotizacion"><span class="icon icon-plus"></span>Nueva solicitud</a>
  </div>

  <form class="document-toolbar" method="get" action="<?php echo URL_RAIZ; ?>/cotizaciones">
    <label class="field-block">Estatus
      <select name="estatus">
        <option value="">Todos</option>
        <?php foreach (array('borrador' => 'Borrador', 'enviada_como_pedido' => 'Solicitud enviada', 'cancelada' => 'Cancelada', 'vencida' => 'Vencida') as $valor => $etiqueta): ?>
          <option value="<?php echo htmlspecialchars($valor); ?>" <?php echo $estatusActual === $valor ? 'selected' : ''; ?>><?php echo htmlspecialchars($etiqueta); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <div class="table-search-actions">
      <button class="btn btn-light" type="submit">Filtrar</button>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/cotizaciones">Limpiar</a>
    </div>
  </form>

  <div data-cotizaciones-feedback class="alert alert-warning is-hidden"></div>
  <?php if (!empty($cotizaciones['error'])): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars(isset($cotizaciones['mensaje']) ? $cotizaciones['mensaje'] : 'No se pudieron consultar cotizaciones.'); ?></div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="data-table cotizaciones-table">
        <thead><tr><th>Folio</th><th>Nombre</th><th>Estatus</th><th>Productos</th><th>Piezas</th><th>Total estimado</th><th>Fecha</th><th class="text-end">Acciones</th></tr></thead>
        <tbody>
          <?php if (empty($items)): ?><tr><td colspan="8" class="empty-row">Todavía no tienes cotizaciones guardadas en el historial nuevo. Si ya enviaste una solicitud antes de activar cotizaciones separadas, revísala en <a href="<?php echo URL_RAIZ; ?>/pedidos">Mis pedidos</a>. Las nuevas cotizaciones que guardes como borrador aparecerán aquí.</td></tr><?php endif; ?>
          <?php foreach ($items as $cotizacion): ?>
            <?php
              $id = isset($cotizacion['id_cotizacion_distribucion']) ? $cotizacion['id_cotizacion_distribucion'] : '';
              $moneda = isset($cotizacion['moneda']) ? $cotizacion['moneda'] : 'MXN';
              $acciones = dist_cot_acciones($cotizacion);
              $idPedido = isset($cotizacion['id_pedido_distribucion']) ? $cotizacion['id_pedido_distribucion'] : '';
            ?>
            <tr data-cotizacion-row data-id="<?php echo htmlspecialchars($id); ?>" data-id-pedido="<?php echo htmlspecialchars($idPedido); ?>" data-estatus="<?php echo htmlspecialchars(isset($cotizacion['estatus']) ? $cotizacion['estatus'] : ''); ?>">
              <td><strong><?php echo htmlspecialchars(isset($cotizacion['folio']) ? $cotizacion['folio'] : 'Sin folio'); ?></strong></td>
              <td><strong><?php echo htmlspecialchars(isset($cotizacion['nombre_documento']) && $cotizacion['nombre_documento'] ? $cotizacion['nombre_documento'] : 'Cotizacion'); ?></strong><small class="line-muted"><?php echo htmlspecialchars(isset($cotizacion['comentarios']) ? substr((string) $cotizacion['comentarios'], 0, 80) : ''); ?></small></td>
              <td><span class="badge <?php echo dist_cot_badge(isset($cotizacion['estatus']) ? $cotizacion['estatus'] : ''); ?>"><?php echo htmlspecialchars(dist_cot_estado_label(isset($cotizacion['estatus']) ? $cotizacion['estatus'] : 'sin_estatus')); ?></span></td>
              <td><?php echo htmlspecialchars(isset($cotizacion['partidas']) ? $cotizacion['partidas'] : 0); ?> productos</td>
              <td><?php echo htmlspecialchars(isset($cotizacion['cantidad_total']) ? (float) $cotizacion['cantidad_total'] : 0); ?> piezas</td>
              <td class="fw-bold"><?php echo dist_cot_money(isset($cotizacion['total_estimado']) ? $cotizacion['total_estimado'] : null, $moneda); ?></td>
              <td><?php echo htmlspecialchars(isset($cotizacion['fecha_registro']) ? $cotizacion['fecha_registro'] : ''); ?></td>
              <td class="text-end action-text-buttons document-actions">
                <?php if (in_array('ver', $acciones, true)): ?><button class="btn btn-light btn-sm" type="button" data-cotizacion-detalle>Ver</button><?php endif; ?>
                <?php if (in_array('editar', $acciones, true)): ?><button class="btn btn-light btn-sm" type="button" data-cotizacion-editar>Editar borrador</button><?php endif; ?>
                <?php if (in_array('enviar_pedido', $acciones, true)): ?><button class="btn btn-primary btn-sm" type="button" data-cotizacion-enviar>Enviar solicitud</button><?php endif; ?>
                <?php if (in_array('ver_pedido', $acciones, true) && $idPedido !== ''): ?><a class="btn btn-light btn-sm" href="<?php echo URL_RAIZ; ?>/pedidos">Ver solicitud</a><?php endif; ?>
                <?php if (in_array('duplicar', $acciones, true)): ?><button class="btn btn-light btn-sm" type="button" data-cotizacion-duplicar>Duplicar</button><?php endif; ?>
                <?php if (in_array('cancelar', $acciones, true)): ?><button class="btn btn-light-danger btn-sm" type="button" data-cotizacion-cancelar>Cancelar</button><?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<div class="modal-backdrop is-hidden" data-cotizacion-modal>
  <section class="product-modal document-modal" role="dialog" aria-modal="true" aria-labelledby="cotizacion_modal_titulo">
    <button class="modal-close" type="button" data-close-cotizacion-modal>Cerrar</button>
    <div class="pedido-modal-header">
      <span class="badge badge-primary" data-cotizacion-modal-folio>Cotización</span>
      <h2 id="cotizacion_modal_titulo" data-cotizacion-modal-title>Detalle de cotización</h2>
      <p class="muted" data-cotizacion-modal-status></p>
    </div>
    <div data-cotizacion-modal-alert class="alert alert-warning is-hidden"></div>
    <div class="table-responsive">
      <table class="data-table pedidos-detail-table">
        <thead><tr><th>Producto</th><th>SKU</th><th>Cantidad</th><th>Precio unitario</th><th>Importe estimado</th><th>Disponibilidad</th></tr></thead>
        <tbody data-cotizacion-modal-items></tbody>
      </table>
    </div>
    <div class="summary-actions pedido-modal-actions">
      <button class="btn btn-light" type="button" data-cotizacion-modal-edit>Editar borrador</button>
      <button class="btn btn-primary" type="button" data-cotizacion-modal-send>Enviar solicitud</button>
      <button class="btn btn-light" type="button" data-cotizacion-modal-duplicate>Duplicar</button>
    </div>
  </section>
</div>



