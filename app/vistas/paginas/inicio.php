<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$nombre = !empty($cliente['nombre']) ? $cliente['nombre'] : 'Cliente';
$estatus = !empty($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$tipoCliente = !empty($cliente['tipo_cliente']) ? $cliente['tipo_cliente'] : 'distribucion';
$puedeCatalogo = SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver');
$puedeMiCatalogo = SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar');
$puedeCotizar = SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar');
?>

<section class="card dashboard-welcome">
  <div>
    <span class="badge badge-primary">Portal B2B</span>
    <h2>Hola, <?php echo htmlspecialchars($nombre); ?></h2>
    <p>Este es tu resumen comercial. Desde aqui puedes entrar a tus productos frecuentes, preparar cotizaciones y revisar el estado general de tu acceso sin cargar todo el catalogo de entrada.</p>
  </div>
  <div class="dashboard-status-box">
    <small>Estatus de cuenta</small>
    <strong><?php echo htmlspecialchars($estatus); ?></strong>
    <span><?php echo htmlspecialchars($tipoCliente); ?></span>
  </div>
</section>

<section class="metrics-grid dashboard-metrics">
  <article class="metric-card"><span class="metric-icon icon icon-star"></span><div><small>Mi catalogo</small><strong><?php echo $puedeMiCatalogo ? 'Listo' : 'Pendiente'; ?></strong><span>Productos conocidos a la mano</span></div></article>
  <article class="metric-card"><span class="metric-icon icon icon-quote"></span><div><small>Cotizaciones</small><strong><?php echo $puedeCotizar ? 'Activo' : 'En revision'; ?></strong><span>Solicitudes sujetas a validacion interna</span></div></article>
  <article class="metric-card"><span class="metric-icon icon icon-shield"></span><div><small>Alertas</small><strong>0</strong><span>Sin avisos pendientes por ahora</span></div></article>
</section>

<section class="content-grid dashboard-grid">
  <article class="card card-span-2">
    <div class="card-header">
      <div><h2>Accesos de trabajo</h2><p>Entra solo a la seccion que necesitas para evitar cargas innecesarias.</p></div>
      <span class="badge badge-info">Resumen ligero</span>
    </div>
    <div class="action-board dashboard-actions">
      <?php if ($puedeMiCatalogo): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/mi-catalogo"><span class="icon icon-star"></span><strong>Mi catalogo</strong><small>Productos frecuentes con cantidad y comentario para cotizar rapido.</small></a>
      <?php endif; ?>
      <?php if ($puedeCotizar): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/cotizacion"><span class="icon icon-quote"></span><strong>Cotizaciones</strong><small>Revisa partidas, cantidades y comentarios antes de enviar.</small></a>
      <?php endif; ?>
      <?php if ($puedeCatalogo): ?>
        <a class="action-tile" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-search"></span><strong>Catalogo completo</strong><small>Buscar productos publicados por ERP cuando lo necesites.</small></a>
      <?php endif; ?>
      <a class="action-tile" href="<?php echo URL_RAIZ; ?>/cuenta"><span class="icon icon-user"></span><strong>Mi cuenta</strong><small>Consulta tu perfil, permisos y estatus comercial.</small></a>
    </div>
  </article>

  <aside class="card alerts-panel">
    <div class="card-header"><div><h2>Alertas</h2><p>Seguimiento comercial.</p></div></div>
    <ul class="status-list">
      <li><span class="badge badge-light">Sin avisos</span><span>Cuando validemos una solicitud, apareceran aqui los cambios importantes.</span></li>
      <li><span class="badge badge-info">Proximo</span><span>Notificaciones de cotizaciones, pedidos y solicitudes por revisar.</span></li>
    </ul>
  </aside>
</section>

<section class="card dashboard-empty-state">
  <div class="card-header">
    <div><h2>Resumen operativo</h2><p>Esta seccion ira mostrando informacion conforme existan cotizaciones, pedidos y alertas conectadas.</p></div>
  </div>
  <div class="module-state-grid">
    <div class="module-state"><span class="badge badge-primary">Mi catalogo</span><strong>Productos frecuentes</strong><small>Usalo para cotizar rapido sin buscar cada SKU en el catalogo completo.</small></div>
    <div class="module-state"><span class="badge badge-info">Cotizaciones</span><strong>Revision interna</strong><small>Las solicitudes no apartan inventario ni crean pedido automatico.</small></div>
    <div class="module-state"><span class="badge badge-warning">Pedidos</span><strong>Por habilitar</strong><small>Aqui podremos mostrar pedidos validados, parciales o con informacion pendiente.</small></div>
  </div>
</section>