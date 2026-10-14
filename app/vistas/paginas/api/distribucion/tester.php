<?php
$apiBaseUrl = isset($datos['api_base_url']) ? $datos['api_base_url'] : ERP_API_BASE_URL;
$erpUrl = isset($datos['erp_url']) ? $datos['erp_url'] : ERP_URL_RAIZ;
?>
<section class="tester-shell" data-api-base="<?php echo htmlspecialchars($apiBaseUrl); ?>" data-erp-url="<?php echo htmlspecialchars($erpUrl); ?>">
  <div class="tester-hero">
    <div>
      <span class="badge badge-primary">Diagnostico interno read-only</span>
      <h2>Prueba integral Distribucion API</h2>
      <p>Valida contratos GET reales contra el ERP y prepara payloads para pruebas manuales controladas de registro, cotizacion, pedido e inventario.</p>
    </div>
    <div class="tester-env">
      <label class="field-block">
        API ERP
        <input type="url" data-api-base-input value="<?php echo htmlspecialchars($apiBaseUrl); ?>">
      </label>
      <label class="field-block">
        Token cliente
        <input type="text" data-token-input placeholder="Token Bearer para GET autenticados">
      </label>
    </div>
  </div>

  <div class="tester-grid">
    <aside class="tester-nav" aria-label="Bloques de prueba">
      <button type="button" class="is-active" data-section-target="contratos">Contratos</button>
      <button type="button" data-section-target="registro">Registro</button>
      <button type="button" data-section-target="login">Login</button>
      <button type="button" data-section-target="perfil">Perfil</button>
      <button type="button" data-section-target="catalogo">Catalogo</button>
      <button type="button" data-section-target="mi_catalogo">Mi catalogo</button>
      <button type="button" data-section-target="cotizaciones">Cotizaciones</button>
      <button type="button" data-section-target="pedidos">Pedidos</button>
      <button type="button" data-section-target="inventario">Inventario cliente</button>
      <button type="button" data-section-target="admin">Panel ERP</button>
      <button type="button" data-section-target="diagnostico">Diagnostico</button>
    </aside>

    <div class="tester-content">
      <section class="tester-section is-active" data-section="contratos">
        <div class="card">
          <div class="card-header">
            <div><h2>Contrato general API</h2><p>Manifiesto y configuracion inicial que gobiernan el portal.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="contratos">Probar bloque</button>
          </div>
          <div class="tester-tests" data-tests="contratos"></div>
        </div>
      </section>

      <section class="tester-section" data-section="registro">
        <div class="card">
          <div class="card-header">
            <div><h2>Registro de prospecto mayorista</h2><p>Categorias de interes y payload de solicitud pendiente de aprobacion ERP.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="registro">Probar GET</button>
          </div>
          <div class="tester-category-panel">
            <div class="tester-category-actions">
              <button class="btn btn-light" type="button" data-load-categories>Cargar categorias</button>
              <span class="badge badge-light" data-category-count>0 seleccionadas</span>
            </div>
            <div class="tester-category-tree" data-category-tree></div>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /auth/registro
            <textarea data-payload-editor="registro" rows="16"></textarea>
          </label>
          <div class="tester-post-note">Este tester no envia registros para evitar escrituras accidentales en la base operativa.</div>
          <div class="tester-tests" data-tests="registro"></div>
        </div>
      </section>

      <section class="tester-section" data-section="login">
        <div class="card">
          <div class="card-header">
            <div><h2>Activacion y login</h2><p>Payloads para sesion externa y GET de perfil con token.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="login">Probar GET perfil</button>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /auth/login
            <textarea data-payload-editor="login" rows="6"></textarea>
          </label>
          <div class="tester-tests" data-tests="login"></div>
        </div>
      </section>

      <section class="tester-section" data-section="perfil">
        <div class="card">
          <div class="card-header">
            <div><h2>Perfil / Mi cuenta</h2><p>GET autenticado y payload de solicitud de cambio.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="perfil">Probar bloque</button>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /auth/perfil/solicitar_cambio
            <textarea data-payload-editor="perfil_cambio" rows="10"></textarea>
          </label>
          <div class="tester-tests" data-tests="perfil"></div>
        </div>
      </section>

      <section class="tester-section" data-section="catalogo">
        <div class="card">
          <div class="card-header">
            <div><h2>Catalogo Distribucion</h2><p>Visibilidad, precio autorizado, disponibilidad comercial y detalle por slug.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="catalogo">Probar bloque</button>
          </div>
          <div class="form-grid two-columns">
            <label class="field-block">Slug producto<input type="text" data-product-slug placeholder="slug-del-producto"></label>
            <label class="field-block">ID SKU para payloads<input type="number" data-sku-id placeholder="id_sku"></label>
          </div>
          <div class="tester-tests" data-tests="catalogo"></div>
        </div>
      </section>

      <section class="tester-section" data-section="mi_catalogo">
        <div class="card">
          <div class="card-header">
            <div><h2>Mi catalogo</h2><p>Seleccion de interes del cliente, sin compra ni apartado.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="mi_catalogo">Probar GET</button>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /mi_catalogo/guardar
            <textarea data-payload-editor="mi_catalogo" rows="8"></textarea>
          </label>
          <div class="tester-tests" data-tests="mi_catalogo"></div>
        </div>
      </section>

      <section class="tester-section" data-section="cotizaciones">
        <div class="card">
          <div class="card-header">
            <div><h2>Cotizaciones</h2><p>Listado y detalle GET; payloads de dry-run, registro y acciones.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="cotizaciones">Probar GET</button>
          </div>
          <div class="form-grid two-columns">
            <label class="field-block">ID cotizacion<input type="number" data-cotizacion-id placeholder="id_cotizacion_distribucion"></label>
            <label class="field-block">Cantidad<input type="number" data-test-cantidad value="1" min="1"></label>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /cotizacion/*
            <textarea data-payload-editor="cotizacion" rows="12"></textarea>
          </label>
          <div class="tester-tests" data-tests="cotizaciones"></div>
        </div>
      </section>

      <section class="tester-section" data-section="pedidos">
        <div class="card">
          <div class="card-header">
            <div><h2>Pedidos</h2><p>Listado y detalle GET; payloads de solicitud preliminar y respuesta.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="pedidos">Probar GET</button>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /pedido/*
            <textarea data-payload-editor="pedido" rows="12"></textarea>
          </label>
          <div class="tester-tests" data-tests="pedidos"></div>
        </div>
      </section>

      <section class="tester-section" data-section="inventario">
        <div class="card">
          <div class="card-header">
            <div><h2>Inventario cliente / sugerido</h2><p>GET de conteos y sugerido; payloads de conteo y pedido sugerido.</p></div>
            <button class="btn btn-primary" type="button" data-run-group="inventario">Probar GET</button>
          </div>
          <label class="field-block tester-textarea-label">
            Payload POST /inventario_cliente/*
            <textarea data-payload-editor="inventario" rows="10"></textarea>
          </label>
          <div class="tester-tests" data-tests="inventario"></div>
        </div>
      </section>

      <section class="tester-section" data-section="admin">
        <div class="card">
          <div class="card-header">
            <div><h2>Panel ERP / checklist administrativo</h2><p>Validacion operativa de atencion mayorista dentro del ERP.</p></div>
            <a class="btn btn-light" href="<?php echo htmlspecialchars($erpUrl); ?>/DistribucionAdmin/administracion" target="_blank" rel="noopener">Abrir panel ERP</a>
          </div>
          <div class="tester-checklist" data-admin-checklist></div>
        </div>
      </section>

      <section class="tester-section" data-section="diagnostico">
        <div class="card">
          <div class="card-header">
            <div><h2>Diagnostico por bloques</h2><p>Resumen vivo de resultados para priorizar ajustes.</p></div>
            <button class="btn btn-light" type="button" data-clear-results>Limpiar resultados</button>
          </div>
          <div class="tester-summary" data-summary></div>
        </div>
      </section>
    </div>
  </div>
</section>
<script src="<?php echo URL_RAIZ; ?>/public/assets/js/distribucion_tester.js"></script>
