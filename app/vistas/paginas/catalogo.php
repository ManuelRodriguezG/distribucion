<?php
if (!function_exists('dist_catalogo_estado_badge')) {
  function dist_catalogo_estado_badge($estado) {
    $mapa = array('disponible' => 'badge-success', 'pocas_piezas' => 'badge-warning', 'bajo_pedido' => 'badge-info', 'consultar_disponibilidad' => 'badge-light', 'no_disponible' => 'badge-danger');
    return isset($mapa[$estado]) ? $mapa[$estado] : 'badge-light';
  }
}
if (!function_exists('dist_catalogo_estado_label')) {
  function dist_catalogo_estado_label($estado) {
    $mapa = array('disponible' => 'Disponible', 'pocas_piezas' => 'Disponibilidad limitada', 'bajo_pedido' => 'Bajo pedido', 'consultar_disponibilidad' => 'Consultar disponibilidad', 'no_disponible' => 'No disponible', 'por_confirmar' => 'Por confirmar');
    return isset($mapa[$estado]) ? $mapa[$estado] : ucfirst(str_replace('_', ' ', (string) $estado));
  }
}
if (!function_exists('dist_catalogo_orden_label')) {
  function dist_catalogo_orden_label($orden) {
    $mapa = array('relevancia' => 'Relevancia', 'nombre' => 'Nombre', 'marca' => 'Marca');
    return isset($mapa[$orden]) ? $mapa[$orden] : ucfirst(str_replace('_', ' ', $orden));
  }
}
if (!function_exists('dist_catalogo_precio')) {
  function dist_catalogo_precio($precio) {
    if (is_array($precio) && !empty($precio['visible']) && isset($precio['monto'])) {
      $moneda = isset($precio['moneda']) ? $precio['moneda'] : 'MXN';
      return '$' . number_format((float) $precio['monto'], 2) . ' ' . htmlspecialchars($moneda);
    }
    return htmlspecialchars(is_array($precio) && isset($precio['mensaje']) ? $precio['mensaje'] : 'Solicitar precio');
  }
}
if (!function_exists('dist_catalogo_categoria_valor')) {
  function dist_catalogo_categoria_valor($categoria) {
    if (!is_array($categoria)) { return ''; }
    if (isset($categoria['id'])) { return (string) $categoria['id']; }
    if (isset($categoria['id_categoria'])) { return (string) $categoria['id_categoria']; }
    if (isset($categoria['id_categoria_erp'])) { return (string) $categoria['id_categoria_erp']; }
    return '';
  }
}
if (!function_exists('dist_catalogo_categoria_texto')) {
  function dist_catalogo_categoria_texto($categoria) {
    if (!is_array($categoria)) { return ''; }
    $texto = isset($categoria['ruta']) && $categoria['ruta'] ? $categoria['ruta'] : (isset($categoria['nombre']) ? $categoria['nombre'] : 'Categoria');
    if (isset($categoria['total'])) { $texto .= ' (' . intval($categoria['total']) . ')'; }
    return $texto;
  }
}

