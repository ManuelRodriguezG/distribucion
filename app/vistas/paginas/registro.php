<?php
$categoriasRespuesta = isset($datos['categorias']) && is_array($datos['categorias']) ? $datos['categorias'] : array();
$categoriasJerarquia = isset($categoriasRespuesta['jerarquia']) && is_array($categoriasRespuesta['jerarquia']) ? $categoriasRespuesta['jerarquia'] : array();
$categoriasItems = isset($categoriasRespuesta['items']) && is_array($categoriasRespuesta['items']) ? $categoriasRespuesta['items'] : array();
$categoriasInteres = !empty($categoriasJerarquia) ? $categoriasJerarquia : $categoriasItems;
$seleccionPadreIncluyeDescendientes = !empty($categoriasRespuesta['seleccion_padre_incluye_descendientes']);
$respuestaRegistro = isset($datos['respuesta']) && is_array($datos['respuesta']) ? $datos['respuesta'] : array();
$solicitudRegistro = isset($datos['solicitud']) && is_array($datos['solicitud']) ? $datos['solicitud'] : array();
$camposFaltantes = isset($respuestaRegistro['campos_faltantes']) && is_array($respuestaRegistro['campos_faltantes']) ? $respuestaRegistro['campos_faltantes'] : array();
$erroresCampos = isset($respuestaRegistro['errores_campos']) && is_array($respuestaRegistro['errores_campos']) ? $respuestaRegistro['errores_campos'] : array();
$codigoRegistro = isset($respuestaRegistro['codigo']) ? (string) $respuestaRegistro['codigo'] : '';
$registroExitoso = !empty($respuestaRegistro) && empty($respuestaRegistro['error']);
$registroExistente = in_array($codigoRegistro, array('solicitud_existente', 'cliente_existente', 'cuenta_en_revision', 'requiere_activacion'), true);
$mostrarCierre = $registroExitoso || $registroExistente;
if (!function_exists('dist_registro_categoria_bool')) {
  function dist_registro_categoria_bool($valor, $default = false) {
    if ($valor === null || $valor === '') { return $default; }
    return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
  }
}
if (!function_exists('dist_registro_valor')) {
  function dist_registro_valor($datos, $campo) {
    return isset($datos[$campo]) ? (string) $datos[$campo] : '';
  }
}
if (!function_exists('dist_registro_checked')) {
  function dist_registro_checked($datos, $campo) {
    return !empty($datos[$campo]) ? ' checked' : '';
  }
}
if (!function_exists('dist_registro_selected')) {
  function dist_registro_selected($datos, $campo, $valor) {
    return isset($datos[$campo]) && (string) $datos[$campo] === (string) $valor ? ' selected' : '';
  }
}
if (!function_exists('dist_registro_label_campo')) {
  function dist_registro_label_campo($campo) {
    $mapa = array(
      'nombre' => 'Nombre personal',
      'nombre_negocio' => 'Nombre del negocio',
      'correo' => 'Correo para acceso',
      'telefono' => 'Telefono',
      'ciudad' => 'Ciudad',
      'estado' => 'Estado',
      'tipo_negocio' => 'Tipo de negocio',
      'facturacion.rfc' => 'RFC',
      'facturacion.razon_social' => 'Razon social',
      'facturacion.regimen_fiscal' => 'Regimen fiscal',
      'facturacion.uso_cfdi' => 'Uso CFDI',
      'facturacion.codigo_postal_fiscal' => 'Codigo postal fiscal',
      'facturacion.correo_facturacion' => 'Correo de facturacion',
      'rfc' => 'RFC',
      'razon_social' => 'Razon social',
      'regimen_fiscal' => 'Regimen fiscal',
      'uso_cfdi' => 'Uso CFDI',
      'codigo_postal_fiscal' => 'Codigo postal fiscal',
      'correo_facturacion' => 'Correo de facturacion'
    );
    return isset($mapa[$campo]) ? $mapa[$campo] : str_replace('_', ' ', (string) $campo);
  }
}
if (!function_exists('dist_registro_error_texto')) {
  function dist_registro_error_texto($errorCampo) {
    if (is_array($errorCampo)) {
      if (!empty($errorCampo['mensaje'])) { return (string) $errorCampo['mensaje']; }
      if (!empty($errorCampo['error'])) { return (string) $errorCampo['error']; }
      if (!empty($errorCampo['etiqueta'])) { return 'Revisa ' . (string) $errorCampo['etiqueta'] . '.'; }
      return 'Revisa este dato.';
    }
    return (string) $errorCampo;
  }
}
if (!function_exists('dist_registro_categoria_opciones')) {
  function dist_registro_categoria_opciones($categorias, $nivel = 0, $seleccionPadreIncluyeDescendientes = true, $seleccionadas = array()) {
    foreach ($categorias as $categoria) {
      if (!is_array($categoria)) { continue; }
      $children = isset($categoria['children']) && is_array($categoria['children']) ? $categoria['children'] : array();
      $idCategoria = isset($categoria['id_categoria_erp']) ? intval($categoria['id_categoria_erp']) : (isset($categoria['id_categoria']) ? intval($categoria['id_categoria']) : 0);
      $nombreCategoria = isset($categoria['nombre']) ? (string) $categoria['nombre'] : (isset($categoria['categoria']) ? (string) $categoria['categoria'] : 'Categoria');
      $rutaCategoria = isset($categoria['ruta']) && $categoria['ruta'] !== '' ? (string) $categoria['ruta'] : $nombreCategoria;
      $incluyeDescendientes = isset($categoria['seleccion_incluye_descendientes'])
        ? dist_registro_categoria_bool($categoria['seleccion_incluye_descendientes'], $seleccionPadreIncluyeDescendientes)
        : ($seleccionPadreIncluyeDescendientes && !empty($children));
      $valorCategoria = json_encode(array(
        'id_categoria_erp' => $idCategoria,
        'nombre' => $nombreCategoria,
        'ruta' => $rutaCategoria,
        'incluye_descendientes' => $incluyeDescendientes
      ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      $checked = isset($seleccionadas[$idCategoria]) || ($idCategoria <= 0 && isset($seleccionadas[$rutaCategoria])) ? ' checked' : '';
      ?>
      <label class="category-interest-option" style="--category-level: <?php echo min(4, max(0, intval($nivel))); ?>;"><input type="checkbox" name="categorias_interes[]" value="<?php echo htmlspecialchars($valorCategoria); ?>"<?php echo $checked; ?>><span><?php echo htmlspecialchars($rutaCategoria); ?></span></label>
      <?php
      if (!empty($children)) {
        dist_registro_categoria_opciones($children, $nivel + 1, $seleccionPadreIncluyeDescendientes, $seleccionadas);
      }
    }
  }
}
$categoriasSeleccionadas = array();
if (!empty($solicitudRegistro['categorias_interes']) && is_array($solicitudRegistro['categorias_interes'])) {
  foreach ($solicitudRegistro['categorias_interes'] as $categoriaSeleccionada) {
    if (!is_array($categoriaSeleccionada)) { continue; }
    if (!empty($categoriaSeleccionada['id_categoria_erp'])) { $categoriasSeleccionadas[intval($categoriaSeleccionada['id_categoria_erp'])] = true; }
    if (!empty($categoriaSeleccionada['ruta'])) { $categoriasSeleccionadas[(string) $categoriaSeleccionada['ruta']] = true; }
  }
}
?>
<article class="card auth-card auth-card-standalone auth-card-wide">
  <div class="card-header"><div><h2><?php echo $mostrarCierre ? ($registroExistente ? 'Solicitud ya registrada' : 'Solicitud recibida') : 'Solicitud de acceso mayoreo'; ?></h2><p><?php echo $mostrarCierre ? 'Gracias por compartir la informacion de tu negocio.' : 'Registro para negocios interesados en catalogo, precios y cotizacion.'; ?></p></div><span class="badge <?php echo $mostrarCierre ? 'badge-success' : 'badge-warning'; ?>"><?php echo $mostrarCierre ? 'Recibida' : 'Revision interna'; ?></span></div>
  <?php if ($mostrarCierre): ?>
    <section class="registro-confirmacion">
      <span class="metric-icon icon icon-shield"></span>
      <div>
        <h3><?php echo $registroExistente ? 'Ya tenemos una solicitud asociada a este correo.' : 'Recibimos tu solicitud.'; ?></h3>
        <p><?php echo htmlspecialchars(isset($respuestaRegistro['mensaje']) ? $respuestaRegistro['mensaje'] : 'Recibimos la informacion de tu negocio y sera revisada por el equipo Artiani.'); ?></p>
        <?php if (!empty($respuestaRegistro['folio'])): ?><div class="confirmation-folio"><span>Folio</span><strong><?php echo htmlspecialchars($respuestaRegistro['folio']); ?></strong></div><?php endif; ?>
        <ul class="status-list compact">
          <li><span class="badge badge-info">Siguiente</span><span><?php echo htmlspecialchars(!empty($respuestaRegistro['siguiente_paso']) ? $respuestaRegistro['siguiente_paso'] : 'Cuando tu solicitud sea aprobada, recibiras instrucciones para activar tu acceso o iniciar sesion.'); ?></span></li>
          <li><span class="badge badge-warning">Nota</span><span>Si necesitas corregir informacion, contacta a Artiani o espera la revision de tu solicitud.</span></li>
        </ul>
        <div class="actions">
          <a class="btn btn-primary" href="<?php echo URL_RAIZ; ?>/auth/login"><span class="icon icon-login"></span>Ir a iniciar sesion</a>
          <a class="btn btn-light" href="<?php echo URL_RAIZ; ?>/">Volver al inicio</a>
        </div>
        <a class="registro-secondary-link" href="<?php echo URL_RAIZ; ?>/auth/registro?otra=1">Enviar otra solicitud con datos distintos</a>
      </div>
    </section>
  <?php else: ?>
  <?php if (!empty($respuestaRegistro)): ?><div class="alert alert-warning"><?php echo htmlspecialchars(isset($respuestaRegistro['mensaje']) ? $respuestaRegistro['mensaje'] : 'No fue posible enviar la solicitud'); ?><?php if (!empty($camposFaltantes)): ?><br><strong>Completa: <?php echo htmlspecialchars(implode(', ', array_map('dist_registro_label_campo', $camposFaltantes))); ?>.</strong><?php endif; ?></div><?php endif; ?>
  <?php if (!empty($erroresCampos)): ?><ul class="status-list compact"><?php foreach ($erroresCampos as $campo => $errorCampo): ?><?php $campoError = is_array($errorCampo) && !empty($errorCampo['campo']) ? $errorCampo['campo'] : $campo; $etiquetaCampo = is_array($errorCampo) && !empty($errorCampo['etiqueta']) ? $errorCampo['etiqueta'] : dist_registro_label_campo($campoError); ?><li><span class="badge badge-warning"><?php echo htmlspecialchars($etiquetaCampo); ?></span><span><?php echo htmlspecialchars(dist_registro_error_texto($errorCampo)); ?></span></li><?php endforeach; ?></ul><?php endif; ?>
  <form method="post" class="form-grid two-columns" data-registro-form>
    <input type="hidden" name="tipo_interes" value="mayorista">
    <input type="hidden" name="empresa" data-registro-empresa>
    <div class="form-section-title full"><strong>Datos de contacto</strong><span>Usaremos estos datos para revisar tu solicitud y enviarte acceso cuando quede habilitado.</span></div>
    <label class="field-block">Nombre personal *<input type="text" name="nombre" required autocomplete="name" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'nombre')); ?>"></label>
    <label class="field-block">Nombre del negocio o empresa *<input type="text" name="nombre_negocio" required data-registro-negocio value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'nombre_negocio')); ?>"></label>
    <label class="field-block">Correo para acceso *<input type="email" name="correo" required autocomplete="email" placeholder="Requerido para crear acceso" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'correo')); ?>"></label>
    <label class="field-block">Telefono *<input type="tel" name="telefono" required autocomplete="tel" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'telefono')); ?>"></label>
    <label class="field-block">WhatsApp<input type="tel" name="whatsapp" placeholder="Si es distinto al telefono" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'whatsapp')); ?>"></label>
    <label class="field-block">RFC<input type="text" name="rfc" maxlength="13" placeholder="Opcional; se usara para facturacion si marcas esa opcion" data-registro-rfc value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'rfc')); ?>"></label>

    <div class="form-section-title full"><strong>Datos del negocio</strong><span>Estos datos ayudan a Artiani a validar tu perfil comercial.</span></div>
    <label class="field-block">Ciudad *<input type="text" name="ciudad" required value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'ciudad')); ?>"></label>
    <label class="field-block">Estado *<input type="text" name="estado" required value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'estado')); ?>"></label>
    <label class="field-block full">Tipo de negocio *
      <select name="tipo_negocio" required>
        <option value="">Selecciona una opcion</option>
        <option value="venta_internet"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'venta_internet'); ?>>Venta por internet</option>
        <option value="veterinaria"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'veterinaria'); ?>>Veterinaria</option>
        <option value="petshop"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'petshop'); ?>>Petshop / tienda de mascotas</option>
        <option value="acuario"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'acuario'); ?>>Acuario</option>
        <option value="acuario_petshop"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'acuario_petshop'); ?>>Acuario y tienda de mascotas</option>
        <option value="estetica_canina"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'estetica_canina'); ?>>Estetica canina</option>
        <option value="criador"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'criador'); ?>>Criador / criadero</option>
        <option value="vendedor_mercado"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'vendedor_mercado'); ?>>Vendedor en mercado o tianguis</option>
        <option value="vendedor_ambulante"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'vendedor_ambulante'); ?>>Vendedor ambulante</option>
        <option value="otro"<?php echo dist_registro_selected($solicitudRegistro, 'tipo_negocio', 'otro'); ?>>Otro</option>
      </select>
    </label>

    <div class="form-section-title full"><strong>Direccion del negocio</strong><span>Si tienes punto fisico, ayudanos a ubicar tu zona de venta.</span></div>
    <label class="field-block">Calle<input type="text" name="calle" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'calle')); ?>"></label>
    <label class="field-block">Numero exterior<input type="text" name="numero_exterior" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'numero_exterior')); ?>"></label>
    <label class="field-block">Numero interior<input type="text" name="numero_interior" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'numero_interior')); ?>"></label>
    <label class="field-block">Colonia<input type="text" name="colonia" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'colonia')); ?>"></label>
    <label class="field-block">Codigo postal<input type="text" name="codigo_postal" inputmode="numeric" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'codigo_postal')); ?>"></label>
    <label class="field-block">Referencias<input type="text" name="referencias" placeholder="Local, mercado, plaza o zona" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'referencias')); ?>"></label>

    <section class="registro-fiscal-section full">
      <div class="form-section-title"><strong>Facturacion</strong><span>Guardar mis datos fiscales para futuras revisiones de pedidos.</span></div>
      <label class="checkbox-row"><input type="checkbox" name="requiere_factura" value="1" data-registro-requiere-factura<?php echo dist_registro_checked($solicitudRegistro, 'requiere_factura'); ?>>Necesito factura</label>
      <div class="form-grid two-columns is-hidden" data-registro-facturacion-fields>
        <p class="form-inline-help full">El RFC capturado arriba se enviara dentro del bloque fiscal. Esto no genera factura ni aprueba tu solicitud automaticamente.</p>
        <label class="field-block">Razon social<input type="text" name="razon_social" maxlength="180" placeholder="Razon social" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'razon_social')); ?>"></label>
        <label class="field-block">Regimen fiscal<input type="text" name="regimen_fiscal" maxlength="160" placeholder="Regimen fiscal SAT" value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'regimen_fiscal')); ?>"></label>
        <label class="field-block">Uso CFDI<input type="text" name="uso_cfdi" maxlength="10" placeholder="G03" data-registro-uso-cfdi value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'uso_cfdi')); ?>"></label>
        <label class="field-block">Codigo postal fiscal<input type="text" name="codigo_postal_fiscal" inputmode="numeric" maxlength="5" placeholder="00000" data-registro-cp-fiscal value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'codigo_postal_fiscal')); ?>"></label>
        <label class="field-block">Correo de facturacion<input type="email" name="correo_facturacion" maxlength="180" placeholder="facturas@dominio.com" data-registro-correo-fiscal value="<?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'correo_facturacion')); ?>"></label>
        <label class="field-block full">Comentarios fiscales<textarea name="comentarios_facturacion" rows="2" maxlength="500" placeholder="Notas fiscales para revision interna"><?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'comentarios_facturacion')); ?></textarea></label>
      </div>
      <p class="form-inline-help">Si lo marcas, Artiani guardara tus datos para revision. La factura no se genera automaticamente.</p>
    </section>

    <section class="registro-fiscal-section full registro-intereses-section">
      <div class="form-section-title"><strong>¿Qué tipos de productos te interesan?</strong><span>Ayuda a Artiani a priorizar tu catalogo inicial.</span></div>
      <?php if (empty($categoriasInteres)): ?>
        <p class="form-inline-help">Por ahora escribe tus categorias, marcas o productos en el campo de intereses comerciales.</p>
      <?php else: ?>
        <div class="category-interest-dropdown" data-category-interest-dropdown>
          <button class="category-interest-trigger" type="button" data-category-interest-trigger aria-expanded="false">
            <span data-category-interest-label>Selecciona categorias de interes</span>
            <span class="category-interest-trigger-meta">Desplegar</span>
          </button>
          <div class="category-interest-panel" data-category-interest-panel>
            <label class="category-interest-search">Buscar categoria
              <input type="search" placeholder="Escribe para filtrar categorias" autocomplete="off" data-category-interest-search>
            </label>
            <div class="category-interest-grid" data-category-interest-grid>
              <?php dist_registro_categoria_opciones($categoriasInteres, 0, $seleccionPadreIncluyeDescendientes, $categoriasSeleccionadas); ?>
            </div>
            <p class="category-interest-empty is-hidden" data-category-interest-empty>No encontramos categorias con ese texto.</p>
          </div>
        </div>
        <p class="form-inline-help">Puedes elegir categorias generales; no necesitas marcar todos los productos o subcategorias. Si eliges una categoria padre, Artiani puede considerar sus subcategorias.</p>
      <?php endif; ?>
    </section>

    <label class="field-block full">Marcas o productos de interes<textarea name="intereses_comerciales" rows="3" placeholder="Categorias, marcas, volumen aproximado o productos que buscas"><?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'intereses_comerciales')); ?></textarea></label>
    <label class="field-block full">Comentarios<textarea name="mensaje" rows="4" placeholder="Cuentanos como vendes, si tienes tienda fisica, redes sociales o necesidades principales"><?php echo htmlspecialchars(dist_registro_valor($solicitudRegistro, 'mensaje')); ?></textarea></label>
    <button class="btn btn-primary" type="submit" data-registro-submit><span class="icon icon-send"></span><span data-registro-submit-label>Enviar solicitud</span></button>
  </form>
  <div class="auth-secondary-action">
    <span>Ya tienes acceso?</span>
    <a href="<?php echo URL_RAIZ; ?>/auth/login">Iniciar sesion</a>
  </div>
  <?php endif; ?>
