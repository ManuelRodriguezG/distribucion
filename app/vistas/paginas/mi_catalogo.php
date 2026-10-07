<?php
if (!function_exists('dist_mi_catalogo_valor')) {
  function dist_mi_catalogo_valor($item, $clave, $default = '') {
    return is_array($item) && isset($item[$clave]) && $item[$clave] !== '' ? $item[$clave] : $default;
  }
}

if (!function_exists('dist_mi_catalogo_opcion_valor')) {
  function dist_mi_catalogo_opcion_valor($item, $claves) {
    if (!is_array($item)) { return ''; }
    foreach ($claves as $clave) {
      if (isset($item[$clave]) && $item[$clave] !== '') { return (string) $item[$clave]; }
    }
    return '';
  }
}

if (!function_exists('dist_mi_catalogo_categoria_texto')) {
  function dist_mi_catalogo_categoria_texto($categoria) {
    if (!is_array($categoria)) { return ''; }
    $texto = isset($categoria['ruta']) && $categoria['ruta'] ? $categoria['ruta'] : (isset($categoria['nombre']) ? $categoria['nombre'] : 'Categoría');
    if (isset($categoria['total'])) { $texto .= ' (' . intval($categoria['total']) . ')'; }
    return $texto;
  }
}

$respuesta = isset($datos['mi_catalogo']) && is_array($datos['mi_catalogo']) ? $datos['mi_catalogo'] : array();
$items = isset($respuesta['items']) && is_array($respuesta['items']) ? $respuesta['items'] : array();
$sesion = isset($respuesta['sesion']) && is_array($respuesta['sesion']) ? $respuesta['sesion'] : array();
$acciones = isset($sesion['acciones']) && is_array($sesion['acciones']) ? $sesion['acciones'] : SesionCliente::acciones();
$q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
$limiteActual = isset($_GET['limite']) ? max(1, min(300, (int) $_GET['limite'])) : 150;
$categoriaActual = isset($_GET['categoria']) ? trim((string) $_GET['categoria']) : '';
$marcaActual = isset($_GET['marca']) ? trim((string) $_GET['marca']) : '';
$categoriasApi = isset($datos['categorias']['items']) && is_array($datos['categorias']['items']) ? $datos['categorias']['items'] : array();
$marcasApi = isset($datos['marcas']['items']) && is_array($datos['marcas']['items']) ? $datos['marcas']['items'] : array();
$categoriaTextoActual = 'Todas las categorías';
foreach ($categoriasApi as $categoriaOpcion) {
  if ((string) dist_mi_catalogo_opcion_valor($categoriaOpcion, array('id', 'id_categoria', 'id_categoria_erp')) === (string) $categoriaActual) { $categoriaTextoActual = dist_mi_catalogo_categoria_texto($categoriaOpcion); break; }
}
$configurado = !array_key_exists('configurado', $respuesta) || !empty($respuesta['configurado']);
$puedePedidoPreliminar = !empty($acciones['pedido_preliminar']) || SesionCliente::tieneAccion('pedido_preliminar', 'distribucion.pedido.preliminar');
?>

<section class="card mi-catalogo-hero">
  <div>
    <span class="badge badge-primary">Productos frecuentes</span>
    <h2>Mi catálogo</h2>
    <p>Productos guardados por tu cuenta para tenerlos a la mano. No confirma venta, no reserva inventario y no modifica existencias.</p>
  </div>
  <div class="mi-catalogo-summary">
    <strong data-mi-catalogo-count><?php echo count($items); ?></strong>
    <span>Productos guardados</span>
  </div>
</section>

<div class="catalog-feedback is-hidden" data-mi-catalogo-feedback></div>

<?php if (!$configurado): ?>
  <div class="alert alert-warning">Esta función estará disponible próximamente.</div>
<?php elseif (!empty($respuesta['error'])): ?>
  <div class="alert alert-warning"><?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'No se pudo consultar Mi catálogo.'); ?></div>
