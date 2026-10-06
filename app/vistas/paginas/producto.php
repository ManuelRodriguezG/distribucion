<?php
if (!function_exists('dist_producto_estado_badge')) {
  function dist_producto_estado_badge($estado) {
    $mapa = array('disponible' => 'badge-success', 'pocas_piezas' => 'badge-warning', 'bajo_pedido' => 'badge-info', 'consultar_disponibilidad' => 'badge-light', 'no_disponible' => 'badge-danger');
    return isset($mapa[$estado]) ? $mapa[$estado] : 'badge-light';
  }
}
if (!function_exists('dist_producto_precio')) {
  function dist_producto_precio($precio) {
    if (is_array($precio) && !empty($precio['visible']) && isset($precio['monto'])) {
      $moneda = isset($precio['moneda']) ? $precio['moneda'] : 'MXN';
      return '$' . number_format((float) $precio['monto'], 2) . ' ' . htmlspecialchars($moneda);
    }
    return htmlspecialchars(is_array($precio) && isset($precio['mensaje']) ? $precio['mensaje'] : 'Solicitar precio');
  }
}
$respuesta = isset($datos['producto']) && is_array($datos['producto']) ? $datos['producto'] : array();
$item = isset($respuesta['item']) && is_array($respuesta['item']) ? $respuesta['item'] : array();
$precio = isset($item['precio']) && is_array($item['precio']) ? $item['precio'] : array();
$disponibilidad = isset($item['disponibilidad']) && is_array($item['disponibilidad']) ? $item['disponibilidad'] : array();
$acciones = isset($item['acciones']) && is_array($item['acciones']) ? $item['acciones'] : array();
$miCatalogo = isset($datos['mi_catalogo']) && is_array($datos['mi_catalogo']) ? $datos['mi_catalogo'] : array('items' => array());
$miCatalogoItems = isset($miCatalogo['items']) && is_array($miCatalogo['items']) ? $miCatalogo['items'] : array();
$miCatalogoIds = array();
foreach ($miCatalogoItems as $miCatalogoItem) { if (isset($miCatalogoItem['id_sku'])) { $miCatalogoIds[(string) $miCatalogoItem['id_sku']] = true; } }
$idSkuActual = isset($item['id_sku']) ? (string) $item['id_sku'] : '';
$puedeMiCatalogo = SesionCliente::tieneAccion('gestionar_mi_catalogo', 'distribucion.mi_catalogo.gestionar');
$enMiCatalogo = $idSkuActual !== '' && isset($miCatalogoIds[$idSkuActual]);
$imagenes = array();
if (!empty($item['imagenes']) && is_array($item['imagenes'])) { $imagenes = $item['imagenes']; }
if (empty($imagenes) && !empty($item['imagen_principal'])) { $imagenes[] = $item['imagen_principal']; }
$atributos = isset($item['atributos']) && is_array($item['atributos']) ? $item['atributos'] : array();
$variantes = isset($item['variantes']) && is_array($item['variantes']) ? $item['variantes'] : array();
$paquetes = isset($item['paquetes']) && is_array($item['paquetes']) ? $item['paquetes'] : array();
$estado = isset($disponibilidad['estado']) ? $disponibilidad['estado'] : 'consultar_disponibilidad';
?>

<div class="catalog-feedback is-hidden" data-mi-catalogo-feedback></div>

<?php if (!empty($respuesta['error'])): ?>
  <div class="alert alert-warning"><?php echo htmlspecialchars($respuesta['mensaje']); ?></div>
<?php elseif (empty($item)): ?>
  <section class="card"><div class="empty-row">Producto no disponible para Distribucion.</div></section>
