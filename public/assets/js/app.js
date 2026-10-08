(function () {
  var storageKey = "distribucion_cotizacion_items";
  var quoteDraftKey = "distribucion_cotizacion_borradores";
  var quoteEditKey = "distribucion_cotizacion_editando";
  var root = document.body.getAttribute("data-url-raiz") || window.location.origin;
  var fiscalFields = ["rfc", "razon_social", "regimen_fiscal", "uso_cfdi", "codigo_postal_fiscal", "correo_facturacion", "comentarios_facturacion"];

  function readCart() {
    try {
      var parsed = JSON.parse(localStorage.getItem(storageKey) || "[]");
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function writeCart(items) {
    localStorage.setItem(storageKey, JSON.stringify(items));
    updateCartCount();
  }

  function readEditState() {
    try {
      var parsed = JSON.parse(localStorage.getItem(quoteEditKey) || "{}");
      return parsed && typeof parsed === "object" ? parsed : {};
    } catch (error) {
      return {};
    }
  }

  function writeEditState(state) {
    localStorage.setItem(quoteEditKey, JSON.stringify(state || {}));
    renderEditState();
  }

  function clearEditState() {
    localStorage.removeItem(quoteEditKey);
    renderEditState();
  }

  function renderEditState() {
    var state = readEditState();
    var nameInput = document.getElementById("cotizacion_borrador_nombre");
    if (nameInput && state.nombre && !nameInput.value) { nameInput.value = state.nombre; }
    var list = document.querySelector("[data-quote-draft-list]");
    if (!list) { return; }
    if (state.id_cotizacion_distribucion) {
      list.innerHTML = "<div class=\"quote-draft-item\"><div><strong>Editando borrador " + escapeHtml(state.folio || state.id_cotizacion_distribucion) + "</strong><small>Los cambios se guardan en Mis cotizaciones.</small></div><div class=\"quote-draft-actions\"><a class=\"btn btn-light btn-sm\" href=\"" + root + "/cotizaciones\">Ver historial</a><button class=\"btn btn-light-danger btn-sm\" type=\"button\" data-clear-edit-draft>Salir del borrador</button></div></div>";
    }
  }

  function money(value) {
    if (value === null || value === undefined || value === "") { return "Pendiente de revisión"; }
    return "$" + Number(value).toLocaleString("es-MX", {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function escapeHtml(value) {
    var div = document.createElement("div");
    div.textContent = value == null ? "" : String(value);
    return div.innerHTML;
  }


  function readDrafts() {
    try {
      var parsed = JSON.parse(localStorage.getItem(quoteDraftKey) || "[]");
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
  }

  function writeDrafts(drafts) {
    localStorage.setItem(quoteDraftKey, JSON.stringify(drafts));
    renderDrafts();
  }

  function renderDrafts() {
    var list = document.querySelector("[data-quote-draft-list]");
    if (!list) { return; }
    var state = readEditState();
    if (state.id_cotizacion_distribucion) { return; }
    list.innerHTML = "<div class=\"empty-row\">Los borradores guardados aparecerán en Mis cotizaciones.</div>";
  }
  function draftPayload() {
    var cart = readCart();
    var edit = readEditState();
    var nameInput = document.getElementById("cotizacion_borrador_nombre");
    var comentarios = document.getElementById("cotizacion_comentarios");
    return {
      id_cotizacion_distribucion: edit.id_cotizacion_distribucion || null,
      nombre: nameInput && nameInput.value.trim() !== "" ? nameInput.value.trim() : (edit.nombre || "Cotización " + new Date().toLocaleString("es-MX")),
      comentarios: comentarios ? comentarios.value : "",
      items: cart.map(function (item) { return {id_sku: Number(item.id_sku), cantidad: Number(item.cantidad || 1), comentario: item.comentario || ""}; })
    };
  }

  function saveCurrentDraft(options) {
    var cart = readCart();
    options = options || {};
    if (!cart.length) { showAlert("Agrega productos antes de guardar la cotización."); return Promise.reject(new Error("empty_cart")); }
    return postJson("/cotizaciones/guardar", draftPayload()).then(function (response) {
      if (response.error) { showAlert(response.mensaje || "No se pudo guardar la cotización"); throw new Error("save_failed"); }
      writeEditState({
        id_cotizacion_distribucion: response.id_cotizacion_distribucion,
        folio: response.folio,
        estatus: response.estatus || "borrador",
        nombre: draftPayload().nombre
      });
      var subtotal = document.getElementById("cotizacion_subtotal");
      var estado = document.getElementById("cotizacion_estado");
      if (subtotal) { subtotal.textContent = money(response.total_estimado); }
      if (estado) { estado.textContent = response.estatus === "borrador" || !response.estatus ? "Borrador" : "Solicitud guardada"; estado.className = "badge badge-warning"; }
      if (!options.quiet) { showAlert("Cotización guardada: " + (response.folio || "borrador"), "ok"); }
      return response;
    });
  }

  function loadDraft(id) {
    var draft = readDrafts().find(function (item) { return String(item.id) === String(id); });
    if (!draft) { return; }
    writeCart(Array.isArray(draft.items) ? draft.items : []);
    renderCart();
    showAlert("Cotización cargada: " + (draft.nombre || "Cotizacion"), "ok");
  }

  function deleteDraft(id) {
    writeDrafts(readDrafts().filter(function (item) { return String(item.id) !== String(id); }));
  }
  function updateCartCount() {
    var total = readCart().reduce(function (sum, item) { return sum + Number(item.cantidad || 1); }, 0);
    document.querySelectorAll("[data-cart-count]").forEach(function (node) { node.textContent = String(total); });
  }

  function showAlert(message, type) {
    var alerta = document.getElementById("cotizacion_alerta");
    if (!alerta) { window.alert(message); return; }
    alerta.textContent = message;
    alerta.classList.remove("is-hidden", "alert-success", "alert-warning");
    alerta.classList.add(type === "ok" ? "alert-success" : "alert-warning");
  }

  function showInlineAdded(button, message) {
    if (!button) { return; }
    var original = button.textContent;
    button.textContent = message;
    button.disabled = true;
    window.setTimeout(function () { button.textContent = original; button.disabled = false; }, 1200);
  }

  function postJson(path, payload) {
    return fetch(root + path, {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: JSON.stringify(payload || {})}).then(function (response) { return response.json(); });
  }

  function valueById(id) {
    var node = document.getElementById(id);
    return node ? String(node.value || "").trim() : "";
  }

  function isChecked(id) {
    var node = document.getElementById(id);
    return !!(node && node.checked);
  }

  function readFacturacionContract() {
    var node = document.getElementById("pedido_facturacion_contract");
    if (!node) { return {}; }
    try {
      var contract = JSON.parse(node.textContent || "{}");
      return contract.facturacion && typeof contract.facturacion === "object" ? contract.facturacion : contract;
    } catch (error) {
      return {};
    }
  }

  function emptyFacturacion() {
    var contract = readFacturacionContract();
    var payload = {};
    fiscalFields.forEach(function (field) { payload[field] = ""; });
    Object.keys(contract || {}).forEach(function (field) {
      if (!Object.prototype.hasOwnProperty.call(payload, field)) { payload[field] = ""; }
    });
    return payload;
  }

  function readEntrega() {
    var direccion = {
      referencia: valueById("pedido_entrega_referencia"),
      comentarios_entrega: valueById("pedido_entrega_comentarios")
    };
    return {
      tipo_entrega: valueById("pedido_entrega_tipo") || "por_definir",
      referencia: direccion.referencia,
      comentarios_entrega: direccion.comentarios_entrega,
      direccion_envio: direccion
    };
  }

  function readFacturacion(requiereFactura) {
    if (!requiereFactura) { return emptyFacturacion(); }
    var payload = emptyFacturacion();
    payload.rfc = valueById("facturacion_rfc").toUpperCase();
    payload.razon_social = valueById("facturacion_razon_social");
    payload.regimen_fiscal = valueById("facturacion_regimen_fiscal");
    payload.uso_cfdi = valueById("facturacion_uso_cfdi").toUpperCase();
    payload.codigo_postal_fiscal = valueById("facturacion_codigo_postal").replace(/\D/g, "").slice(0, 5);
    payload.correo_facturacion = valueById("facturacion_correo").toLowerCase();
    payload.comentarios_facturacion = valueById("facturacion_comentarios");
    return payload;
  }

  function validateItemsPayload(payload, emptyMessage) {
    if (!payload.items || !payload.items.length) { return emptyMessage || "Agrega al menos un producto antes de revisar la solicitud."; }
    var invalid = payload.items.some(function (item) {
      return !item || !Number.isFinite(Number(item.id_sku)) || Number(item.id_sku) <= 0 || !Number.isFinite(Number(item.cantidad)) || Number(item.cantidad) < 1;
    });
    return invalid ? "Cada producto debe tener cantidad mínima de 1." : "";
  }

  function validatePedidoPayload(payload) {
    var itemError = validateItemsPayload(payload, "Agrega al menos un producto antes de enviar la solicitud.");
    if (itemError) { return itemError; }
    if (!payload.requiere_factura) { return ""; }
    var required = {
      rfc: "RFC",
      razon_social: "razon social",
      regimen_fiscal: "regimen fiscal",
      uso_cfdi: "uso CFDI",
      codigo_postal_fiscal: "código postal fiscal",
      correo_facturacion: "correo de facturación"
    };
    var missing = Object.keys(required).filter(function (field) { return !String(payload.facturacion[field] || "").trim(); });
    if (missing.length) { return "Completa los datos fiscales: " + missing.map(function (field) { return required[field]; }).join(", ") + "."; }
    return "";
  }

  function toggleFacturacionFields() {
    var fields = document.querySelector("[data-facturacion-fields]");
    if (fields) { fields.classList.toggle("is-hidden", !isChecked("pedido_requiere_factura")); }
  }

  function readMiCatalogoInputs(button) {
    var row = button.closest("[data-mi-catalogo-row]");
    if (!row) { return {cantidad: 1, comentario: ""}; }
    var qty = row.querySelector("[data-mi-cotizacion-cantidad]");
    var comment = row.querySelector("[data-mi-cotizacion-comentario]");
    return {cantidad: Math.max(1, Number(qty ? qty.value : 1) || 1), comentario: comment ? comment.value.trim() : ""};
  }

  function addItem(idSku, nombre, cantidad, comentario, redirect, button) {
    if (!idSku) { return; }
    var qty = Math.max(1, Number(cantidad || 1) || 1);
    var cart = readCart();
    var found = cart.find(function (item) { return String(item.id_sku) === String(idSku); });
    if (found) {
      found.cantidad = Number(found.cantidad || 1) + qty;
      if (comentario) { found.comentario = comentario; }
      found.nombre = found.nombre || nombre || "Producto";
    } else {
      cart.push({id_sku: Number(idSku), nombre: nombre || "Producto", cantidad: qty, comentario: comentario || ""});
    }
    writeCart(cart);
    if (redirect !== false) { window.location.href = root + "/cotizacion"; } else { showInlineAdded(button, "Agregado a cotización"); }
  }

  function snapshotBySku(response) {
    var snapshot = {};
    (response.items || []).forEach(function (item) { snapshot[item.id_sku] = item; });
    return snapshot;
  }

  function renderCart(snapshot) {
    var container = document.getElementById("cotizacion_items");
    if (!container) { return; }
    var cart = readCart();
    if (!cart.length) {
      container.innerHTML = "<tr><td colspan=\"6\" class=\"empty-row\">Agrega productos desde el catálogo o Mi catálogo.</td></tr>";
    } else {
      container.innerHTML = cart.map(function (item) {
        var snap = snapshot && snapshot[item.id_sku] ? snapshot[item.id_sku] : {};
        var disponibilidad = snap.disponibilidad || "Por revisar";
        var valido = snap.valido === false ? "badge-warning" : "badge-info";
        return "<tr data-id-sku=\"" + escapeHtml(item.id_sku) + "\">" +
          "<td><strong>" + escapeHtml(item.nombre) + "</strong><span class=\"line-muted\">SKU " + escapeHtml(item.id_sku) + "</span></td>" +
          "<td><div class=\"qty-stepper\"><button type=\"button\" data-quote-minus>-</button><input type=\"text\" inputmode=\"numeric\" value=\"" + escapeHtml(item.cantidad || 1) + "\" data-quote-qty><button type=\"button\" data-quote-plus>+</button></div></td>" +
          "<td><input class=\"quote-line-comment\" type=\"text\" maxlength=\"240\" value=\"" + escapeHtml(item.comentario || "") + "\" placeholder=\"Comentario de partida\" data-quote-comment></td>" +
          "<td><span class=\"badge " + valido + "\">" + escapeHtml(disponibilidad) + "</span></td>" +
          "<td class=\"fw-bold\">" + money(snap.subtotal) + "</td>" +
          "<td class=\"text-end\"><button class=\"btn btn-light btn-sm\" type=\"button\" data-quote-remove>Quitar</button></td>" +
        "</tr>";
      }).join("");
    }
    var totalNode = document.getElementById("cotizacion_total_items");
    if (totalNode) { totalNode.textContent = String(cart.length); }
    updateCartCount();
  }

  function comentariosConPartidas(comentarioGeneral, items) {
    var lineas = [];
    var general = String(comentarioGeneral || "").trim();
    if (general !== "") { lineas.push(general); }
    items.forEach(function (item) {
      var comentario = String(item.comentario || "").trim();
      if (comentario !== "") { lineas.push("SKU " + item.id_sku + " - " + (item.nombre || "Producto") + ": " + comentario); }
    });
    return lineas.join("\n");
  }

  function payloadCart() {
    var comentarios = document.getElementById("cotizacion_comentarios");
    var cart = readCart();
    var requiereFactura = isChecked("pedido_requiere_factura") ? 1 : 0;
    var entrega = readEntrega();
    return {
      items: cart.map(function (item) { return {id_sku: Number(item.id_sku), cantidad: Number(item.cantidad)}; }),
      entrega: entrega,
      tipo_entrega: entrega.tipo_entrega,
      direccion_envio: entrega.direccion_envio,
      comentarios: comentariosConPartidas(comentarios ? comentarios.value : "", cart),
      requiere_factura: requiereFactura,
      facturacion: readFacturacion(requiereFactura)
    };
  }

  function applyDryRun(response) {
    renderCart(snapshotBySku(response));
    var subtotal = document.getElementById("cotizacion_subtotal");
    var estado = document.getElementById("cotizacion_estado");
    if (subtotal) { subtotal.textContent = money(response.totales ? response.totales.total_estimado : null); }
    if (estado) { estado.textContent = response.bloqueos && response.bloqueos.length ? "Revisión" : "Listo"; estado.className = "badge " + (response.bloqueos && response.bloqueos.length ? "badge-warning" : "badge-success"); }
    showAlert(response.mensaje || (response.error ? "No fue posible revisar la solicitud" : "Solicitud revisada correctamente."), response.error ? "warn" : "ok");
  }

  function revisarCotizacion() { var payload = payloadCart(); var error = validateItemsPayload(payload, "Agrega al menos un producto antes de revisar la solicitud."); if (error) { showAlert(error); return; } postJson("/cotizacion/dryrun", payload).then(applyDryRun).catch(function () { showAlert("No se pudo revisar la solicitud."); }); }

  function enviarCotizacion() {
    var payload = payloadCart();
    var error = validatePedidoPayload(payload);
    if (error) { showAlert(error); return; }
    saveCurrentDraft({quiet: true}).then(function (draft) {
      return postJson("/cotizaciones/enviar_pedido", {
        id_cotizacion_distribucion: draft.id_cotizacion_distribucion,
        entrega: payload.entrega,
        tipo_entrega: payload.tipo_entrega,
        direccion_envio: payload.direccion_envio,
        comentarios: payload.comentarios,
        requiere_factura: payload.requiere_factura,
        facturacion: payload.facturacion
      });
    }).then(function (response) {
      if (response.error) { showAlert(response.mensaje || "No se pudo enviar la solicitud"); return; }
      writeCart([]); clearEditState(); renderCart();
      var subtotal = document.getElementById("cotizacion_subtotal");
      var estado = document.getElementById("cotizacion_estado");
      if (subtotal) { subtotal.textContent = money(response.total_estimado); }
      if (estado) { estado.textContent = response.estatus_pedido || "Solicitud enviada"; estado.className = "badge badge-success"; }
      showAlert("Solicitud enviada: " + (response.folio_pedido || response.folio || "sin folio"), "ok");
      window.setTimeout(function () { window.location.href = root + "/pedidos"; }, 900);
    }).catch(function () { showAlert("No se pudo enviar la solicitud."); });
  }
  function changeQty(row, delta) {
    var idSku = row.getAttribute("data-id-sku");
    var cart = readCart();
    cart.forEach(function (item) { if (String(item.id_sku) === String(idSku)) { item.cantidad = Math.max(1, Number(item.cantidad || 1) + delta); } });
    writeCart(cart); renderCart();
  }

  function updateCartField(target, field) {
    var idSku = target.closest("[data-id-sku]").getAttribute("data-id-sku");
    var cart = readCart();
    cart.forEach(function (item) {
      if (String(item.id_sku) === String(idSku)) {
        if (field === "cantidad") { var cleanQty = Number(String(target.value).replace(/\D/g, "")); item.cantidad = Math.max(1, cleanQty || 1); target.value = item.cantidad; } else { item.comentario = target.value; }
      }
    });
    writeCart(cart);
  }

  function setCatalogView(mode) {
    var table = document.querySelector(".catalog-table-view");
    var cards = document.querySelector(".catalog-card-view");
    if (!table || !cards) { return; }
    if (mode === "cards" && typeof window.renderCatalogCardsIfNeeded === "function") { window.renderCatalogCardsIfNeeded(); }
    document.querySelectorAll("[data-view-mode]").forEach(function (button) {
      button.classList.toggle("is-active", button.getAttribute("data-view-mode") === mode);
    });
    table.classList.toggle("is-hidden", mode !== "table");
    cards.classList.toggle("is-hidden", mode !== "cards");
  }

  if (window.matchMedia && window.matchMedia("(max-width: 760px)").matches) {
    setCatalogView("cards");
  }

  document.addEventListener("click", function (event) {
    var sidebarButton = event.target.closest("[data-toggle-sidebar]");
    if (sidebarButton) { document.body.classList.toggle("sidebar-open"); return; }
    var mode = event.target.closest("[data-view-mode]");
    if (mode) { setCatalogView(mode.getAttribute("data-view-mode")); return; }
    var add = event.target.closest("[data-add-cotizacion]");
    if (add) { var extra = add.hasAttribute("data-from-mi-catalogo") ? readMiCatalogoInputs(add) : {cantidad: 1, comentario: ""}; addItem(add.getAttribute("data-id-sku"), add.getAttribute("data-nombre"), extra.cantidad, extra.comentario, !add.hasAttribute("data-from-mi-catalogo"), add); return; }
    if (event.target.closest("#cotizacion_revisar")) { revisarCotizacion(); return; }
    if (event.target.closest("#cotizacion_enviar")) { enviarCotizacion(); return; }
    if (event.target.closest("#cotizacion_guardar_borrador")) { saveCurrentDraft(); return; }
    if (event.target.closest("[data-clear-edit-draft]")) { clearEditState(); showAlert("Ya no estas editando un borrador guardado.", "ok"); return; }
    var draftRow = event.target.closest("[data-draft-id]");
    if (draftRow && event.target.closest("[data-load-draft]")) { loadDraft(draftRow.getAttribute("data-draft-id")); return; }
    if (draftRow && event.target.closest("[data-delete-draft]")) { deleteDraft(draftRow.getAttribute("data-draft-id")); return; }
    var plus = event.target.closest("[data-quote-plus]");
    if (plus) { changeQty(plus.closest("[data-id-sku]"), 1); return; }
    var minus = event.target.closest("[data-quote-minus]");
    if (minus) { changeQty(minus.closest("[data-id-sku]"), -1); return; }
    var remove = event.target.closest("[data-quote-remove]");
    if (remove) { var idSku = remove.closest("[data-id-sku]").getAttribute("data-id-sku"); writeCart(readCart().filter(function (item) { return String(item.id_sku) !== String(idSku); })); renderCart(); }
  });

  document.addEventListener("input", function (event) {
    if (event.target.hasAttribute("data-quote-qty")) { updateCartField(event.target, "cantidad"); return; }
    if (event.target.hasAttribute("data-quote-comment")) { updateCartField(event.target, "comentario"); return; }
    if (event.target.id === "facturacion_rfc" || event.target.id === "facturacion_uso_cfdi") { event.target.value = event.target.value.toUpperCase(); return; }
    if (event.target.id === "facturacion_correo") { event.target.value = event.target.value.toLowerCase(); return; }
    if (event.target.id === "facturacion_codigo_postal") { event.target.value = event.target.value.replace(/\D/g, "").slice(0, 5); }
  });

  document.addEventListener("change", function (event) {
    if (event.target.id === "pedido_requiere_factura") { toggleFacturacionFields(); }
  });

  updateCartCount();
  renderCart();
  toggleFacturacionFields();
})();

(function () {
  document.addEventListener("click", function (event) {
    var tab = event.target.closest("[data-product-tab]");
    if (!tab) { return; }
    var target = tab.getAttribute("data-product-tab");
    document.querySelectorAll("[data-product-tab]").forEach(function (button) {
      button.classList.toggle("is-active", button === tab);
    });
    document.querySelectorAll("[data-product-panel]").forEach(function (panel) {
      panel.classList.toggle("is-hidden", panel.getAttribute("data-product-panel") !== target);
    });
  });
})();

(function () {
  function setText(selector, value) {
    var node = document.querySelector(selector);
    if (node) { node.textContent = value || ""; }
  }

  function closeProductModal() {
    var modal = document.querySelector("[data-product-modal]");
    if (modal) { modal.classList.add("is-hidden"); }
  }

  document.addEventListener("click", function (event) {
    var open = event.target.closest("[data-open-product-modal]");
    if (open) {
      var modal = document.querySelector("[data-product-modal]");
      if (!modal) { return; }
      setText("[data-modal-sku]", open.getAttribute("data-sku") || "SKU");
      setText("[data-modal-title]", open.getAttribute("data-nombre") || "Producto");
      setText("[data-modal-category]", open.getAttribute("data-categoria") || "");
      setText("[data-modal-brand]", open.getAttribute("data-marca") || "Sin marca");
      setText("[data-modal-presentation]", open.getAttribute("data-presentacion") || "Unidad");
      setText("[data-modal-price]", open.getAttribute("data-precio") || "Solicitar precio");
      setText("[data-modal-availability]", open.getAttribute("data-disponibilidad") || "Consultar disponibilidad");
      var media = document.querySelector("[data-modal-image]");
      var image = open.getAttribute("data-imagen") || "";
      if (media) { media.innerHTML = image ? "<img src=\"" + image.replace(/\"/g, "&quot;") + "\" alt=\"Producto\" loading=\"lazy\" decoding=\"async\">" : "<span class=\"icon icon-boxes\"></span>"; }
      var link = document.querySelector("[data-modal-full-link]");
      if (link) {
        var slug = open.getAttribute("data-slug") || "";
        link.href = slug ? ((document.body.getAttribute("data-url-raiz") || window.location.origin) + "/catalogo/producto/" + encodeURIComponent(slug)) : "#";
        link.classList.toggle("is-hidden", slug === "");
      }
      modal.classList.remove("is-hidden");
      return;
    }
    if (event.target.closest("[data-close-product-modal]") || event.target.matches("[data-product-modal]")) {
      closeProductModal();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") { closeProductModal(); }
  });
})();

(function () {
var originalTableHtml = null;
  var originalCardsHtml = null;
  var catalogIndex = null;

  function normalizeText(value) {
    return String(value || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
  }

  function escapeHtml(value) {
    var div = document.createElement("div");
    div.textContent = value == null ? "" : String(value);
    return div.innerHTML;
  }

  function readCatalogIndex() {
    if (catalogIndex !== null) { return catalogIndex; }
    var node = document.getElementById("catalogo_indice_json");
    if (!node) { catalogIndex = {items: [], mi_catalogo_ids: [], puede_mi_catalogo: false, url_raiz: ""}; return catalogIndex; }
    try {
      catalogIndex = JSON.parse(node.textContent || "{}");
    } catch (error) {
      catalogIndex = {};
    }
    catalogIndex.items = Array.isArray(catalogIndex.items) ? catalogIndex.items : [];
    catalogIndex.mi_catalogo_ids = Array.isArray(catalogIndex.mi_catalogo_ids) ? catalogIndex.mi_catalogo_ids.map(String) : [];
    catalogIndex.url_raiz = catalogIndex.url_raiz || (document.body.getAttribute("data-url-raiz") || window.location.origin);
    return catalogIndex;
  }

  function priceText(item) {
    var precio = item && item.precio && typeof item.precio === "object" ? item.precio : {};
    if (precio.visible && precio.monto !== undefined && precio.monto !== null && precio.monto !== "") {
      return "$" + Number(precio.monto).toLocaleString("es-MX", {minimumFractionDigits: 2, maximumFractionDigits: 2}) + " " + (precio.moneda || "MXN");
    }
    return precio.mensaje || "Solicitar precio";
  }

  function badgeClass(estado) {
    var map = {disponible: "badge-success", pocas_piezas: "badge-warning", bajo_pedido: "badge-info", consultar_disponibilidad: "badge-light", no_disponible: "badge-danger"};
    return map[estado] || "badge-light";
  }

  function itemSearchText(item) {
    return normalizeText([
      item.id_sku,
      item.sku,
      item.nombre,
      item.categoria,
      item.marca,
      item.presentacion,
      priceText(item)
    ].join(" "));
  }

  function actionButtons(item, data) {
    var idSku = item.id_sku == null ? "" : String(item.id_sku);
    var acciones = item.acciones && typeof item.acciones === "object" ? item.acciones : {};
    var saved = data.mi_catalogo_ids.indexOf(idSku) !== -1;
    var html = "<button class=\"btn btn-light btn-sm\" type=\"button\" data-open-product-modal" +
      " data-nombre=\"" + escapeHtml(item.nombre || "Producto") + "\"" +
      " data-sku=\"" + escapeHtml(item.sku || "") + "\"" +
      " data-categoria=\"" + escapeHtml(item.categoria || "") + "\"" +
      " data-marca=\"" + escapeHtml(item.marca || "Sin marca") + "\"" +
      " data-presentacion=\"" + escapeHtml(item.presentacion || "Unidad") + "\"" +
      " data-precio=\"" + escapeHtml(priceText(item)) + "\"" +
      " data-disponibilidad=\"" + escapeHtml(item.disponibilidad && item.disponibilidad.mensaje ? item.disponibilidad.mensaje : (item.disponibilidad && item.disponibilidad.estado ? item.disponibilidad.estado : "Consultar disponibilidad")) + "\"" +
      " data-imagen=\"" + escapeHtml(item.imagen_principal || "") + "\"" +
      " data-slug=\"" + escapeHtml(item.slug || "") + "\">Ver detalle</button>";
    if (data.puede_mi_catalogo && idSku !== "") {
      html += "<button class=\"btn btn-light btn-sm mi-catalogo-toggle " + (saved ? "is-saved" : "") + "\" type=\"button\" data-mi-catalogo-toggle data-id-sku=\"" + escapeHtml(idSku) + "\" data-estatus=\"" + (saved ? "inactivo" : "activo") + "\">" + (saved ? "En Mi catálogo" : "Agregar a Mi catálogo") + "</button>";
    }
    if (acciones.agregar_cotizacion) {
      html += "<button class=\"btn btn-primary btn-sm\" type=\"button\" data-add-cotizacion data-id-sku=\"" + escapeHtml(idSku) + "\" data-nombre=\"" + escapeHtml(item.nombre || "Producto") + "\">Agregar a cotización</button>";
    } else if (acciones.solicitar_precio) {
      html += "<a class=\"btn btn-light btn-sm\" href=\"" + escapeHtml(data.url_raiz) + "/auth/registro\">Solicitar precio</a>";
    }
    return html;
  }

  function renderTableRow(item, data) {
    var disponibilidad = item.disponibilidad && typeof item.disponibilidad === "object" ? item.disponibilidad : {};
    var estado = disponibilidad.estado || "consultar_disponibilidad";
    var image = item.imagen_principal ? "<img src=\"" + escapeHtml(item.imagen_principal) + "\" alt=\"" + escapeHtml(item.nombre || "Producto") + "\" loading=\"lazy\" decoding=\"async\">" : "<span class=\"thumb-placeholder icon icon-boxes\"></span>";
    return "<tr data-catalog-row data-id-sku=\"" + escapeHtml(item.id_sku || "") + "\">" +
      "<td><div class=\"product-cell\">" + image + "<div><strong>" + escapeHtml(item.nombre || "Producto") + "</strong><small>ID SKU " + escapeHtml(item.id_sku || "") + "</small></div></div></td>" +
      "<td class=\"catalog-category-cell\">" + escapeHtml(item.categoria || "Sin categoria visible") + "</td>" +
      "<td><span class=\"badge badge-light\">" + escapeHtml(item.sku || "") + "</span></td>" +
      "<td>" + escapeHtml(item.marca || "Sin marca") + "</td>" +
      "<td>" + escapeHtml(item.presentacion || "Unidad") + "</td>" +
      "<td class=\"fw-bold\">" + escapeHtml(priceText(item)) + "</td>" +
      "<td><span class=\"badge " + badgeClass(estado) + "\">" + escapeHtml(estado) + "</span><small class=\"line-muted\">" + escapeHtml(disponibilidad.mensaje || "Consultar disponibilidad") + "</small></td>" +
      "<td class=\"text-end table-actions action-text-buttons\">" + actionButtons(item, data) + "</td>" +
    "</tr>";
  }

  function renderCard(item, data) {
    var disponibilidad = item.disponibilidad && typeof item.disponibilidad === "object" ? item.disponibilidad : {};
    var estado = disponibilidad.estado || "consultar_disponibilidad";
    var image = item.imagen_principal ? "<img src=\"" + escapeHtml(item.imagen_principal) + "\" alt=\"" + escapeHtml(item.nombre || "Producto") + "\" loading=\"lazy\" decoding=\"async\">" : "<span class=\"icon icon-boxes\"></span>";
    return "<article class=\"product-card product-card-final\" data-catalog-card data-id-sku=\"" + escapeHtml(item.id_sku || "") + "\"><div class=\"product-media\">" + image + "</div><div class=\"product-card-body\"><div class=\"product-tags\"><span class=\"badge badge-light\">" + escapeHtml(item.sku || "SKU") + "</span><span class=\"badge " + badgeClass(estado) + "\">" + escapeHtml(estado) + "</span></div><h3>" + escapeHtml(item.nombre || "Producto") + "</h3><p>" + escapeHtml(item.categoria || "Sin categoria visible") + "</p><div class=\"product-meta-row\"><span>" + escapeHtml(item.marca || "Sin marca") + "</span><span>" + escapeHtml(item.presentacion || "Unidad") + "</span></div><div class=\"price-line\">" + escapeHtml(priceText(item)) + "</div><div class=\"actions product-actions action-text-buttons\">" + actionButtons(item, data) + "</div></div></article>";
  }

  function renderCatalogCardsIfNeeded() {
    var cardList = document.querySelector("[data-catalog-card-list]");
    if (!cardList || cardList.getAttribute("data-cards-rendered") === "1") { return; }
    var data = readCatalogIndex();
    cardList.innerHTML = data.items.length ? data.items.map(function (item) { return renderCard(item, data); }).join("") : "<div class=\"empty-row\">Sin productos visibles en esta página.</div>";
    cardList.setAttribute("data-cards-rendered", "1");
    originalCardsHtml = cardList.innerHTML;
  }
  window.renderCatalogCardsIfNeeded = renderCatalogCardsIfNeeded;

  function restoreOriginal(tableBody, cardList) {
    if (originalTableHtml !== null) { tableBody.innerHTML = originalTableHtml; }
    if (cardList && originalCardsHtml !== null) { cardList.innerHTML = originalCardsHtml; cardList.setAttribute("data-cards-rendered", "1"); }
  }

  function applyLocalTableFilter(input) {
    var tableBody = document.querySelector("[data-catalog-table-body]");
    if (!tableBody) { return; }
    var cardList = document.querySelector("[data-catalog-card-list]");
    if (originalTableHtml === null) { originalTableHtml = tableBody.innerHTML; }
    if (cardList && originalCardsHtml === null) { renderCatalogCardsIfNeeded(); originalCardsHtml = cardList.innerHTML; }
    var data = readCatalogIndex();
    var query = normalizeText(input.value);
    if (query === "") {
      restoreOriginal(tableBody, cardList);
      var originalRows = tableBody.querySelectorAll("[data-catalog-row]").length;
      var originalCounter = document.querySelector("[data-local-filter-count]");
      if (originalCounter) { originalCounter.textContent = originalRows + " visibles"; }
      return;
    }
    var matches = data.items.filter(function (item) { return itemSearchText(item).indexOf(query) !== -1; });
    tableBody.innerHTML = matches.length ? matches.map(function (item) { return renderTableRow(item, data); }).join("") : "<tr><td colspan=\"8\" class=\"empty-row\">Sin coincidencias en los productos cargados.</td></tr>";
    if (cardList) { cardList.innerHTML = matches.map(function (item) { return renderCard(item, data); }).join(""); }
    var counter = document.querySelector("[data-local-filter-count]");
    if (counter) { counter.textContent = matches.length + " coincidencias"; }
    var status = document.querySelector("[data-catalog-filter-status]");
    if (status) { status.textContent = data.items.length + " productos en esta pagina"; }
  }

  document.addEventListener("input", function (event) {
    if (!event.target.hasAttribute("data-table-local-search")) { return; }
    applyLocalTableFilter(event.target);
  });

  if (window.matchMedia && window.matchMedia("(max-width: 760px)").matches) {
    renderCatalogCardsIfNeeded();
  }
})();

(function () {
  function pedidoStatusLabel(value) {
    var map = {pedido_solicitado: "Pedido recibido", en_revision: "En revisión comercial", respondida: "Respuesta enviada", requiere_ajuste_cliente: "Ajuste solicitado", cliente_acepto: "Propuesta aceptada", aceptado: "Propuesta aceptada", cliente_rechazo: "Propuesta no aceptada", rechazado: "Propuesta no aceptada", cancelado: "Cancelado", terminado: "Completado", completado: "Completado", sin_estatus: "En revisión", pendiente_respuesta_cliente: "Pendiente de tu respuesta"};
    return map[value] || "En revisión";
  }

  function entregaLabel(value) {
    var map = {por_definir: "Entrega por confirmar", envio: "Envío", recoger_tienda: "Recoger en tienda"};
    return map[value] || "Entrega por confirmar";
  }

  function revisionLabel(value) {
    var map = {por_confirmar: "Por confirmar", disponible: "Disponible", no_disponible: "No disponible", parcial: "Parcial", pendiente: "Pendiente de revisión"};
    return map[value] || (value ? String(value).replace(/_/g, " ") : "Por confirmar");
  }

  function setText(selector, value) {
    var node = document.querySelector(selector);
    if (node) { node.textContent = value || ""; }
  }

  function closeModal() {
    var modal = document.querySelector("[data-mi-catalogo-modal]");
    if (modal) { modal.classList.add("is-hidden"); }
  }

  document.addEventListener("click", function (event) {
    var open = event.target.closest("[data-open-mi-catalogo-modal]");
    if (open) {
      var modal = document.querySelector("[data-mi-catalogo-modal]");
      if (!modal) { return; }
      setText("[data-mi-modal-sku]", open.getAttribute("data-sku"));
      setText("[data-mi-modal-title]", open.getAttribute("data-nombre"));
      setText("[data-mi-modal-category]", open.getAttribute("data-categoria"));
      setText("[data-mi-modal-brand]", open.getAttribute("data-marca"));
      setText("[data-mi-modal-presentation]", open.getAttribute("data-presentacion"));
      var media = document.querySelector("[data-mi-modal-image]");
      var image = open.getAttribute("data-imagen") || "";
      if (media) { media.innerHTML = image ? "<img src=\"" + image.replace(/\"/g, "&quot;") + "\" alt=\"Producto\" loading=\"lazy\" decoding=\"async\">" : "<span class=\"icon icon-boxes\"></span>"; }
      modal.classList.remove("is-hidden");
      return;
    }
    if (event.target.closest("[data-close-mi-catalogo-modal]") || event.target.matches("[data-mi-catalogo-modal]")) {
      closeModal();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") { closeModal(); }
  });
})();
(function () {
  var root = document.body.getAttribute("data-url-raiz") || window.location.origin;

  function postJson(path, payload) {
    return fetch(root + path, {
      method: "POST",
      headers: {"Content-Type": "application/json"},
      credentials: "same-origin",
      body: JSON.stringify(payload || {})
    }).then(function (response) {
      return response.json();
    });
  }

  function feedback(message, ok) {
    var node = document.querySelector("[data-mi-catalogo-feedback]");
    if (!node) {
      window.alert(message);
      return;
    }
    node.textContent = message;
    node.classList.remove("is-hidden", "alert-success", "alert-warning");
    node.classList.add(ok ? "alert-success" : "alert-warning");
  }

  function setButtons(idSku, saved) {
    document.querySelectorAll("[data-mi-catalogo-toggle][data-id-sku='" + String(idSku).replace(/'/g, "\\'") + "']").forEach(function (button) {
      button.disabled = false;
      button.classList.toggle("is-saved", saved);
      button.setAttribute("data-estatus", saved ? "inactivo" : "activo");
      if (button.closest("[data-mi-catalogo-row]")) {
        button.textContent = "Quitar de Mi catálogo";
      } else {
        button.textContent = saved ? "En Mi catálogo" : "Agregar a Mi catálogo";
      }
    });
  }

  function updateCount(delta) {
    document.querySelectorAll("[data-mi-catalogo-count]").forEach(function (node) {
      var current = Number(node.textContent || 0);
      node.textContent = String(Math.max(0, current + delta));
    });
  }

  function ensureEmptyState() {
    var list = document.querySelector("[data-mi-catalogo-list]");
    if (!list || list.querySelector("[data-mi-catalogo-row]")) { return; }
    list.innerHTML = "<tr><td colspan=\"7\" class=\"empty-row\">Aun no tienes productos guardados en Mi catálogo.</td></tr>";
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest("[data-mi-catalogo-toggle]");
    if (!button) { return; }
    var idSku = button.getAttribute("data-id-sku");
    var estatus = button.getAttribute("data-estatus") || "activo";
    if (!idSku) { return; }
    button.disabled = true;
    postJson("/mi-catalogo/guardar", {id_sku: Number(idSku), estatus: estatus}).then(function (response) {
      if (response.configurado === false) {
        feedback("Esta función estará disponible próximamente.", false);
        button.disabled = false;
        return;
      }
      if (response.error) {
        feedback(response.mensaje || "No se pudo actualizar Mi catálogo.", false);
        button.disabled = false;
        return;
      }
      var saved = estatus === "activo";
      setButtons(idSku, saved);
      if (!saved) {
        var row = button.closest("[data-mi-catalogo-row]");
        if (row) {
          row.remove();
          updateCount(-1);
          ensureEmptyState();
        }
      } else {
        updateCount(1);
      }
      feedback(response.mensaje || (saved ? "Producto guardado en Mi catálogo." : "Producto quitado de Mi catálogo."), true);
    }).catch(function () {
      button.disabled = false;
      feedback("No se pudo conectar con Mi catálogo.", false);
    });
  });
})();






(function () {
  var root = document.body.getAttribute("data-url-raiz") || window.location.origin;
  var currentPedido = null;
  var currentPedidoItems = [];
  var cartKey = "distribucion_cotizacion_items";

  function escapeHtml(value) {
    var div = document.createElement("div");
    div.textContent = value == null ? "" : String(value);
    return div.innerHTML;
  }

  function money(value) {
    if (value === null || value === undefined || value === "" || isNaN(Number(value))) { return "Pendiente de revisión"; }
    return "$" + Number(value).toLocaleString("es-MX", {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function pedidoStatusLabel(value) {
    var map = {pedido_solicitado: "Pedido recibido", en_revision: "En revisión comercial", respondida: "Respuesta enviada", requiere_ajuste_cliente: "Ajuste solicitado", cliente_acepto: "Propuesta aceptada", aceptado: "Propuesta aceptada", cliente_rechazo: "Propuesta no aceptada", rechazado: "Propuesta no aceptada", cancelado: "Cancelado", terminado: "Completado", completado: "Completado", sin_estatus: "En revisión", pendiente_respuesta_cliente: "Pendiente de tu respuesta"};
    return map[value] || "En revisión";
  }

  function entregaLabel(value) {
    var map = {por_definir: "Entrega por confirmar", envio: "Envío", recoger_tienda: "Recoger en tienda"};
    return map[value] || "Entrega por confirmar";
  }

  function revisionLabel(value) {
    var map = {por_confirmar: "Por confirmar", disponible: "Disponible", no_disponible: "No disponible", parcial: "Parcial", pendiente: "Pendiente de revisión"};
    return map[value] || (value ? String(value).replace(/_/g, " ") : "Por confirmar");
  }

  function setText(selector, value) {
    var node = document.querySelector(selector);
    if (node) { node.textContent = value || ""; }
  }

  function showPedidoAlert(message, ok) {
    var node = document.querySelector("[data-pedido-modal-alert]");
    if (!node) { window.alert(message); return; }
    node.textContent = message;
    node.classList.remove("is-hidden", "alert-success", "alert-warning");
    node.classList.add(ok ? "alert-success" : "alert-warning");
  }

  function closePedidoModal() {
    var modal = document.querySelector("[data-pedido-modal]");
    if (modal) { modal.classList.add("is-hidden"); }
  }

  function fetchJson(path) {
    return fetch(root + path, {credentials: "same-origin"}).then(function (response) { return response.json(); });
  }

  function postJson(path, payload) {
    return fetch(root + path, {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: JSON.stringify(payload || {})}).then(function (response) { return response.json(); });
  }

  function renderPedidoDetalle(response) {
    if (response.error) { showPedidoAlert(response.mensaje || "No se pudo consultar la solicitud"); return; }
    currentPedido = response.pedido || null;
    currentPedidoItems = Array.isArray(response.items) ? response.items : [];
    var modal = document.querySelector("[data-pedido-modal]");
    var body = document.querySelector("[data-pedido-modal-items]");
    if (!modal || !body || !currentPedido) { return; }
    setText("[data-pedido-modal-folio]", currentPedido.folio || "Pedido");
    setText("[data-pedido-modal-title]", "Detalle de solicitud");
    setText("[data-pedido-modal-status]", "Estatus: " + pedidoStatusLabel(currentPedido.estatus || "sin_estatus") + " | Entrega: " + entregaLabel(currentPedido.tipo_entrega || "por_definir"));
    body.innerHTML = currentPedidoItems.length ? currentPedidoItems.map(function (item) {
      var nombre = item.producto_actual || item.nombre_snapshot || "Producto";
      var sku = item.sku_actual || item.sku_snapshot || item.id_sku || "";
      return "<tr>" +
        "<td><strong>" + escapeHtml(nombre) + "</strong></td>" +
        "<td><span class=\"badge badge-light\">" + escapeHtml(sku) + "</span></td>" +
        "<td>" + escapeHtml(item.cantidad || "0") + "</td>" +
        "<td>" + escapeHtml(item.cantidad_confirmada || "Pendiente de revisión") + "</td>" +
        "<td class=\"fw-bold\">" + money(item.precio_unitario_snapshot) + "</td>" +
        "<td class=\"fw-bold\">" + money(item.subtotal_snapshot) + "</td>" +
        "<td><span class=\"badge badge-info\">" + escapeHtml(revisionLabel(item.estatus_revision || item.disponibilidad_snapshot || "por_confirmar")) + "</span><small class=\"line-muted\">" + escapeHtml(item.comentario_revision || "") + "</small></td>" +
      "</tr>";
    }).join("") : "<tr><td colspan=\"7\" class=\"empty-row\">Sin partidas en este pedido.</td></tr>";
    var alert = document.querySelector("[data-pedido-modal-alert]");
    if (alert) { alert.classList.add("is-hidden"); }
    modal.classList.remove("is-hidden");
  }

  function repetirPedido() {
    if (!currentPedidoItems.length) { showPedidoAlert("Esta solicitud no tiene productos para crear una nueva."); return; }
    var items = currentPedidoItems.map(function (item) {
      return {
        id_sku: Number(item.id_sku),
        nombre: item.producto_actual || item.nombre_snapshot || "Producto",
        cantidad: Number(item.cantidad_confirmada || item.cantidad || 1) || 1,
        comentario: "Nueva solicitud basada en " + (currentPedido && currentPedido.folio ? currentPedido.folio : "solicitud anterior")
      };
    }).filter(function (item) { return item.id_sku > 0; });
    localStorage.setItem(cartKey, JSON.stringify(items));
    window.location.href = root + "/cotizacion";
  }

  function responderPedido(respuesta) {
    if (!currentPedido || !currentPedido.id_cotizacion_distribucion) { return; }
    var comentario = document.querySelector("[data-pedido-response-comment]");
    postJson("/pedidos/responder", {
      id_cotizacion_distribucion: Number(currentPedido.id_cotizacion_distribucion),
      respuesta: respuesta,
      comentario: comentario ? comentario.value : "",
      tipo_entrega: currentPedido.tipo_entrega || "por_definir"
    }).then(function (response) {
      if (response.error) { showPedidoAlert(response.mensaje || "No se pudo responder la solicitud"); return; }
      showPedidoAlert(response.mensaje || "Respuesta registrada", true);
      window.setTimeout(function () { window.location.reload(); }, 900);
    }).catch(function () { showPedidoAlert("No se pudo responder la solicitud"); });
  }

  document.addEventListener("click", function (event) {
    var open = event.target.closest("[data-open-pedido-detalle]");
    if (open) {
      fetchJson("/pedidos/detalle?id_cotizacion_distribucion=" + encodeURIComponent(open.getAttribute("data-id"))).then(renderPedidoDetalle).catch(function () { showPedidoAlert("No se pudo consultar la solicitud"); });
      return;
    }
    if (event.target.closest("[data-close-pedido-modal]") || event.target.matches("[data-pedido-modal]")) { closePedidoModal(); return; }
    if (event.target.closest("[data-repeat-pedido]")) { repetirPedido(); return; }
    var responder = event.target.closest("[data-pedido-responder]");
    if (responder) { responderPedido(responder.getAttribute("data-pedido-responder")); }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") { closePedidoModal(); }
  });
})();










(function () {
  var root = document.body.getAttribute("data-url-raiz") || window.location.origin;
  var cartKey = "distribucion_cotizacion_items";
  var editKey = "distribucion_cotizacion_editando";
  var current = null;
  var currentItems = [];

  function escapeHtml(value) {
    var div = document.createElement("div");
    div.textContent = value == null ? "" : String(value);
    return div.innerHTML;
  }

  function money(value) {
    if (value === null || value === undefined || value === "" || isNaN(Number(value))) { return "Pendiente de revisión"; }
    return "$" + Number(value).toLocaleString("es-MX", {minimumFractionDigits: 2, maximumFractionDigits: 2});
  }

  function fetchJson(path) {
    return fetch(root + path, {credentials: "same-origin"}).then(function (response) { return response.json(); });
  }

  function postJson(path, payload) {
    return fetch(root + path, {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: JSON.stringify(payload || {})}).then(function (response) { return response.json(); });
  }

  function feedback(message, ok) {
    var node = document.querySelector("[data-cotizaciones-feedback]") || document.querySelector("[data-cotizacion-modal-alert]");
    if (!node) { window.alert(message); return; }
    node.textContent = message;
    node.classList.remove("is-hidden", "alert-success", "alert-warning");
    node.classList.add(ok ? "alert-success" : "alert-warning");
  }

  function closeModal() {
    var modal = document.querySelector("[data-cotizacion-modal]");
    if (modal) { modal.classList.add("is-hidden"); }
  }

  function pedidoStatusLabel(value) {
    var map = {pedido_solicitado: "Pedido recibido", en_revision: "En revisión comercial", respondida: "Respuesta enviada", requiere_ajuste_cliente: "Ajuste solicitado", cliente_acepto: "Propuesta aceptada", aceptado: "Propuesta aceptada", cliente_rechazo: "Propuesta no aceptada", rechazado: "Propuesta no aceptada", cancelado: "Cancelado", terminado: "Completado", completado: "Completado", sin_estatus: "En revisión", pendiente_respuesta_cliente: "Pendiente de tu respuesta"};
    return map[value] || "En revisión";
  }

  function entregaLabel(value) {
    var map = {por_definir: "Entrega por confirmar", envio: "Envío", recoger_tienda: "Recoger en tienda"};
    return map[value] || "Entrega por confirmar";
  }

  function revisionLabel(value) {
    var map = {por_confirmar: "Por confirmar", disponible: "Disponible", no_disponible: "No disponible", parcial: "Parcial", pendiente: "Pendiente de revisión"};
    return map[value] || (value ? String(value).replace(/_/g, " ") : "Por confirmar");
  }

  function setText(selector, value) {
    var node = document.querySelector(selector);
    if (node) { node.textContent = value || ""; }
  }

  function cotizacionStatusLabel(value) {
    var map = {borrador: "Borrador", recibida: "Recibida por Artiani", enviada_como_pedido: "Solicitud enviada", pedido_solicitado: "Pedido recibido", cancelada: "Cancelada", vencida: "Vencida", sin_estatus: "En revisión", pendiente_respuesta_cliente: "Pendiente de tu respuesta"};
    return map[value] || "En revisión";
  }

  function disponibilidadLabel(value) {
    var map = {por_revisar: "Por revisar", por_confirmar: "Por confirmar", disponible: "Disponible", no_disponible: "No disponible", parcial: "Parcial"};
    return map[value] || (value ? String(value).replace(/_/g, " ") : "Por revisar");
  }

  function renderDetalle(response) {
    if (response.error) { feedback(response.mensaje || "No se pudo consultar la cotización"); return; }
    current = response.cotizacion || null;
    currentItems = Array.isArray(response.items) ? response.items : [];
    var modal = document.querySelector("[data-cotizacion-modal]");
    var body = document.querySelector("[data-cotizacion-modal-items]");
    if (!modal || !body || !current) { return; }
    setText("[data-cotizacion-modal-folio]", current.folio || "Cotización");
    setText("[data-cotizacion-modal-title]", current.nombre_documento || "Detalle de cotización");
    setText("[data-cotizacion-modal-status]", "Estatus: " + cotizacionStatusLabel(current.estatus || "sin_estatus") + " | Total estimado: " + money(current.total_estimado));
    body.innerHTML = currentItems.length ? currentItems.map(function (item) {
      var nombre = item.producto_actual || item.nombre_snapshot || "Producto";
      var sku = item.sku_actual || item.sku_snapshot || item.id_sku || "";
      return "<tr>" +
        "<td><strong>" + escapeHtml(nombre) + "</strong></td>" +
        "<td><span class=\"badge badge-light\">" + escapeHtml(sku) + "</span></td>" +
        "<td>" + escapeHtml(item.cantidad || "0") + "</td>" +
        "<td class=\"fw-bold\">" + money(item.precio_unitario_snapshot) + "</td>" +
        "<td class=\"fw-bold\">" + money(item.subtotal_snapshot) + "</td>" +
        "<td><span class=\"badge badge-info\">" + escapeHtml(disponibilidadLabel(item.disponibilidad_snapshot || "por_revisar")) + "</span></td>" +
      "</tr>";
    }).join("") : "<tr><td colspan=\"6\" class=\"empty-row\">Sin partidas en esta cotización.</td></tr>";
    var editable = current.estatus === "borrador";
    document.querySelectorAll("[data-cotizacion-modal-edit], [data-cotizacion-modal-send]").forEach(function (button) { button.classList.toggle("is-hidden", !editable); });
    var alert = document.querySelector("[data-cotizacion-modal-alert]");
    if (alert) { alert.classList.add("is-hidden"); }
    modal.classList.remove("is-hidden");
  }

  function loadToEditor(cotizacion, items) {
    var cart = (items || []).map(function (item) {
      return {
        id_sku: Number(item.id_sku),
        nombre: item.producto_actual || item.nombre_snapshot || "Producto",
        cantidad: Number(item.cantidad || 1) || 1,
        comentario: item.comentario_cliente || ""
      };
    }).filter(function (item) { return item.id_sku > 0; });
    localStorage.setItem(cartKey, JSON.stringify(cart));
    localStorage.setItem(editKey, JSON.stringify({
      id_cotizacion_distribucion: cotizacion.id_cotizacion_distribucion,
      folio: cotizacion.folio,
      estatus: cotizacion.estatus,
      nombre: cotizacion.nombre_documento || "Cotización"
    }));
    window.location.href = root + "/cotizacion";
  }

  function openDetalle(id) {
    fetchJson("/cotizaciones/detalle?id_cotizacion_distribucion=" + encodeURIComponent(id)).then(renderDetalle).catch(function () { feedback("No se pudo consultar la cotización"); });
  }

  function editarDesdeFila(row) {
    fetchJson("/cotizaciones/detalle?id_cotizacion_distribucion=" + encodeURIComponent(row.getAttribute("data-id"))).then(function (response) {
      if (response.error) { feedback(response.mensaje || "No se pudo cargar la cotización"); return; }
      loadToEditor(response.cotizacion, response.items || []);
    }).catch(function () { feedback("No se pudo cargar la cotización"); });
  }

  function duplicar(id) {
    postJson("/cotizaciones/duplicar", {id_cotizacion_distribucion: Number(id)}).then(function (response) {
      if (response.error) { feedback(response.mensaje || "No se pudo duplicar la cotización"); return; }
      feedback(response.mensaje || "Cotización duplicada", true);
      window.setTimeout(function () { window.location.reload(); }, 700);
    }).catch(function () { feedback("No se pudo duplicar la cotización"); });
  }

  function cancelar(id) {
    if (!window.confirm("¿Cancelar esta cotización?")) { return; }
    postJson("/cotizaciones/cancelar", {id_cotizacion_distribucion: Number(id)}).then(function (response) {
      if (response.error) { feedback(response.mensaje || "No se pudo cancelar la cotización"); return; }
      feedback(response.mensaje || "Cotización cancelada", true);
      window.setTimeout(function () { window.location.reload(); }, 700);
    }).catch(function () { feedback("No se pudo cancelar la cotización"); });
  }

  function enviarPedido(id) {
    postJson("/cotizaciones/enviar_pedido", {
      id_cotizacion_distribucion: Number(id),
      entrega: {tipo_entrega: "por_definir", direccion_envio: {}, comentarios_entrega: ""},
      tipo_entrega: "por_definir",
      requiere_factura: 0,
      facturacion: {rfc: "", razon_social: "", regimen_fiscal: "", uso_cfdi: "", codigo_postal_fiscal: "", correo_facturacion: "", comentarios_facturacion: ""}
    }).then(function (response) {
      if (response.error) { feedback(response.mensaje || "No se pudo enviar la solicitud"); return; }
      feedback("Solicitud enviada: " + (response.folio_pedido || response.folio || "sin folio"), true);
      window.setTimeout(function () { window.location.href = root + "/pedidos"; }, 900);
    }).catch(function () { feedback("No se pudo enviar la solicitud"); });
  }

  document.addEventListener("click", function (event) {
    var row = event.target.closest("[data-cotizacion-row]");
    if (event.target.closest("[data-close-cotizacion-modal]") || event.target.matches("[data-cotizacion-modal]")) { closeModal(); return; }
    if (row && event.target.closest("[data-cotizacion-detalle]")) { openDetalle(row.getAttribute("data-id")); return; }
    if (row && event.target.closest("[data-cotizacion-editar]")) { editarDesdeFila(row); return; }
    if (row && event.target.closest("[data-cotizacion-duplicar]")) { duplicar(row.getAttribute("data-id")); return; }
    if (row && event.target.closest("[data-cotizacion-cancelar]")) { cancelar(row.getAttribute("data-id")); return; }
    if (row && event.target.closest("[data-cotizacion-enviar]")) { enviarPedido(row.getAttribute("data-id")); return; }
    if (event.target.closest("[data-cotizacion-modal-edit]") && current) { loadToEditor(current, currentItems); return; }
    if (event.target.closest("[data-cotizacion-modal-duplicate]") && current) { duplicar(current.id_cotizacion_distribucion); return; }
    if (event.target.closest("[data-cotizacion-modal-send]") && current) { enviarPedido(current.id_cotizacion_distribucion); return; }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") { closeModal(); }
  });
})();

(function () {
  var root = document.body.getAttribute("data-url-raiz") || window.location.origin;

  function postJson(path, payload) {
    return fetch(root + path, {method: "POST", headers: {"Content-Type": "application/json"}, credentials: "same-origin", body: JSON.stringify(payload || {})}).then(function (response) { return response.json(); });
  }

  function feedback(message, ok) {
    var node = document.querySelector("[data-notificaciones-feedback]");
    if (!node) { window.alert(message); return; }
    node.textContent = message;
    node.classList.remove("is-hidden", "alert-success", "alert-warning");
    node.classList.add(ok ? "alert-success" : "alert-warning");
  }

  function notificationId(button) {
    var item = button.closest("[data-notificacion-id]");
    return item ? item.getAttribute("data-notificacion-id") : "";
  }

  function updateTopbarCount(total) {
    document.querySelectorAll("[data-notification-count]").forEach(function (node) {
      var count = Math.max(0, Number(total || 0) || 0);
      node.textContent = String(count);
      node.classList.toggle("has-items", count > 0);
      node.classList.toggle("is-muted", count < 1);
    });
  }

  function refreshSummary() {
    if (!document.querySelector("[data-notification-count]")) { return; }
    fetch(root + "/notificaciones/resumen", {credentials: "same-origin"}).then(function (response) {
      return response.json();
    }).then(function (response) {
      if (response && !response.error) {
        updateTopbarCount(response.no_leidas || response.pendientes || response.total_no_leidas || 0);
      }
    }).catch(function () {});
  }

  document.addEventListener("click", function (event) {
    var readButton = event.target.closest("[data-notificacion-leida]");
    if (readButton) {
      var readId = notificationId(readButton);
      if (!readId) { return; }
      readButton.disabled = true;
      postJson("/notificaciones/marcar_leida", {id_notificacion: readId}).then(function (response) {
        if (response.error) { feedback(response.mensaje || "No se pudo marcar la notificacion.", false); readButton.disabled = false; return; }
        var item = readButton.closest("[data-notificacion-id]");
        if (item) { item.classList.remove("is-unread"); item.classList.add("is-read"); }
        readButton.remove();
        feedback(response.mensaje || "Notificacion marcada como leida.", true);
        refreshSummary();
      }).catch(function () {
        readButton.disabled = false;
        feedback("No se pudo marcar la notificacion.", false);
      });
      return;
    }

    var sendButton = event.target.closest("[data-notificacion-enviar]");
    if (sendButton) {
      var sendId = notificationId(sendButton);
      var channel = sendButton.getAttribute("data-notificacion-enviar");
      if (!sendId || !channel) { return; }
      sendButton.disabled = true;
      postJson("/notificaciones/enviar", {id_notificacion: sendId, canal: channel}).then(function (response) {
        sendButton.disabled = false;
        if (response.error) { feedback(response.mensaje || "No se pudo enviar la notificacion.", false); return; }
        feedback(response.mensaje || "Notificacion enviada por " + channel + ".", true);
      }).catch(function () {
        sendButton.disabled = false;
        feedback("No se pudo enviar la notificacion.", false);
      });
    }
  });

  refreshSummary();
})();