if (!function_exists('dist_catalogo_url_pagina')) {
  function dist_catalogo_url_pagina($pagina, $limite = null) {
    $query = $_GET;
    unset($query['url']);
    $query['pagina'] = max(1, intval($pagina));
    if ($limite !== null) { $query['limite'] = max(1, intval($limite)); }
    return URL_RAIZ . '/catalogo' . (!empty($query) ? '?' . http_build_query($query) : '');
  }
}
if (!function_exists('dist_catalogo_paginas_visibles')) {
  function dist_catalogo_paginas_visibles($paginaActual, $totalPaginas) {
    $paginaActual = max(1, intval($paginaActual));
    $totalPaginas = max(1, intval($totalPaginas));
    $inicio = max(1, $paginaActual - 2);
    $fin = min($totalPaginas, $paginaActual + 2);
    if ($paginaActual <= 2) { $fin = min($totalPaginas, 5); }
    if ($paginaActual >= $totalPaginas - 1) { $inicio = max(1, $totalPaginas - 4); }
    return range($inicio, $fin);
  }
}
$catalogo = isset($datos['catalogo']) && is_array($datos['catalogo']) ? $datos['catalogo'] : array();
$items = isset($catalogo['items']) && is_array($catalogo['items']) ? $catalogo['items'] : array();
$categoriasApi = isset($datos['categorias']['items']) && is_array($datos['categorias']['items']) ? $datos['categorias']['items'] : array();
$filtros = isset($datos['filtros']) && is_array($datos['filtros']) ? $datos['filtros'] : array();
$ordenamientos = isset($filtros['ordenamientos']) && is_array($filtros['ordenamientos']) ? $filtros['ordenamientos'] : array('relevancia', 'nombre', 'marca');
$paginacion = isset($catalogo['paginacion']) && is_array($catalogo['paginacion']) ? $catalogo['paginacion'] : array();
$paginaActual = max(1, intval(isset($paginacion['pagina']) ? $paginacion['pagina'] : (isset($_GET['pagina']) ? $_GET['pagina'] : 1)));
$limiteActual = max(1, intval(isset($paginacion['limite']) ? $paginacion['limite'] : (isset($_GET['limite']) ? $_GET['limite'] : 24)));
$totalResultados = max(0, intval(isset($paginacion['total']) ? $paginacion['total'] : count($items)));
$totalPaginas = max(1, (int) ceil($totalResultados / max(1, $limiteActual)));
$sesion = isset($catalogo['sesion']) && is_array($catalogo['sesion']) ? $catalogo['sesion'] : array();
$q = isset($_GET['q']) ? $_GET['q'] : '';
$categoriaActual = isset($_GET['categoria']) ? $_GET['categoria'] : (isset($catalogo['filtros_aplicados']['categoria']) ? (string) $catalogo['filtros_aplicados']['categoria'] : '');
$precioMin = isset($_GET['precio_min']) ? $_GET['precio_min'] : (isset($catalogo['filtros_aplicados']['precio_min']) ? $catalogo['filtros_aplicados']['precio_min'] : '');
$precioMax = isset($_GET['precio_max']) ? $_GET['precio_max'] : (isset($catalogo['filtros_aplicados']['precio_max']) ? $catalogo['filtros_aplicados']['precio_max'] : '');
$ordenActual = isset($_GET['orden']) ? $_GET['orden'] : (isset($catalogo['filtros_aplicados']['orden']) ? $catalogo['filtros_aplicados']['orden'] : 'relevancia');
$accionesSesion = isset($sesion['acciones']) && is_array($sesion['acciones']) ? $sesion['acciones'] : array();
$puedeVerPrecio = !empty($accionesSesion['ver_precio']);
$puedeMiCatalogo = !empty($accionesSesion['gestionar_mi_catalogo']) || SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar');
$miCatalogo = isset($datos['mi_catalogo']) && is_array($datos['mi_catalogo']) ? $datos['mi_catalogo'] : array('items' => array());
$miCatalogoItems = isset($miCatalogo['items']) && is_array($miCatalogo['items']) ? $miCatalogo['items'] : array();
$miCatalogoIds = array();
foreach ($miCatalogoItems as $miCatalogoItem) { if (isset($miCatalogoItem['id_sku'])) { $miCatalogoIds[(string) $miCatalogoItem['id_sku']] = true; } }
$catalogoIndiceItems = $items;
$filtrosActivos = trim($q) !== '' || intval($categoriaActual) > 0 || trim((string) $precioMin) !== '' || trim((string) $precioMax) !== '' || $ordenActual !== 'relevancia';
$categoriaTextoActual = 'Todas las categorias';
foreach ($categoriasApi as $categoria) {
  if ((string) dist_catalogo_categoria_valor($categoria) === (string) $categoriaActual) { $categoriaTextoActual = dist_catalogo_categoria_texto($categoria); break; }
}
?>