<?php else: ?>
  <section class="card catalog-table-card mi-catalogo-card">
    <div class="card-header">
      <div><h2>Productos guardados</h2><p>Busca, revisa detalle o quita productos de tu lista personal.</p></div>
      <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-boxes"></span>Catálogo autorizado</a>
    </div>

    <form class="mi-catalogo-toolbar" method="get">
      <label class="field-block">Buscar en Mi catálogo
        <div class="search-control"><span class="icon icon-search"></span><input type="search" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="SKU, producto, marca o categoria"></div>
      </label>
      <label class="field-block filter-category">Categoría
        <div class="category-combobox" data-category-combobox>
          <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoriaActual); ?>" data-category-value>
          <button class="category-combobox-button" type="button" data-category-toggle><span data-category-label><?php echo htmlspecialchars($categoriaTextoActual); ?></span><span class="icon icon-filter"></span></button>
          <div class="category-combobox-menu is-hidden" data-category-menu>
            <input type="search" data-category-search placeholder="Buscar categoría" autocomplete="off">
            <div class="category-option-list">
              <button type="button" data-category-option value="">Todas las categorías</button>
              <?php foreach ($categoriasApi as $categoriaOpcion): ?>
                <?php $valorCategoría = dist_mi_catalogo_opcion_valor($categoriaOpcion, array('id', 'id_categoria', 'id_categoria_erp')); if ($valorCategoría === '') { continue; } $textoCategoría = dist_mi_catalogo_categoria_texto($categoriaOpcion); ?>
                <button type="button" data-category-option value="<?php echo htmlspecialchars($valorCategoría); ?>"><?php echo htmlspecialchars($textoCategoría); ?></button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </label>
      <label class="field-block">Marca
        <select name="marca">
          <option value="">Todas las marcas</option>
          <?php foreach ($marcasApi as $marcaOpcion): ?>
            <?php $valorMarca = dist_mi_catalogo_opcion_valor($marcaOpcion, array('id', 'id_marca', 'id_marca_erp')); if ($valorMarca === '') { continue; } $textoMarca = isset($marcaOpcion['nombre']) ? $marcaOpcion['nombre'] : (isset($marcaOpcion['marca']) ? $marcaOpcion['marca'] : 'Marca'); ?>
            <option value="<?php echo htmlspecialchars($valorMarca); ?>" <?php echo (string) $marcaActual === (string) $valorMarca ? 'selected' : ''; ?>><?php echo htmlspecialchars($textoMarca); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="field-block mi-catalogo-limit">Mostrar
        <select name="limite">
          <?php foreach (array(50, 100, 150, 300) as $limite): ?>
            <option value="<?php echo $limite; ?>" <?php echo $limiteActual === $limite ? 'selected' : ''; ?>><?php echo $limite; ?> productos</option>
          <?php endforeach; ?>
        </select>
      </label>
      <div class="filter-actions">
        <button class="btn btn-primary" type="submit">Buscar</button>
        <?php if ($q !== '' || $categoriaActual !== '' || $marcaActual !== '' || isset($_GET['limite'])): ?><a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/mi-catalogo">Limpiar</a><?php endif; ?>
      </div>
    </form>

    <div class="table-responsive">
      <table class="data-table mi-catalogo-table">
        <thead><tr><th>Producto</th><th>Categoría</th><th>Marca</th><th>Presentacion</th><th>Cantidad</th><th>Comentario</th><th class="text-end">Acciones</th></tr></thead>
        <tbody data-mi-catalogo-list>
          <?php if (empty($items)): ?><tr><td colspan="7" class="empty-row">Aun no tienes productos guardados en Mi catálogo.</td></tr><?php endif; ?>
          <?php foreach ($items as $item): ?>
            <?php
              $idSku = dist_mi_catalogo_valor($item, 'id_sku');
              $nombre = dist_mi_catalogo_valor($item, 'nombre_sku', dist_mi_catalogo_valor($item, 'producto', 'Producto'));
              $sku = dist_mi_catalogo_valor($item, 'sku', 'SKU');
              $slug = dist_mi_catalogo_valor($item, 'slug');
              $imagen = dist_mi_catalogo_valor($item, 'imagen_principal');
              $categoria = dist_mi_catalogo_valor($item, 'categoria', 'Sin categoría');
              $marca = dist_mi_catalogo_valor($item, 'marca', 'Sin marca');
              $presentacion = dist_mi_catalogo_valor($item, 'presentacion', 'Unidad');
            ?>
            <tr data-mi-catalogo-row data-id-sku="<?php echo htmlspecialchars($idSku); ?>">
              <td><div class="product-cell"><?php if ($imagen !== ''): ?><img src="<?php echo htmlspecialchars($imagen); ?>" alt="<?php echo htmlspecialchars($nombre); ?>"><?php else: ?><span class="thumb-placeholder icon icon-boxes"></span><?php endif; ?><div><strong><?php echo htmlspecialchars($nombre); ?></strong><small><?php echo htmlspecialchars($sku); ?></small></div></div></td>
              <td class="catalog-category-cell"><?php echo htmlspecialchars($categoria); ?></td>
              <td><?php echo htmlspecialchars($marca); ?></td>
              <td><?php echo htmlspecialchars($presentacion); ?></td>
              <td><input class="quote-inline-qty" type="number" min="1" step="1" value="1" data-mi-cotizacion-cantidad></td>
              <td><input class="quote-inline-comment" type="text" maxlength="240" placeholder="Nota para cotizar" data-mi-cotizacion-comentario></td>
              <td class="text-end table-actions action-text-buttons">
                <button class="btn btn-light btn-sm" type="button"
                  data-open-mi-catalogo-modal
                  data-id-sku="<?php echo htmlspecialchars($idSku); ?>"
                  data-sku="<?php echo htmlspecialchars($sku); ?>"
                  data-nombre="<?php echo htmlspecialchars($nombre); ?>"
                  data-categoria="<?php echo htmlspecialchars($categoria); ?>"
                  data-marca="<?php echo htmlspecialchars($marca); ?>"
                  data-presentacion="<?php echo htmlspecialchars($presentacion); ?>"
                  data-imagen="<?php echo htmlspecialchars($imagen); ?>"
                  data-slug="<?php echo htmlspecialchars($slug); ?>">Ver detalle</button>
                <?php if ($puedePedidoPreliminar): ?><button class="btn btn-primary btn-sm" type="button" data-add-cotizacion data-from-mi-catalogo data-id-sku="<?php echo htmlspecialchars($idSku); ?>" data-nombre="<?php echo htmlspecialchars($nombre); ?>">Enviar a cotización</button><?php endif; ?>
                <button class="btn btn-light-danger btn-sm" type="button" data-mi-catalogo-toggle data-id-sku="<?php echo htmlspecialchars($idSku); ?>" data-estatus="inactivo">Quitar de Mi catálogo</button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <div class="modal-backdrop is-hidden" data-mi-catalogo-modal>
    <div class="product-modal mi-catalogo-modal" role="dialog" aria-modal="true" aria-labelledby="mi_catalogo_modal_titulo">
      <button class="modal-close" type="button" data-close-mi-catalogo-modal>Cerrar</button>
      <div class="product-modal-grid">
        <div class="product-modal-media" data-mi-modal-image><span class="icon icon-boxes"></span></div>
        <div>
          <span class="badge badge-primary" data-mi-modal-sku></span>
          <h2 id="mi_catalogo_modal_titulo" data-mi-modal-title>Producto</h2>
          <div class="info-strip">
            <div><small>Categoría</small><strong data-mi-modal-category></strong></div>
            <div><small>Marca</small><strong data-mi-modal-brand></strong></div>
            <div><small>Presentacion</small><strong data-mi-modal-presentation></strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>