<?php else: ?>
  <section class="product-detail-shell product-detail-final">
    <article class="card media-panel">
      <div class="product-detail-image-wrap">
        <?php if (!empty($imagenes)): ?><img class="product-detail-image" src="<?php echo htmlspecialchars($imagenes[0]); ?>" alt="<?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?>"><?php else: ?><div class="product-detail-image placeholder"><span class="icon icon-boxes"></span></div><?php endif; ?>
      </div>
      <div class="thumbnail-row">
        <?php if (count($imagenes) <= 1): ?><span class="thumbnail-empty">Galeria pendiente de API</span><?php endif; ?>
        <?php foreach (array_slice($imagenes, 0, 4) as $imagen): ?><img src="<?php echo htmlspecialchars($imagen); ?>" alt="Imagen de producto"><?php endforeach; ?>
      </div>
    </article>

    <article class="card product-info-panel">
      <div class="product-tags"><span class="badge badge-light"><?php echo htmlspecialchars(isset($item['sku']) ? $item['sku'] : 'SKU'); ?></span><span class="badge <?php echo dist_producto_estado_badge($estado); ?>"><?php echo htmlspecialchars($estado); ?></span></div>
      <h2><?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?></h2>
      <p class="muted"><?php echo htmlspecialchars(trim((isset($item['marca']) && $item['marca'] ? $item['marca'] : 'Sin marca') . ' / ' . (isset($item['categoria']) ? $item['categoria'] : 'Sin categoria visible'), ' /')); ?></p>
      <div class="detail-price"><?php echo dist_producto_precio($precio); ?></div>
      <div class="info-strip"><div><small>Disponibilidad</small><strong><?php echo htmlspecialchars(isset($disponibilidad['mensaje']) ? $disponibilidad['mensaje'] : 'Consultar disponibilidad'); ?></strong></div><div><small>Presentacion</small><strong><?php echo htmlspecialchars(isset($item['presentacion']) ? $item['presentacion'] : 'Unidad'); ?></strong></div><div><small>Marca</small><strong><?php echo htmlspecialchars(isset($item['marca']) && $item['marca'] ? $item['marca'] : 'Sin marca'); ?></strong></div><div><small>Categoria</small><strong><?php echo htmlspecialchars(isset($item['categoria']) ? $item['categoria'] : 'Catalogo'); ?></strong></div></div>
      <div class="actions">
        <?php if ($puedeMiCatalogo && $idSkuActual !== ''): ?><button class="btn btn-light mi-catalogo-toggle <?php echo $enMiCatalogo ? 'is-saved' : ''; ?>" type="button" data-mi-catalogo-toggle data-id-sku="<?php echo htmlspecialchars($idSkuActual); ?>" data-estatus="<?php echo $enMiCatalogo ? 'inactivo' : 'activo'; ?>"><?php echo $enMiCatalogo ? 'En Mi catálogo' : 'Agregar a Mi catálogo'; ?></button><?php endif; ?>
        <?php if (!empty($acciones['agregar_cotizacion'])): ?><button class="btn btn-primary" type="button" data-add-cotizacion data-id-sku="<?php echo htmlspecialchars(isset($item['id_sku']) ? $item['id_sku'] : ''); ?>" data-nombre="<?php echo htmlspecialchars(isset($item['nombre']) ? $item['nombre'] : 'Producto'); ?>"><span class="icon icon-plus"></span>Agregar a cotizacion</button><?php elseif (!empty($acciones['solicitar_precio'])): ?><a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/auth/registro"><span class="icon icon-tag"></span>Solicitar precio</a><?php endif; ?>
        <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-arrow-left"></span>Volver al catalogo</a>
      </div>
    </article>
  </section>

  <section class="product-tabs card">
    <div class="tab-bar"><button class="is-active" type="button" data-product-tab="atributos">Atributos</button><button type="button" data-product-tab="variantes">Variantes</button><button type="button" data-product-tab="paquetes">Paquetes</button><button type="button" data-product-tab="condiciones">Condiciones</button></div>
    <div class="tab-panel" data-product-panel="atributos">
      <div class="key-value-list">
        <?php if (empty($atributos)): ?><div class="empty-row">Sin atributos adicionales visibles para este producto.</div><?php endif; ?>
        <?php foreach ($atributos as $clave => $valor): ?><?php $atributoNombre = is_string($clave) ? $clave : (is_array($valor) && isset($valor['nombre']) ? $valor['nombre'] : 'Atributo'); $atributoValor = is_array($valor) ? (isset($valor['valor']) ? $valor['valor'] : json_encode($valor)) : $valor; ?><div><span><?php echo htmlspecialchars($atributoNombre); ?></span><strong><?php echo htmlspecialchars($atributoValor); ?></strong></div><?php endforeach; ?>
      </div>
    </div>
    <div class="tab-panel is-hidden" data-product-panel="variantes"><div class="key-value-list"><?php if (empty($variantes)): ?><div class="empty-row">Sin variantes visibles para este producto.</div><?php endif; ?><?php foreach ($variantes as $variante): ?><div><span><?php echo htmlspecialchars(isset($variante['sku']) ? $variante['sku'] : 'Variante'); ?></span><strong><?php echo htmlspecialchars(isset($variante['nombre']) ? $variante['nombre'] : json_encode($variante)); ?></strong></div><?php endforeach; ?></div></div>
    <div class="tab-panel is-hidden" data-product-panel="paquetes"><div class="key-value-list"><?php if (empty($paquetes)): ?><div class="empty-row">Sin paquetes relacionados visibles para este producto.</div><?php endif; ?><?php foreach ($paquetes as $paquete): ?><div><span><?php echo htmlspecialchars(isset($paquete['sku']) ? $paquete['sku'] : 'Paquete'); ?></span><strong><?php echo htmlspecialchars(isset($paquete['nombre']) ? $paquete['nombre'] : json_encode($paquete)); ?></strong></div><?php endforeach; ?></div></div>
    <div class="tab-panel is-hidden" data-product-panel="condiciones"><ul class="status-list"><li><span class="badge badge-success">OK</span><span>Informacion comercial visible para tu cuenta.</span></li><li><span class="badge badge-info">ERP</span><span>Precio y disponibilidad sujetos a autorizacion comercial.</span></li><li><span class="badge badge-warning">MVP</span><span>Cotizacion disponible cuando tu cuenta lo tenga habilitado.</span></li></ul></div>
  </section>
<?php endif; ?>