<section class="catalog-filter-bar card">
  <div class="filter-bar-heading">
    <div><span class="badge badge-primary">Catálogo autorizado</span><h2>Búsqueda y filtros</h2><p>Encuentra productos por nombre, SKU, marca o categoría.</p></div>
    <div class="filter-summary"><span><?php echo (int) (isset($paginacion['total']) ? $paginacion['total'] : count($items)); ?> resultados</span><span><?php echo $puedeVerPrecio ? 'Precios según autorización' : 'Precios por confirmar'; ?></span></div>
  </div>

  <form class="catalog-top-filters" method="get">
    <input type="hidden" name="pagina" value="1">
    <label class="field-block filter-search">Buscar
      <div class="search-control"><span class="icon icon-search"></span><input type="search" name="q" placeholder="Producto, SKU o marca" value="<?php echo htmlspecialchars($q); ?>"></div>
    </label>

    <label class="field-block filter-category">Categoria
      <div class="category-combobox" data-category-combobox>
        <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoriaActual); ?>" data-category-value>
        <button class="category-combobox-button" type="button" data-category-toggle><span data-category-label><?php echo htmlspecialchars($categoriaTextoActual); ?></span><span class="icon icon-filter"></span></button>
        <div class="category-combobox-menu is-hidden" data-category-menu>
          <input type="search" data-category-search placeholder="Buscar categoria" autocomplete="off">
          <div class="category-option-list">
            <button type="button" data-category-option value="">Todas las categorias</button>
            <?php foreach ($categoriasApi as $categoria): ?>
              <?php $valorCategoria = dist_catalogo_categoria_valor($categoria); if ($valorCategoria === '') { continue; } $textoCategoria = dist_catalogo_categoria_texto($categoria); ?>
              <button type="button" data-category-option value="<?php echo htmlspecialchars($valorCategoria); ?>"><?php echo htmlspecialchars($textoCategoria); ?></button>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </label>

    <div class="price-filter-group">
      <label class="field-block">Precio min<input type="number" name="precio_min" min="0" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($precioMin); ?>" <?php echo $puedeVerPrecio ? '' : 'disabled'; ?>></label>
      <label class="field-block">Precio max<input type="number" name="precio_max" min="0" step="0.01" placeholder="0.00" value="<?php echo htmlspecialchars($precioMax); ?>" <?php echo $puedeVerPrecio ? '' : 'disabled'; ?>></label>
    </div>

    <label class="field-block filter-limit">Mostrar
      <select name="limite">
        <?php foreach (array(24, 40, 60) as $limiteOpcion): ?><option value="<?php echo $limiteOpcion; ?>" <?php echo intval($limiteActual) === intval($limiteOpcion) ? 'selected' : ''; ?>><?php echo $limiteOpcion; ?> por pagina</option><?php endforeach; ?>
      </select>
    </label>
    <label class="field-block filter-order">Ordenar
      <select name="orden"><?php foreach ($ordenamientos as $orden): ?><option value="<?php echo htmlspecialchars($orden); ?>" <?php echo (string) $ordenActual === (string) $orden ? 'selected' : ''; ?>><?php echo htmlspecialchars(dist_catalogo_orden_label($orden)); ?></option><?php endforeach; ?></select>
    </label>

    <div class="filter-actions"><button class="btn btn-primary" type="submit">Aplicar filtros</button><?php if ($filtrosActivos): ?><a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/catalogo">Limpiar</a><?php endif; ?></div>
  </form>
  <?php if (!$puedeVerPrecio): ?><div class="filter-inline-note">El filtro de precio se habilita cuando tu cuenta tenga precios disponibles.</div><?php endif; ?>
</section>

