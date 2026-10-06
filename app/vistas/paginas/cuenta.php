<?php
$cliente = isset($datos['cliente']) && is_array($datos['cliente']) ? $datos['cliente'] : array();
$respuesta = isset($datos['respuesta']) && is_array($datos['respuesta']) ? $datos['respuesta'] : null;
$permisos = isset($cliente['permisos']) && is_array($cliente['permisos']) ? $cliente['permisos'] : array();
$acciones = isset($cliente['acciones']) && is_array($cliente['acciones']) ? $cliente['acciones'] : array();
$estatus = isset($cliente['estatus']) ? $cliente['estatus'] : 'en_revision';
$listaPrecio = isset($cliente['id_lista_precio']) && $cliente['id_lista_precio'] !== '' ? $cliente['id_lista_precio'] : null;
$catalogoModo = isset($cliente['catalogo_modo']) ? $cliente['catalogo_modo'] : 'general';
$camposPendientes = isset($cliente['campos_pendientes']) && is_array($cliente['campos_pendientes']) ? $cliente['campos_pendientes'] : array();
$datosContacto = isset($cliente['contacto']) && is_array($cliente['contacto']) ? $cliente['contacto'] : $cliente;
$datosComerciales = isset($cliente['datos_comerciales']) && is_array($cliente['datos_comerciales']) ? $cliente['datos_comerciales'] : (isset($cliente['comercial']) && is_array($cliente['comercial']) ? $cliente['comercial'] : array());
$datosEntrega = isset($cliente['entrega']) && is_array($cliente['entrega']) ? $cliente['entrega'] : (isset($cliente['direccion']) && is_array($cliente['direccion']) ? $cliente['direccion'] : array());
$datosFacturacion = isset($cliente['facturacion']) && is_array($cliente['facturacion']) ? $cliente['facturacion'] : array();
$solicitudesCambio = isset($cliente['solicitudes_cambio']) && is_array($cliente['solicitudes_cambio']) ? $cliente['solicitudes_cambio'] : array();
$puedeVerCatalogo = SesionCliente::tieneAccion('ver_catalogo', 'distribucion.catalogo.ver');
$puedeVerPrecioLista = SesionCliente::tieneAccion('ver_precio', 'distribucion.precio.ver_lista_asignada');
$puedeCotizar = SesionCliente::tieneAccion('agregar_cotizacion', 'distribucion.cotizacion.solicitar');
$badgeEstatus = $estatus === 'aprobado' ? 'badge-success' : ($estatus === 'suspendido' || $estatus === 'rechazado' ? 'badge-danger' : 'badge-warning');
if (!function_exists('dist_cuenta_valor')) {
  function dist_cuenta_valor($datos, $clave, $default = '') {
    if (!is_array($datos) || !array_key_exists($clave, $datos) || $datos[$clave] === null) { return $default; }
    return (string) $datos[$clave];
  }
}
if (!function_exists('dist_cuenta_checked')) {
  function dist_cuenta_checked($datos, $clave) {
    return !empty($datos[$clave]) ? 'checked' : '';
  }
}
?>
<section class="content-grid account-page">
  <?php if ($respuesta): ?>
    <div class="alert <?php echo empty($respuesta['error']) ? 'alert-success' : 'alert-warning'; ?> card-span-2">
      <?php echo htmlspecialchars(isset($respuesta['mensaje']) ? $respuesta['mensaje'] : 'Solicitud procesada'); ?>
      <?php if (isset($respuesta['requiere_revision']) && $respuesta['requiere_revision']): ?><strong> Quedó en revisión del ERP.</strong><?php endif; ?>
    </div>
  <?php endif; ?>

  <article class="card">
    <div class="card-header"><div><h2>Perfil comercial</h2><p>Informacion resuelta por el ERP.</p></div><span class="badge <?php echo $badgeEstatus; ?>"><?php echo htmlspecialchars($estatus); ?></span></div>
    <div class="key-value-list">
      <div><span>Nombre</span><strong><?php echo htmlspecialchars(dist_cuenta_valor($cliente, 'nombre', 'Cliente')); ?></strong></div>
      <div><span>Correo de acceso</span><strong><?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'correo', dist_cuenta_valor($cliente, 'correo', 'Pendiente'))); ?></strong></div>
      <div><span>Tipo</span><strong><?php echo htmlspecialchars(dist_cuenta_valor($cliente, 'tipo_cliente', 'registrado')); ?></strong></div>
      <div><span>Lista de precio</span><strong><?php echo htmlspecialchars($listaPrecio ? $listaPrecio : 'Sin lista visible'); ?></strong></div>
      <div><span>Catalogo</span><strong><?php echo htmlspecialchars($catalogoModo); ?></strong></div>
    </div>
  </article>

  <article class="card card-span-2">
    <div class="card-header"><div><h2>Accesos disponibles</h2><p>Modulos habilitados para tu cuenta comercial.</p></div></div>
    <div class="module-state-grid">
      <div class="module-state"><span class="badge <?php echo $puedeVerCatalogo ? 'badge-success' : 'badge-warning'; ?>"><?php echo $puedeVerCatalogo ? 'Activo' : 'Pendiente'; ?></span><strong>Catalogo</strong><small><?php echo $puedeVerCatalogo ? 'Ya puedes consultar productos publicados.' : 'Pendiente de habilitacion comercial.'; ?></small></div>
      <div class="module-state"><span class="badge <?php echo $puedeVerPrecioLista && $listaPrecio ? 'badge-success' : 'badge-warning'; ?>"><?php echo $puedeVerPrecioLista && $listaPrecio ? 'Activo' : 'Pendiente'; ?></span><strong>Precios</strong><small><?php echo $puedeVerPrecioLista && $listaPrecio ? 'Lista comercial asignada.' : 'Se habilitaran cuando tu lista este lista.'; ?></small></div>
      <div class="module-state"><span class="badge <?php echo $puedeCotizar ? 'badge-success' : 'badge-warning'; ?>"><?php echo $puedeCotizar ? 'Activo' : 'Pendiente'; ?></span><strong>Cotizacion</strong><small>El ERP revalida antes de registrar.</small></div>
    </div>
  </article>

  <form class="card card-span-2 account-form" method="post" action="<?php echo URL_RAIZ; ?>/cuenta/guardar">
    <div class="card-header"><div><h2>Editar mi cuenta</h2><p>Contacto se puede aplicar directo; datos comerciales, entrega y facturacion pueden quedar en revision.</p></div><button class="btn btn-primary" type="submit"><span class="icon icon-save"></span>Guardar cambios</button></div>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Contacto</strong><span>Editable directo con auditoria.</span></div>
      <div class="form-grid two-columns">
        <label class="field-block">Telefono<input type="tel" name="telefono" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'telefono')); ?>"></label>
        <label class="field-block">WhatsApp<input type="tel" name="whatsapp" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'whatsapp')); ?>"></label>
        <label class="field-block">Correo principal<input type="email" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'correo', dist_cuenta_valor($cliente, 'correo'))); ?>" readonly></label>
        <label class="field-block">Correo alterno<input type="email" name="correo_alterno" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'correo_alterno')); ?>"></label>
        <label class="field-block full">Contacto principal<input type="text" name="contacto_principal" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosContacto, 'contacto_principal')); ?>"></label>
      </div>
    </section>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Datos comerciales</strong><span>Requieren revision si cambian.</span></div>
      <div class="form-grid two-columns">
        <label class="field-block">Empresa<input type="text" name="datos_comerciales[empresa]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosComerciales, 'empresa', dist_cuenta_valor($cliente, 'empresa'))); ?>"></label>
        <label class="field-block">Nombre comercial<input type="text" name="datos_comerciales[nombre_negocio]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosComerciales, 'nombre_negocio')); ?>"></label>
        <label class="field-block">Tipo de negocio<input type="text" name="datos_comerciales[tipo_negocio]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosComerciales, 'tipo_negocio')); ?>"></label>
        <label class="field-block">Ciudad<input type="text" name="datos_comerciales[ciudad]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosComerciales, 'ciudad')); ?>"></label>
        <label class="field-block">Estado<input type="text" name="datos_comerciales[estado]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosComerciales, 'estado')); ?>"></label>
      </div>
    </section>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Direccion de entrega</strong><span>Queda en revision logistica.</span></div>
      <div class="form-grid two-columns">
        <label class="field-block">Calle<input type="text" name="entrega[calle]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'calle')); ?>"></label>
        <label class="field-block">Numero exterior<input type="text" name="entrega[numero_exterior]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'numero_exterior')); ?>"></label>
        <label class="field-block">Numero interior<input type="text" name="entrega[numero_interior]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'numero_interior')); ?>"></label>
        <label class="field-block">Colonia<input type="text" name="entrega[colonia]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'colonia')); ?>"></label>
        <label class="field-block">Ciudad<input type="text" name="entrega[ciudad]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'ciudad')); ?>"></label>
        <label class="field-block">Estado<input type="text" name="entrega[estado]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'estado')); ?>"></label>
        <label class="field-block">Codigo postal<input type="text" name="entrega[codigo_postal]" inputmode="numeric" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'codigo_postal')); ?>"></label>
        <label class="field-block">Referencias<input type="text" name="entrega[referencias]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosEntrega, 'referencias')); ?>"></label>
      </div>
    </section>

    <section class="quote-form-section">
      <div class="form-section-title"><strong>Facturacion</strong><span>No genera factura automaticamente.</span></div>
      <label class="checkbox-row"><input type="checkbox" name="facturacion[requiere_factura]" value="1" <?php echo dist_cuenta_checked($datosFacturacion, 'requiere_factura'); ?>>Requiero factura en pedidos entregados y pagados</label>
      <div class="form-grid two-columns">
        <label class="field-block">RFC<input type="text" name="facturacion[rfc]" maxlength="13" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'rfc')); ?>"></label>
        <label class="field-block">Razon social<input type="text" name="facturacion[razon_social]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'razon_social')); ?>"></label>
        <label class="field-block">Regimen fiscal<input type="text" name="facturacion[regimen_fiscal]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'regimen_fiscal')); ?>"></label>
        <label class="field-block">Uso CFDI<input type="text" name="facturacion[uso_cfdi]" maxlength="10" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'uso_cfdi')); ?>"></label>
        <label class="field-block">Codigo postal fiscal<input type="text" name="facturacion[codigo_postal_fiscal]" inputmode="numeric" maxlength="5" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'codigo_postal_fiscal')); ?>"></label>
        <label class="field-block">Correo facturacion<input type="email" name="facturacion[correo_facturacion]" value="<?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'correo_facturacion')); ?>"></label>
        <label class="field-block full">Comentarios fiscales<textarea name="facturacion[comentarios_facturacion]" rows="2"><?php echo htmlspecialchars(dist_cuenta_valor($datosFacturacion, 'comentarios_facturacion')); ?></textarea></label>
      </div>
    </section>

    <div class="account-edit-placeholder">
      <span class="badge badge-info">ERP</span>
      <span>No puedes modificar lista de precios, permisos, tipo de cliente, estatus ni modo de catalogo desde el portal.</span>
    </div>
  </form>

  <article class="card card-span-2">
    <div class="card-header"><div><h2>Solicitudes de cambio</h2><p>Historial de cambios pendientes, aplicados o rechazados.</p></div></div>
    <ul class="status-list">
      <?php if (empty($solicitudesCambio)): ?><li><span class="badge badge-info">Sin pendientes</span><span>No hay cambios de perfil reportados por el ERP.</span></li><?php endif; ?>
      <?php foreach ($solicitudesCambio as $solicitud): ?><li><span class="badge badge-warning"><?php echo htmlspecialchars(isset($solicitud['estatus']) ? $solicitud['estatus'] : 'revision'); ?></span><span><?php echo htmlspecialchars(isset($solicitud['resumen']) ? $solicitud['resumen'] : 'Cambio solicitado'); ?><?php if (!empty($solicitud['fecha_registro'])): ?> · <?php echo htmlspecialchars($solicitud['fecha_registro']); ?><?php endif; ?></span></li><?php endforeach; ?>
    </ul>
  </article>

  <aside class="card">
    <div class="card-header"><div><h2>Datos pendientes</h2><p>Base para liberar lista y precios.</p></div></div>
    <ul class="status-list">
      <?php if (empty($camposPendientes)): ?><li><span class="badge badge-success">OK</span><span>El ERP no reporta campos pendientes.</span></li><?php endif; ?>
      <?php foreach ($camposPendientes as $campo): ?><li><span class="badge badge-warning">Pendiente</span><span><?php echo htmlspecialchars($campo); ?></span></li><?php endforeach; ?>
      <?php if (!$listaPrecio): ?><li><span class="badge badge-warning">Lista</span><span>Lista de precio pendiente de asignacion.</span></li><?php endif; ?>
    </ul>
  </aside>

  <article class="card card-span-2">
    <div class="card-header"><div><h2>Siguiente paso</h2><p>Cuando se habiliten precios o cotizacion, apareceran automaticamente en tu cuenta.</p></div></div>
    <?php if ($puedeVerCatalogo): ?><a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/catalogo"><span class="icon icon-boxes"></span>Ver catalogo</a><?php endif; ?>
  </article>
</section>