</article>
<script>
(function () {
  var form = document.querySelector('[data-registro-form]');
  var negocio = document.querySelector('[data-registro-negocio]');
  var empresa = document.querySelector('[data-registro-empresa]');
  var submit = document.querySelector('[data-registro-submit]');
  var submitLabel = document.querySelector('[data-registro-submit-label]');
  var requiereFactura = document.querySelector('[data-registro-requiere-factura]');
  var fiscalFields = document.querySelector('[data-registro-facturacion-fields]');
  var rfc = document.querySelector('[data-registro-rfc]');
  var usoCfdi = document.querySelector('[data-registro-uso-cfdi]');
  var cpFiscal = document.querySelector('[data-registro-cp-fiscal]');
  var correoFiscal = document.querySelector('[data-registro-correo-fiscal]');
  var categoriasDropdown = document.querySelector('[data-category-interest-dropdown]');
  if (!negocio || !empresa || !form) { return; }
  var sync = function () { empresa.value = negocio.value; };
  var toggleFiscal = function () { if (fiscalFields && requiereFactura) { fiscalFields.classList.toggle('is-hidden', !requiereFactura.checked); } };
  var validarFiscal = function (event) {
    if (!requiereFactura || !requiereFactura.checked || !fiscalFields) { return; }
    var requeridos = Array.prototype.slice.call(fiscalFields.querySelectorAll('input[name="razon_social"], input[name="regimen_fiscal"], input[name="uso_cfdi"], input[name="codigo_postal_fiscal"], input[name="correo_facturacion"]'));
    if (rfc) { requeridos.unshift(rfc); }
    var incompleto = requeridos.some(function (node) { return String(node.value || '').trim() === ''; });
    if (incompleto) {
      event.preventDefault();
      window.alert('Completa RFC, razon social, regimen fiscal, uso CFDI, codigo postal fiscal y correo de facturacion.');
      if (submit) { submit.disabled = false; }
      if (submitLabel) { submitLabel.textContent = 'Enviar solicitud'; }
    }
  };
  negocio.addEventListener('input', sync);
  form.addEventListener('submit', sync);
  form.addEventListener('submit', validarFiscal);
  form.addEventListener('submit', function (event) {
    if (event.defaultPrevented) { return; }
    if (submit) { submit.disabled = true; }
    if (submitLabel) { submitLabel.textContent = 'Enviando solicitud...'; }
  });
  if (requiereFactura) { requiereFactura.addEventListener('change', toggleFiscal); }
  if (rfc) { rfc.addEventListener('input', function () { rfc.value = rfc.value.toUpperCase(); }); }
  if (usoCfdi) { usoCfdi.addEventListener('input', function () { usoCfdi.value = usoCfdi.value.toUpperCase(); }); }
  if (cpFiscal) { cpFiscal.addEventListener('input', function () { cpFiscal.value = cpFiscal.value.replace(/\D/g, '').slice(0, 5); }); }
  if (correoFiscal) { correoFiscal.addEventListener('input', function () { correoFiscal.value = correoFiscal.value.toLowerCase(); }); }
  if (categoriasDropdown) {
    var trigger = categoriasDropdown.querySelector('[data-category-interest-trigger]');
    var label = categoriasDropdown.querySelector('[data-category-interest-label]');
    var checks = Array.prototype.slice.call(categoriasDropdown.querySelectorAll('input[type="checkbox"]'));
    var search = categoriasDropdown.querySelector('[data-category-interest-search]');
    var empty = categoriasDropdown.querySelector('[data-category-interest-empty]');
    var options = Array.prototype.slice.call(categoriasDropdown.querySelectorAll('.category-interest-option'));
    var updateCategoryLabel = function () {
      var selected = checks.filter(function (node) { return node.checked; });
      if (!label) { return; }
      label.textContent = selected.length ? selected.length + ' categorias seleccionadas' : 'Selecciona categorias de interes';
    };
    if (trigger) {
      trigger.addEventListener('click', function () {
        var open = categoriasDropdown.classList.toggle('is-open');
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open && search) { setTimeout(function () { search.focus(); }, 40); }
      });
    }
    checks.forEach(function (node) { node.addEventListener('change', updateCategoryLabel); });
    if (search) {
      search.addEventListener('input', function () {
        var term = String(search.value || '').trim().toLowerCase();
        var visibles = 0;
        options.forEach(function (option) {
          var text = String(option.textContent || '').toLowerCase();
          var visible = term === '' || text.indexOf(term) !== -1;
          option.classList.toggle('is-hidden', !visible);
          if (visible) { visibles++; }
        });
        if (empty) { empty.classList.toggle('is-hidden', visibles > 0); }
      });
    }
    document.addEventListener('click', function (event) {
      if (!categoriasDropdown.contains(event.target)) {
        categoriasDropdown.classList.remove('is-open');
        if (trigger) { trigger.setAttribute('aria-expanded', 'false'); }
      }
    });
    updateCategoryLabel();
  }
  sync();
  toggleFiscal();
})();
</script>