<div class="catalog-feedback is-hidden" data-mi-catalogo-feedback></div>
<script type="application/json" id="catalogo_indice_json"><?php echo json_encode(array("items" => $catalogoIndiceItems, "mi_catalogo_ids" => array_keys($miCatalogoIds), "puede_mi_catalogo" => $puedeMiCatalogo, "url_raiz" => URL_RAIZ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?></script>

<?php if (!empty($catalogo['error'])): ?>
  <div class="alert alert-warning"><?php echo htmlspecialchars($catalogo['mensaje']); ?></div>
<?php else: ?>
  <section class="card catalog-table-card">
    <div class="card-header">
      <div><h2>Tabla de productos</h2><p>Vista rápida para comparar productos, precios y disponibilidad.</p></div>
      <div class="header-tools"><span class="badge <?php echo $puedeVerPrecio ? 'badge-success' : 'badge-warning'; ?>"><?php echo $puedeVerPrecio ? 'Precio disponible' : 'Solicitar precio'; ?></span><div class="segmented" role="tablist" aria-label="Modo de vista"><button class="is-active" type="button" data-view-mode="table">Tabla</button><button type="button" data-view-mode="cards">Tarjetas</button></div></div>
    </div>

    <div class="table-local-tools">
      <label class="field-block">Buscar en productos cargados
        <div class="search-control"><span class="icon icon-search"></span><input type="search" data-table-local-search placeholder="SKU, producto, marca, categoria o precio"></div>
      </label>
      <div class="table-search-actions"><span data-local-filter-count><?php echo count($items); ?> visibles</span><span data-catalog-filter-status>Búsqueda en esta página</span></div>
    </div>

    <div class="table-responsive catalog-view catalog-table-view">
      <table class="data-table catalog-wide-table">
        <thead><tr><th>Producto</th><th>Categoria</th><th>SKU</th><th>Marca</th><th>Presentación</th><th>Precio</th><th>Disponibilidad</th><th class="text-end">Acciones</th></tr></thead>
        <tbody data-catalog-table-body>
          <?php if (empty($items)): ?><tr><td colspan="8" class="empty-row">Tu catálogo está siendo configurado. Si necesitas algún producto, contáctanos.</td></tr><?php endif; ?>
          <tr class="is-hidden" data-local-empty-row><td colspan="8" class="empty-row">Sin coincidencias en los productos cargados.</td></tr>
          <?php foreach ($items as $item): ?>
            <?php $precio = isset($item['precio']) && is_array($item['precio']) ? $item['precio'] : array(); $disponibilidad = isset($item['disponibilidad']) && is_array($item['disponibilidad']) ? $item['disponibilidad'] : array(); $acciones = isset($item['acciones']) && is_array($item['acciones']) ? $item['acciones'] : array(); $estado = isset($disponibilidad['estado']) ? $disponibilidad['estado'] : 'consultar_disponibilidad'; $precioTexto = dist_catalogo_precio($precio); $idSkuActual = isset($item['id_sku']) ? (string) $item['id_sku'] : ''; $enMiCatalogo = $idSkuActual !== '' && isset($miCatalogoIds[$idSkuActual]); ?>
            <tr data-catalog-row data-id-sku="<?php echo htmlspecialchars($idSkuActual); ?>">
              <td><div class="product-cell"><?php if (!empty($item['imagen_principal'])): ?><img src="<?php echo htmlspecialchars($item['imagen_principal']); ?>" alt="<?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?>" loading="lazy" decoding="async"><?php else: ?><span class="thumb-placeholder icon icon-boxes"></span><?php endif; ?><div><strong><?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?></strong><small><?php echo htmlspecialchars(isset($item['sku']) ? $item['sku'] : 'SKU'); ?></small></div></div></td>
              <td class="catalog-category-cell"><?php echo htmlspecialchars(isset($item['categoria']) && $item['categoria'] ? $item['categoria'] : 'Sin categoría'); ?></td>
              <td><span class="badge badge-light"><?php echo htmlspecialchars(isset($item['sku']) ? $item['sku'] : ''); ?></span></td>
              <td><?php echo htmlspecialchars(isset($item['marca']) && $item['marca'] ? $item['marca'] : 'Sin marca'); ?></td>
              <td><?php echo htmlspecialchars(isset($item['presentacion']) ? $item['presentacion'] : 'Unidad'); ?></td>
              <td class="fw-bold"><?php echo $precioTexto; ?></td>
              <td><span class="badge <?php echo dist_catalogo_estado_badge($estado); ?>"><?php echo htmlspecialchars(dist_catalogo_estado_label($estado)); ?></span><small class="line-muted"><?php echo htmlspecialchars(isset($disponibilidad['mensaje']) ? $disponibilidad['mensaje'] : 'Consultar disponibilidad'); ?></small></td>
              <td class="text-end table-actions action-text-buttons"><button class="btn btn-light btn-sm" type="button" data-open-product-modal data-nombre="<?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?>" data-sku="<?php echo htmlspecialchars(isset($item['sku']) ? $item['sku'] : ''); ?>" data-categoria="<?php echo htmlspecialchars(isset($item['categoria']) ? $item['categoria'] : ''); ?>" data-marca="<?php echo htmlspecialchars(isset($item['marca']) ? $item['marca'] : 'Sin marca'); ?>" data-presentacion="<?php echo htmlspecialchars(isset($item['presentacion']) ? $item['presentacion'] : 'Unidad'); ?>" data-precio="<?php echo htmlspecialchars(strip_tags($precioTexto)); ?>" data-disponibilidad="<?php echo htmlspecialchars(isset($disponibilidad['mensaje']) ? $disponibilidad['mensaje'] : $estado); ?>" data-imagen="<?php echo htmlspecialchars(isset($item['imagen_principal']) ? $item['imagen_principal'] : ''); ?>" data-slug="<?php echo htmlspecialchars(isset($item['slug']) ? $item['slug'] : ''); ?>">Ver detalle</button><?php if ($puedeMiCatalogo && $idSkuActual !== ''): ?><button class="btn btn-light btn-sm mi-catalogo-toggle <?php echo $enMiCatalogo ? 'is-saved' : ''; ?>" type="button" data-mi-catalogo-toggle data-id-sku="<?php echo htmlspecialchars($idSkuActual); ?>" data-estatus="<?php echo $enMiCatalogo ? 'inactivo' : 'activo'; ?>"><?php echo $enMiCatalogo ? 'En Mi catálogo' : 'Agregar a Mi catálogo'; ?></button><?php endif; ?><?php if (!empty($acciones['agregar_cotizacion'])): ?><button class="btn btn-primary btn-sm" type="button" data-add-cotizacion data-id-sku="<?php echo htmlspecialchars(isset($item['id_sku']) ? $item['id_sku'] : ''); ?>" data-nombre="<?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?>">Agregar a cotización</button><?php elseif (!empty($acciones['solicitar_precio'])): ?><a class="btn btn-light btn-sm" href="<?php echo URL_RAIZ; ?>/auth/registro">Solicitar precio</a><?php endif; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="catalog-view catalog-card-view is-hidden" data-catalog-card-list></div>

        <?php if (!empty($paginacion)): ?>
      <div class="pagination-strip catalog-pagination">
        <div class="pagination-summary">
          <span>Página <?php echo htmlspecialchars($paginaActual); ?> de <?php echo htmlspecialchars($totalPaginas); ?></span>
          <span><?php echo htmlspecialchars($limiteActual); ?> por pagina</span>
          <span><?php echo htmlspecialchars($totalResultados); ?> productos</span>
        </div>
        <nav class="pagination-controls" aria-label="Paginación de catálogo">
          <a class="page-link <?php echo $paginaActual <= 1 ? 'is-disabled' : ''; ?>" href="<?php echo $paginaActual <= 1 ? '#' : htmlspecialchars(dist_catalogo_url_pagina(1)); ?>">Primera</a>
          <a class="page-link <?php echo $paginaActual <= 1 ? 'is-disabled' : ''; ?>" href="<?php echo $paginaActual <= 1 ? '#' : htmlspecialchars(dist_catalogo_url_pagina($paginaActual - 1)); ?>">Anterior</a>
          <?php foreach (dist_catalogo_paginas_visibles($paginaActual, $totalPaginas) as $paginaVisible): ?>
            <a class="page-link <?php echo intval($paginaVisible) === intval($paginaActual) ? 'is-active' : ''; ?>" href="<?php echo htmlspecialchars(dist_catalogo_url_pagina($paginaVisible)); ?>"><?php echo htmlspecialchars($paginaVisible); ?></a>
          <?php endforeach; ?>
          <a class="page-link <?php echo $paginaActual >= $totalPaginas ? 'is-disabled' : ''; ?>" href="<?php echo $paginaActual >= $totalPaginas ? '#' : htmlspecialchars(dist_catalogo_url_pagina($paginaActual + 1)); ?>">Siguiente</a>
          <a class="page-link <?php echo $paginaActual >= $totalPaginas ? 'is-disabled' : ''; ?>" href="<?php echo $paginaActual >= $totalPaginas ? '#' : htmlspecialchars(dist_catalogo_url_pagina($totalPaginas)); ?>">Última</a>
        </nav>
      </div>
    <?php endif; ?>
  </section>
<?php endif; ?>

<div class="modal-backdrop is-hidden" data-product-modal>
  <section class="product-modal" role="dialog" aria-modal="true" aria-labelledby="product_modal_title">
    <button class="modal-close" type="button" data-close-product-modal>Cerrar</button>
    <div class="product-modal-grid"><div class="product-modal-media" data-modal-image></div><div><span class="badge badge-primary" data-modal-sku></span><h2 id="product_modal_title" data-modal-title></h2><p class="muted" data-modal-category></p><div class="info-strip"><div><small>Marca</small><strong data-modal-brand></strong></div><div><small>Presentación</small><strong data-modal-presentation></strong></div><div><small>Precio</small><strong data-modal-price></strong></div><div><small>Disponibilidad</small><strong data-modal-availability></strong></div></div><div class="actions"><a class="btn btn-light" data-modal-full-link href="#">Abrir ficha completa</a></div></div></div>
  </section>
</div>




