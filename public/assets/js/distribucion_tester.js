(function () {
  var root = document.querySelector('.tester-shell');
  if (!root) { return; }

  var state = {
    token: window.localStorage.getItem('distribucion_tester_token') || '',
    results: []
  };

  var apiInput = root.querySelector('[data-api-base-input]');
  var tokenInput = root.querySelector('[data-token-input]');
  var summaryBox = root.querySelector('[data-summary]');
  tokenInput.value = state.token;

  var payloads = {
    registro: {
      nombre: 'Nombre del contacto',
      nombre_negocio: 'Nombre comercial',
      empresa: 'Nombre comercial',
      correo: 'correo@dominio.com',
      telefono: '5555555555',
      whatsapp: '5555555555',
      rfc: '',
      ciudad: 'Guadalajara',
      estado: 'Jalisco',
      tipo_interes: 'mayorista',
      tipo_negocio: 'petshop',
      calle: 'Calle',
      numero_exterior: '123',
      numero_interior: 'A',
      colonia: 'Colonia',
      codigo_postal: '00000',
      referencias: 'Local, mercado, plaza o zona',
      intereses_comerciales: 'Marcas, categorias o volumen aproximado',
      categorias_interes: [],
      mensaje: 'Comentarios adicionales'
    },
    login: { correo: 'correo@dominio.com', contrasenia: 'contrasenia_de_prueba' },
    perfil_cambio: {
      contacto: { telefono: '5555555555', whatsapp: '5555555555', contacto_principal: 'Nombre del contacto' },
      entrega: { referencias: 'Actualizar referencias de entrega desde tester' }
    },
    mi_catalogo: { id_sku: null, accion: 'agregar', comentario: 'Producto de interes agregado desde tester' },
    cotizacion: {
      items: [{ id_sku: null, cantidad: 1 }],
      comentarios: 'Cotizacion de prueba desde tester',
      metodo_entrega: 'por_definir',
      entrega: {
        calle: 'Calle',
        numero_exterior: '123',
        colonia: 'Colonia',
        ciudad: 'Guadalajara',
        estado: 'Jalisco',
        codigo_postal: '00000',
        referencias: 'Referencias de prueba'
      },
      facturacion: { requiere_factura: 0 }
    },
    pedido: {
      items: [{ id_sku: null, cantidad: 1 }],
      comentarios: 'Pedido preliminar de prueba desde tester',
      metodo_entrega: 'por_definir',
      entrega: {
        calle: 'Calle',
        numero_exterior: '123',
        colonia: 'Colonia',
        ciudad: 'Guadalajara',
        estado: 'Jalisco',
        codigo_postal: '00000',
        referencias: 'Referencias de prueba'
      },
      facturacion: { requiere_factura: 0 }
    },
    inventario: { items: [{ id_sku: null, minimo: 1, maximo: 5, existencia_cliente: 0, comentario: 'Conteo tester' }] }
  };

  var tests = [
    { id: 'contratos_manifest', group: 'contratos', name: 'GET contratos', method: 'GET', endpoint: 'contratos', expect: ['error', 'api'] },
    { id: 'contratos_config', group: 'contratos', name: 'GET configuracion_inicial', method: 'GET', endpoint: 'configuracion_inicial', expect: ['error', 'api', 'sesion', 'ui'] },
    { id: 'registro_categorias', group: 'registro', name: 'GET categorias_interes', method: 'GET', endpoint: 'categorias_interes', expect: ['error', 'depurar.jerarquia'] },
    { id: 'login_perfil', group: 'login', name: 'GET perfil con token', method: 'GET', endpoint: 'auth/perfil', auth: true, expect: ['error', 'depurar.perfil', 'depurar.permisos', 'depurar.acciones'] },
    { id: 'perfil_get', group: 'perfil', name: 'GET perfil', method: 'GET', endpoint: 'auth/perfil', auth: true, expect: ['depurar.perfil.contacto', 'depurar.perfil.datos_comerciales', 'depurar.perfil.entrega', 'depurar.perfil.facturacion', 'depurar.perfil.solicitudes_cambio'] },
    { id: 'catalogo_listar', group: 'catalogo', name: 'GET catalogo', method: 'GET', endpoint: 'catalogo?limite=12', auth: true, expect: ['error'], security: ['items.0.stock', 'items.0.costo', 'items.0.margen'] },
    { id: 'catalogo_producto', group: 'catalogo', name: 'GET producto slug', method: 'GET', endpoint: 'producto/{slug}', auth: true, expect: ['error'], security: ['producto.stock', 'producto.costo', 'producto.margen'] },
    { id: 'catalogo_categorias', group: 'catalogo', name: 'GET categorias', method: 'GET', endpoint: 'categorias', auth: true, expect: ['error'] },
    { id: 'catalogo_filtros', group: 'catalogo', name: 'GET filtros', method: 'GET', endpoint: 'filtros', auth: true, expect: ['error'] },
    { id: 'mi_catalogo_listar', group: 'mi_catalogo', name: 'GET Mi catalogo', method: 'GET', endpoint: 'mi_catalogo/listar', auth: true, expect: ['error'] },
    { id: 'cotizacion_listar', group: 'cotizaciones', name: 'GET listar cotizaciones', method: 'GET', endpoint: 'cotizacion/listar', auth: true, expect: ['error'] },
    { id: 'cotizacion_detalle', group: 'cotizaciones', name: 'GET detalle cotizacion', method: 'GET', endpoint: 'cotizacion/detalle?id_cotizacion_distribucion={id}', auth: true, expect: ['error'] },
    { id: 'pedido_listar', group: 'pedidos', name: 'GET listar pedidos', method: 'GET', endpoint: 'pedido/listar', auth: true, expect: ['error'] },
    { id: 'pedido_detalle', group: 'pedidos', name: 'GET detalle pedido', method: 'GET', endpoint: 'pedido/detalle?id_cotizacion_distribucion={id}', auth: true, expect: ['error'] },
    { id: 'inventario_listar', group: 'inventario', name: 'GET inventario cliente', method: 'GET', endpoint: 'inventario_cliente/listar', auth: true, expect: ['error'] },
    { id: 'inventario_sugerido', group: 'inventario', name: 'GET sugerido', method: 'GET', endpoint: 'inventario_cliente/sugerido', auth: true, expect: ['error'] }
  ];

  function pretty(valor) { return JSON.stringify(valor, null, 2); }
  function baseUrl() { return (apiInput.value || root.getAttribute('data-api-base') || '').replace(/\/+$/, ''); }
  function currentToken() { return (tokenInput.value || '').trim(); }
  function endpointUrl(endpoint) { return baseUrl() + '/' + endpoint.replace(/^\/+/, ''); }
  function badgeClass(status) { return status === 'ok' ? 'badge-success' : (status === 'warn' ? 'badge-warning' : 'badge-danger'); }
  function value(selector) { var el = root.querySelector(selector); return el ? el.value : ''; }

  function escapeHtml(value) {
    return String(value === undefined || value === null ? '' : value)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  function getPath(obj, path) {
    return path.split('.').reduce(function (acc, key) {
      return acc && Object.prototype.hasOwnProperty.call(acc, key) ? acc[key] : undefined;
    }, obj);
  }

  function resolveEndpoint(endpoint) {
    return endpoint
      .replace('{slug}', encodeURIComponent(value('[data-product-slug]') || 'producto-sin-slug'))
      .replace('{id}', encodeURIComponent(value('[data-cotizacion-id]') || '0'));
  }

  function writeJsonEditor(nombre, valor) {
    var editor = root.querySelector('[data-payload-editor="' + nombre + '"]');
    if (editor) { editor.value = pretty(valor); }
  }

  function readJsonEditor(nombre) {
    var editor = root.querySelector('[data-payload-editor="' + nombre + '"]');
    try { return JSON.parse(editor ? editor.value : '{}'); } catch (e) { return {}; }
  }

  function refreshDynamicPayloads() {
    var sku = parseInt(value('[data-sku-id]') || '0', 10) || null;
    var cantidad = parseFloat(value('[data-test-cantidad]') || '1') || 1;
    ['cotizacion', 'pedido'].forEach(function (key) {
      var data = readJsonEditor(key);
      if (!Array.isArray(data.items) || !data.items.length) { data.items = [{ id_sku: sku, cantidad: cantidad }]; }
      data.items[0].id_sku = data.items[0].id_sku || sku;
      data.items[0].cantidad = data.items[0].cantidad || cantidad;
      writeJsonEditor(key, data);
    });
    var mi = readJsonEditor('mi_catalogo');
    mi.id_sku = mi.id_sku || sku;
    writeJsonEditor('mi_catalogo', mi);
    var inv = readJsonEditor('inventario');
    if (!Array.isArray(inv.items) || !inv.items.length) { inv.items = [{ id_sku: sku, minimo: 1, maximo: 5, existencia_cliente: 0 }]; }
    inv.items[0].id_sku = inv.items[0].id_sku || sku;
    writeJsonEditor('inventario', inv);
  }

  function validate(test, response) {
    var notes = [];
    if (!response) { return { status: 'error', notes: ['No llego JSON interpretable'], next: 'Validar host, CORS y disponibilidad del ERP.' }; }
    (test.expect || []).forEach(function (path) {
      if (getPath(response, path) === undefined) { notes.push('Falta ' + path); }
    });
    (test.security || []).forEach(function (path) {
      if (getPath(response, path) !== undefined) { notes.push('Campo sensible expuesto: ' + path); }
    });
    return { status: notes.length || response.error === true ? 'warn' : 'ok', notes: notes, next: 'Revisar respuesta cruda y continuar con el siguiente bloque.' };
  }

  function compactSummary(response) {
    if (!response) { return 'Sin respuesta JSON'; }
    var bits = [];
    if (response.mensaje) { bits.push(response.mensaje); }
    if (response.folio) { bits.push('Folio: ' + response.folio); }
    if (response.estatus) { bits.push('Estatus: ' + response.estatus); }
    if (response.permisos) { bits.push('Permisos: ' + response.permisos.length); }
    if (response.acciones) { bits.push('Acciones recibidas'); }
    if (response.items) { bits.push('Items: ' + response.items.length); }
    return bits.length ? bits.join(' | ') : 'Respuesta JSON recibida';
  }

  function request(test) {
    var options = { method: 'GET', headers: { Accept: 'application/json', 'X-Canal-Comercial': 'distribucion' } };
    if (test.auth && currentToken()) { options.headers.Authorization = 'Bearer ' + currentToken(); }
    return fetch(endpointUrl(resolveEndpoint(test.endpoint)), options)
      .then(function (res) { return res.text().then(function (text) { return { status: res.status, text: text }; }); })
      .then(function (raw) {
        var response = null;
        try { response = JSON.parse(raw.text); } catch (e) {}
        var validation = validate(test, response);
        if (raw.status >= 400 && validation.status === 'ok') { validation.status = 'warn'; }
        renderResult(test, response || raw.text, validation.status, validation.notes, validation.next, raw.status);
        if (test.id === 'registro_categorias' && response) { renderCategoryTree(getPath(response, 'depurar.jerarquia') || response.jerarquia || []); }
      })
      .catch(function (error) {
        renderResult(test, null, 'error', [error.message], 'Validar host local, CORS y disponibilidad del ERP.');
      });
  }

  function renderResult(test, response, status, notes, next, httpStatus) {
    var host = root.querySelector('[data-tests="' + test.group + '"]');
    if (!host) { return; }
    var card = host.querySelector('[data-test-card="' + test.id + '"]');
    if (!card) {
      card = document.createElement('article');
      card.className = 'tester-result-card';
      card.setAttribute('data-test-card', test.id);
      host.appendChild(card);
    }
    card.className = 'tester-result-card';
    card.innerHTML = '<div class="tester-result-head"><div><strong>' + escapeHtml(test.name) + '</strong><small>' + escapeHtml(test.method + ' /' + resolveEndpoint(test.endpoint)) + '</small></div><span class="badge ' + badgeClass(status) + '">' + status.toUpperCase() + '</span></div>' +
      '<div class="tester-result-meta"><span>HTTP: ' + escapeHtml(httpStatus || '-') + '</span><span>Token: ' + (test.auth ? (currentToken() ? 'enviado' : 'requerido/no enviado') : 'no requerido') + '</span></div>' +
      '<div class="tester-result-summary"><strong>Resumen</strong><p>' + escapeHtml(compactSummary(response)) + '</p></div>' +
      '<details open><summary>Respuesta cruda JSON</summary><pre>' + escapeHtml(typeof response === 'string' ? response : pretty(response)) + '</pre></details>' +
      '<div class="tester-observations"><strong>Observaciones</strong><ul>' + (notes.length ? notes.map(function (note) { return '<li>' + escapeHtml(note) + '</li>'; }).join('') : '<li>Sin observaciones criticas.</li>') + '</ul><strong>Siguiente accion</strong><p>' + escapeHtml(next) + '</p></div>';
    state.results = state.results.filter(function (item) { return item.id !== test.id; });
    state.results.push({ id: test.id, group: test.group, endpoint: resolveEndpoint(test.endpoint), status: status, notes: notes });
    renderSummary();
  }

  function renderSummary() {
    if (!summaryBox) { return; }
    if (!state.results.length) {
      summaryBox.innerHTML = '<p class="muted">Aun no hay pruebas ejecutadas.</p>';
      return;
    }
    var groups = {};
    state.results.forEach(function (item) {
      if (!groups[item.group]) { groups[item.group] = []; }
      groups[item.group].push(item);
    });
    summaryBox.innerHTML = Object.keys(groups).map(function (group) {
      var items = groups[group];
      var errors = items.filter(function (item) { return item.status === 'error'; }).length;
      var warns = items.filter(function (item) { return item.status === 'warn'; }).length;
      var ok = items.filter(function (item) { return item.status === 'ok'; }).length;
      var priority = errors ? 'Alta' : (warns ? 'Media' : 'Baja');
      return '<div class="tester-summary-block"><h3>' + escapeHtml(group.replace(/_/g, ' ')) + '</h3><p><strong>Ya funciona:</strong> ' + ok + '. <strong>Advertencias:</strong> ' + warns + '. <strong>Errores:</strong> ' + errors + '. <strong>Prioridad:</strong> ' + priority + '.</p><ul>' +
        items.map(function (item) { return '<li><span class="badge ' + badgeClass(item.status) + '">' + item.status.toUpperCase() + '</span> <strong>' + escapeHtml(item.endpoint) + '</strong>: ' + escapeHtml(item.notes.length ? item.notes.join('; ') : 'Contrato respondio sin faltantes detectados.') + '</li>'; }).join('') +
        '</ul></div>';
    }).join('');
  }

  function renderPlaceholders() {
    tests.forEach(function (test) {
      var host = root.querySelector('[data-tests="' + test.group + '"]');
      if (!host) { return; }
      var card = document.createElement('article');
      card.className = 'tester-result-card is-idle';
      card.setAttribute('data-test-card', test.id);
      card.innerHTML = '<div class="tester-result-head"><div><strong>' + escapeHtml(test.name) + '</strong><small>' + escapeHtml(test.method + ' /' + test.endpoint) + '</small></div><button class="btn btn-light btn-sm" type="button" data-run-test="' + test.id + '">Probar</button></div>';
      host.appendChild(card);
    });
  }

  function renderCategoryTree(items) {
    var host = root.querySelector('[data-category-tree]');
    if (!host) { return; }
    if (!items.length) {
      host.innerHTML = '<p class="muted">Sin categorias recibidas.</p>';
      return;
    }
    host.innerHTML = flattenCategories(items).map(function (item) {
      return '<label class="tester-category-option" style="--level:' + item.level + '"><input type="checkbox" data-category-check value="' + escapeHtml(item.id_categoria_erp || item.id || '') + '" data-name="' + escapeHtml(item.nombre || '') + '" data-route="' + escapeHtml(item.ruta || item.nombre || '') + '"><span>' + escapeHtml(item.ruta || item.nombre || 'Categoria') + '</span><small>Incluye descendientes</small></label>';
    }).join('');
  }

  function flattenCategories(items, level, parentRoute) {
    level = level || 0;
    parentRoute = parentRoute || '';
    var out = [];
    (items || []).forEach(function (item) {
      var name = item.nombre || item.categoria || '';
      var route = item.ruta || (parentRoute ? parentRoute + ' > ' + name : name);
      out.push(Object.assign({}, item, { level: level, ruta: route }));
      out = out.concat(flattenCategories(item.hijos || item.children || item.descendientes || [], level + 1, route));
    });
    return out;
  }

  function syncSelectedCategories() {
    var selected = Array.prototype.slice.call(root.querySelectorAll('[data-category-check]:checked')).map(function (input) {
      return {
        id_categoria_erp: parseInt(input.value || '0', 10) || null,
        nombre: input.getAttribute('data-name') || '',
        ruta: input.getAttribute('data-route') || '',
        incluye_descendientes: true
      };
    });
    var registro = readJsonEditor('registro');
    registro.categorias_interes = selected;
    writeJsonEditor('registro', registro);
    var count = root.querySelector('[data-category-count]');
    if (count) { count.textContent = selected.length + ' seleccionadas'; }
  }

  function renderAdminChecklist() {
    var host = root.querySelector('[data-admin-checklist]');
    if (!host) { return; }
    var items = [
      'Prospectos pendientes con folio, estatus, contacto, negocio, categorias, IP y user agent',
      'Aprobar, rechazar o suspender sin aprobacion automatica desde Distribucion',
      'Generar y copiar link de activacion; abrir WhatsApp con mensaje manual',
      'Asignar permisos comerciales y lista de precios solo en ERP',
      'Configurar catalogo general o personalizado por categoria, marca o SKU',
      'Ocultar productos especificos y permitir categorias padre con descendientes',
      'Revisar Mi catalogo, cotizaciones, pedidos y partidas del cliente',
      'Confirmar cantidades, indicar faltantes, costo de envio y comentarios visibles',
      'Conservar historial de cambios, accesos y auditoria comercial'
    ];
    host.innerHTML = items.map(function (item) {
      return '<label class="tester-check-item"><input type="checkbox"> <span>' + escapeHtml(item) + '</span></label>';
    }).join('');
  }

  function boot() {
    Object.keys(payloads).forEach(function (key) { writeJsonEditor(key, payloads[key]); });
    renderPlaceholders();
    renderAdminChecklist();
    renderSummary();

    root.addEventListener('click', function (event) {
      var sectionButton = event.target.closest('[data-section-target]');
      if (sectionButton) {
        var section = sectionButton.getAttribute('data-section-target');
        root.querySelectorAll('[data-section-target]').forEach(function (btn) { btn.classList.toggle('is-active', btn === sectionButton); });
        root.querySelectorAll('[data-section]').forEach(function (panel) { panel.classList.toggle('is-active', panel.getAttribute('data-section') === section); });
      }
      var groupButton = event.target.closest('[data-run-group]');
      if (groupButton) {
        refreshDynamicPayloads();
        var group = groupButton.getAttribute('data-run-group');
        tests.filter(function (test) { return test.group === group; }).reduce(function (chain, test) {
          return chain.then(function () { return request(test); });
        }, Promise.resolve());
      }
      var testButton = event.target.closest('[data-run-test]');
      if (testButton) {
        refreshDynamicPayloads();
        var test = tests.filter(function (item) { return item.id === testButton.getAttribute('data-run-test'); })[0];
        if (test) { request(test); }
      }
      if (event.target.closest('[data-load-categories]')) { request(tests[2]); }
      if (event.target.closest('[data-clear-results]')) { state.results = []; renderSummary(); }
    });

    root.addEventListener('change', function (event) {
      if (event.target.matches('[data-category-check]')) { syncSelectedCategories(); }
      if (event.target === tokenInput) { window.localStorage.setItem('distribucion_tester_token', tokenInput.value.trim()); }
    });
  }

  boot();
})();
