<?php
$contratos = isset($datos['contratos']) && is_array($datos['contratos']) ? $datos['contratos'] : array();
$pedidoFacturacion = isset($contratos['payloads']['pedido_facturacion']) ? $contratos['payloads']['pedido_facturacion'] : array();
?>
<section class="quote-page">
  <div class="card card-span-2">
    <div class="card-header">
      <div><h2>Partidas de pedido</h2><p>El ERP recalcula precio, visibilidad y disponibilidad antes de registrar.</p></div>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-plus"></span>Agregar productos</a>
    </div>
    <div id="cotizacion_alerta" class="alert alert-warning is-hidden"></div>
    <div class="table-responsive">
      <table class="data-table quote-table">
        <thead><tr><th>Producto</th><th>Cantidad</th><th>Comentario</th><th>Disponibilidad</th><th>Subtotal</th><th class="text-end">Acciones</th></tr></thead>
        <tbody id="cotizacion_items"></tbody>
      </table>
    </div>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Entrega</strong><span>El ERP valida condiciones antes de aceptar el pedido.</span></div>
      <div class="form-grid two-columns">
        <label class="field-block">Tipo de entrega
          <select id="pedido_entrega_tipo">
            <option value="por_definir">Por definir</option>
            <option value="envio">Envio</option>
            <option value="recoger_tienda">Recoger en tienda</option>
          </select>
        </label>
        <label class="field-block">Referencia de entrega
          <input id="pedido_entrega_referencia" type="text" maxlength="180" placeholder="Sucursal, ciudad o referencia">
        </label>
        <label class="field-block full">Comentarios de entrega
          <textarea id="pedido_entrega_comentarios" rows="2" placeholder="Horario, direccion tentativa o notas para revisar"></textarea>
        </label>
      </div>
    </section>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Facturacion</strong><span>Solo guarda la intencion; no genera factura automaticamente.</span></div>
      <label class="checkbox-row"><input type="checkbox" id="pedido_requiere_factura">Quiero factura</label>
      <div class="form-grid two-columns is-hidden" data-facturacion-fields>
        <label class="field-block">RFC
          <input id="facturacion_rfc" type="text" maxlength="13" autocomplete="off" placeholder="RFC">
        </label>
        <label class="field-block">Razon social
          <input id="facturacion_razon_social" type="text" maxlength="180" placeholder="Razon social">
        </label>
        <label class="field-block">Regimen fiscal
          <input id="facturacion_regimen_fiscal" type="text" maxlength="160" placeholder="Regimen fiscal SAT">
        </label>
        <label class="field-block">Uso CFDI
          <input id="facturacion_uso_cfdi" type="text" maxlength="10" placeholder="G03">
        </label>
        <label class="field-block">Codigo postal fiscal
          <input id="facturacion_codigo_postal" type="text" inputmode="numeric" maxlength="5" placeholder="00000">
        </label>
        <label class="field-block">Correo de facturacion
          <input id="facturacion_correo" type="email" maxlength="180" placeholder="facturas@dominio.com">
        </label>
        <label class="field-block full">Comentarios fiscales
          <textarea id="facturacion_comentarios" rows="2" maxlength="500" placeholder="Notas fiscales para revision interna"></textarea>
        </label>
      </div>
    </section>


    <section class="quote-form-section quote-drafts-section">
      <div class="form-section-title"><strong>Borrador ERP</strong><span>Guarda el avance para continuar despues o enviarlo como pedido.</span></div>
      <div class="quote-draft-tools">
        <label class="field-block">Nombre de cotizacion
          <input id="cotizacion_borrador_nombre" type="text" maxlength="120" placeholder="Ej. Alimento perro mediano, pedido quincenal">
        </label>
        <button class="btn btn-light" type="button" id="cotizacion_guardar_borrador">Guardar borrador</button>
      </div>
      <div class="quote-draft-list" data-quote-draft-list><div class="empty-row">Los borradores se guardan en el ERP y aparecen en Mis cotizaciones.</div></div>
    </section>
    <label class="field-block">Comentarios comerciales
      <textarea id="cotizacion_comentarios" rows="3" placeholder="Condiciones generales o notas para el equipo comercial"></textarea>
    </label>
    <script type="application/json" id="pedido_facturacion_contract"><?php echo json_encode($pedidoFacturacion, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?></script>
  </div>

  <aside class="card quote-summary">
    <div class="card-header"><div><h2>Resumen</h2><p>Validacion previa al envio.</p></div></div>
    <dl class="summary-list">
      <div><dt>Partidas</dt><dd id="cotizacion_total_items">0</dd></div>
      <div><dt>Total estimado</dt><dd id="cotizacion_subtotal">Por revisar</dd></div>
      <div><dt>Estado</dt><dd><span class="badge badge-warning" id="cotizacion_estado">Borrador</span></dd></div>
    </dl>
    <div class="summary-actions">
      <button class="btn btn-light w-100" type="button" id="cotizacion_revisar"><span class="icon icon-refresh"></span>Revisar en ERP</button>
      <button class="btn btn-primary w-100" type="button" id="cotizacion_enviar"><span class="icon icon-send"></span>Enviar pedido</button>
    </div>
    <ul class="status-list compact">
      <li><span class="badge badge-success">OK</span><span>No aparta inventario</span></li>
      <li><span class="badge badge-success">OK</span><span>No crea venta automatica</span></li>
      <li><span class="badge badge-info">ERP</span><span>Pedido y factura quedan en revision</span></li>
    </ul>
  </aside>
</section>


