# Instrucciones copiables para la API de Distribucion

Necesito crear una API de Distribucion dentro del ERP principal `panel_de_control`, manteniendo el ERP como fuente de verdad y dejando el proyecto externo `distribucion` como consumidor. La API debe ser segura, versionada y orientada a clientes externos, revendedores, mayoristas y distribuidores autorizados.

## Objetivo

Crear un contrato API para que la plataforma `C:\xampp\htdocs\distribucion` pueda consultar catalogo, precios autorizados, disponibilidad comercial y registrar solicitudes/cotizaciones sin duplicar logica principal del ERP.

## Principio central

El proyecto Distribucion no debe consultar directamente tablas internas del ERP ni duplicar reglas de negocio. Toda informacion sensible debe resolverse en el ERP mediante endpoints controlados.

El ERP sigue siendo la fuente de verdad para:

- Productos maestros.
- SKUs vendibles.
- Variantes.
- Presentaciones.
- Aperturas de empaque.
- Paquetes.
- Imagenes.
- Categorias.
- Marcas.
- Atributos.
- Reglas de unidad, venta fraccionaria, inventario y trazabilidad.
- Estados operativos.
- Listas de precios.
- Inventario real.
- Disponibilidad comercial.
- Clientes autorizados.
- Tipos de cliente.
- Permisos comerciales.
- Solicitudes y cotizaciones.

## Arquitectura deseada

Crear en el ERP un controlador API separado, por ejemplo:

```text
app/controladores/DistribucionApi.php
app/modelos/DistribucionCatalogoApi.php
app/modelos/DistribucionClientesApi.php
app/modelos/DistribucionCotizacionesApi.php
app/modelos/DistribucionPermisosApi.php
app/modelos/DistribucionApiEsquema.php
```

El controlador debe exponer endpoints JSON para el proyecto externo. Debe aceptar CORS solo desde origenes autorizados y responder OPTIONS cuando aplique.

Origenes autorizados para CORS:

```text
http://distribucion.artiani.com.local
https://distribucion.artiani.com.mx
```

El proyecto externo local se ejecuta como:

```text
http://distribucion.artiani.com.local
```

El dominio productivo previsto sera:

```text
https://distribucion.artiani.com.mx
```

## Nombre de API

```text
ERP Distribucion API
```

Version inicial:

```text
fase1-2026-09-08
```

Canal comercial:

```text
distribucion
```

## Tipos de cliente

Soportar estos tipos:

```text
publico
registrado
revendedor
mayorista
distribuidor_autorizado
administrador_interno
```

Los tipos no deben decidir todo por si solos. Deben combinarse con permisos granulares y lista de precios asignada.

## Estados de cliente

```text
pendiente
en_revision
aprobado
rechazado
suspendido
```

Un cliente no aprobado puede iniciar sesion si se decide permitirlo, pero solo debe ver informacion limitada.

## Permisos comerciales

Crear o resolver estos permisos:

```text
distribucion.catalogo.ver
distribucion.catalogo.ver_detalle
distribucion.precio.ver_publico
distribucion.precio.ver_mayoreo
distribucion.precio.ver_lista_asignada
distribucion.inventario.ver_disponibilidad
distribucion.cotizacion.solicitar
distribucion.pedido.preliminar
distribucion.catalogo.descargar
distribucion.cuenta.editar
```

La API debe devolver al frontend las acciones permitidas para cada usuario.

## Reglas de precio

La API debe resolver precios en este orden:

1. Si el cliente tiene `distribucion.precio.ver_lista_asignada` y una lista activa asignada, devolver precio de esa lista.
2. Si no tiene lista asignada pero tiene `distribucion.precio.ver_publico`, devolver precio publico autorizado.
3. Si tiene permiso de mayoreo general, devolver precio mayoreo segun regla ERP.
4. Si no tiene permiso, no devolver precio numerico y mostrar mensaje `Solicitar precio`.
5. Si el SKU no existe en la lista asignada, aplicar politica configurada: ocultar precio, usar precio publico o marcar como consultar.

Nunca devolver costos, margenes internos, proveedores, costos promedio, utilidad ni stock exacto en endpoints externos.

## Reglas de inventario

No mostrar stock exacto en el MVP. Mostrar disponibilidad comercial:

```text
disponible
pocas_piezas
bajo_pedido
consultar_disponibilidad
no_disponible
```

El ERP debe calcular esa disponibilidad desde Inventario, pero Distribucion solo recibe el estado comercial y un mensaje.

## Endpoints publicos o semi-publicos

### GET /DistribucionApi/contratos

Devuelve manifiesto de contratos, version, endpoints disponibles, reglas generales, permisos posibles y ejemplos de payload.

### GET /DistribucionApi/configuracion_inicial

Devuelve configuracion inicial para arrancar frontend:

```json
{
  "error": false,
  "api": {
    "nombre": "ERP Distribucion API",
    "version": "fase1-2026-09-08",
    "canal": "distribucion",
    "fuente_verdad": "ERP"
  },
  "sesion": {
    "autenticado": false,
    "tipo_cliente": "publico",
    "estatus": null,
    "permisos": []
  },
  "ui": {
    "precio_oculto_label": "Solicitar precio",
    "disponibilidad_default": "consultar_disponibilidad"
  }
}
```

### POST /DistribucionApi/auth/registro

Registra una solicitud de acceso comercial.

Payload propuesto para solicitud de mayoreo:

```json
{
  "nombre": "Nombre del contacto",
  "nombre_negocio": "Nombre comercial o empresa",
  "empresa": "Nombre comercial o empresa",
  "correo": "correo@dominio.com",
  "telefono": "5555555555",
  "whatsapp": "5555555555",
  "rfc": "RFC opcional",
  "ciudad": "Guadalajara",
  "estado": "Jalisco",
  "tipo_interes": "mayorista",
  "tipo_negocio": "petshop",
  "calle": "Calle",
  "numero_exterior": "123",
  "numero_interior": "A",
  "colonia": "Colonia",
  "codigo_postal": "00000",
  "referencias": "Local, mercado, plaza o zona",
  "intereses_comerciales": "Marcas, categorias o volumen aproximado",
  "mensaje": "Comentarios adicionales"
}
```

Notas de contrato:

- `tipo_interes` inicial para Distribucion debe ser `mayorista`; por ahora no ofrecer `distribuidor_autorizado` en el portal externo.
- `tipo_negocio` debe admitir al menos: `venta_internet`, `veterinaria`, `petshop`, `acuario`, `acuario_petshop`, `estetica_canina`, `criador`, `vendedor_mercado`, `vendedor_ambulante`, `otro`.
- Objetivo futuro: permitir contacto sin correo si existe telefono o WhatsApp suficiente para seguimiento; mientras login siga siendo por correo, el ERP puede mantener correo requerido.

Respuesta:

```json
{
  "error": false,
  "mensaje": "Solicitud recibida. Tu acceso sera revisado.",
  "folio": "DIST-20260908-0001",
  "estatus": "pendiente"
}
```

No debe aprobar automaticamente listas de precio ni permisos sensibles.

### POST /DistribucionApi/auth/login

Autentica cliente externo.

Payload:

```json
{
  "correo": "correo@dominio.com",
  "contrasenia": "secreta"
}
```

Respuesta:

```json
{
  "error": false,
  "token": "jwt_o_token_seguro",
  "perfil": {
    "id_cliente_distribucion": 123,
    "nombre": "Cliente",
    "tipo_cliente": "mayorista",
    "estatus": "aprobado",
    "id_lista_precio": 4,
    "permisos": [
      "distribucion.catalogo.ver",
      "distribucion.precio.ver_lista_asignada",
      "distribucion.cotizacion.solicitar"
    ]
  }
}
```

## Endpoints de catalogo

### GET /DistribucionApi/catalogo

Lista productos/SKUs visibles para el usuario o publico.

Parametros:

```text
q
categoria
marca
atributos
pagina
limite
orden
```

Reglas:

- Solo devolver productos activos para canal distribucion.
- Respetar visibilidad por cliente/grupo/canal.
- No devolver costos.
- No devolver stock exacto.
- Precio solo si el usuario tiene permiso.
- Incluir acciones disponibles por item.

Respuesta de item:

```json
{
  "id_producto": 10,
  "id_sku": 22,
  "slug": "producto-ejemplo",
  "sku": "SKU-001",
  "nombre": "Producto ejemplo",
  "marca": "Marca",
  "categoria": "Categoria",
  "imagen_principal": "https://...",
  "presentacion": "Caja 12 piezas",
  "precio": {
    "visible": true,
    "tipo": "lista_asignada",
    "moneda": "MXN",
    "monto": 125.5,
    "mensaje": null
  },
  "disponibilidad": {
    "visible": true,
    "estado": "disponible",
    "mensaje": "Disponible para cotizacion"
  },
  "acciones": {
    "ver_detalle": true,
    "solicitar_precio": false,
    "agregar_cotizacion": true
  }
}
```

### GET /DistribucionApi/producto/{slug}

Devuelve ficha comercial del producto/SKU.

Debe incluir:

- Datos comerciales autorizados.
- Imagenes.
- Variantes.
- Presentaciones.
- Paquetes relacionados.
- Atributos tecnicos visibles.
- Precio resuelto segun permiso.
- Disponibilidad comercial.
- Acciones permitidas.

No debe incluir:

- Costos.
- Proveedores.
- Margenes.
- Stock exacto salvo permiso explicito futuro.
- Campos internos de auditoria.

### GET /DistribucionApi/categorias

Devuelve arbol o listado de categorias visibles para distribucion.

### GET /DistribucionApi/marcas

Devuelve marcas visibles con conteos opcionales.

### GET /DistribucionApi/filtros

Devuelve filtros disponibles segun catalogo visible.

## Endpoints de precios y disponibilidad

### POST /DistribucionApi/precios/resolver

Resuelve precios para una lista de SKUs.

Payload:

```json
{
  "items": [
    { "id_sku": 22, "cantidad": 3 },
    { "id_sku": 31, "cantidad": 1 }
  ]
}
```

La respuesta debe recalcular en servidor y no confiar en precios enviados por frontend.

### POST /DistribucionApi/disponibilidad/resolver

Resuelve disponibilidad comercial para varios SKUs.

## Endpoints de cotizacion

### POST /DistribucionApi/cotizacion/dryrun

Valida una cotizacion sin guardar.

Reglas:

- Recalcular precios en ERP.
- Validar permisos del cliente.
- Validar visibilidad de cada SKU.
- Validar disponibilidad comercial.
- No apartar inventario.
- No crear venta.
- No crear pedido.
- No modificar stock.

Payload:

```json
{
  "items": [
    {
      "id_sku": 22,
      "cantidad": 3
    }
  ],
  "comentarios": "Favor de cotizar entrega"
}
```

Respuesta:

```json
{
  "error": false,
  "mensaje": "Cotizacion validada",
  "totales": {
    "moneda": "MXN",
    "subtotal": 376.5,
    "total_estimado": 376.5
  },
  "items": [
    {
      "id_sku": 22,
      "cantidad": 3,
      "precio_unitario": 125.5,
      "subtotal": 376.5,
      "disponibilidad": "disponible",
      "valido": true
    }
  ],
  "guardrails": {
    "no_aparta_inventario": true,
    "no_crea_venta": true,
    "requiere_revision_interna": true
  }
}
```

### POST /DistribucionApi/cotizacion/registrar

Registra una solicitud real de cotizacion.

Reglas:

- Requiere cliente autenticado y permiso `distribucion.cotizacion.solicitar`.
- Recalcula todo antes de guardar.
- Guarda snapshot comercial.
- Genera folio.
- No aparta inventario.
- No crea venta.
- No crea pedido automatico.

Respuesta:

```json
{
  "error": false,
  "mensaje": "Cotizacion registrada",
  "folio": "COT-DIST-20260908-0001",
  "estatus": "recibida"
}
```

## Endpoints internos ERP para administracion

Crear un controlador interno, por ejemplo `DistribucionAdmin`, protegido por sesion ERP y permisos internos.

Rutas sugeridas:

```text
GET  /DistribucionAdmin/solicitudes
GET  /DistribucionAdmin/solicitud_detalle
POST /DistribucionAdmin/cliente_aprobar
POST /DistribucionAdmin/cliente_rechazar
POST /DistribucionAdmin/cliente_suspendir
POST /DistribucionAdmin/asignar_tipo_cliente
POST /DistribucionAdmin/asignar_lista_precio
POST /DistribucionAdmin/asignar_permisos
GET  /DistribucionAdmin/cotizaciones
GET  /DistribucionAdmin/cotizacion_detalle
POST /DistribucionAdmin/cotizacion_accion_plan
POST /DistribucionAdmin/cotizacion_convertir_plan
```

Permisos ERP sugeridos:

```text
distribucion.ver
distribucion.editar
distribucion.aprobar_clientes
distribucion.asignar_precios
distribucion.cotizaciones.ver
distribucion.cotizaciones.gestionar
```

## Seguridad minima

- HTTPS en produccion.
- CORS limitado a dominios autorizados.
- Token seguro para clientes externos.
- Rate limit en login, registro y cotizacion.
- Auditoria de aprobaciones y cambios de lista.
- No exponer errores SQL.
- No exponer costos, margenes ni proveedores.
- No confiar en precios/cantidades calculadas por frontend.
- Validar canal `distribucion`.
- Versionar contratos.

## Tablas o entidades sugeridas

Si el ERP no cuenta aun con equivalentes, crear entidades para:

```text
erp_distribucion_clientes
erp_distribucion_solicitudes
erp_distribucion_cliente_permisos
erp_distribucion_cliente_listas
erp_distribucion_cotizaciones
erp_distribucion_cotizacion_items
erp_distribucion_tokens
erp_distribucion_auditoria
```

Pero si ya existen clientes, prospectos, permisos o listas en ERP, preferir reutilizarlos con campos de canal antes de duplicar entidades.

## Integracion con Ecommerce

No mezclar directamente los endpoints de Ecommerce Publico con Distribucion.

Crear servicios compartidos internos si conviene:

```text
CatalogoCanalesErp
PreciosCanalesErp
DisponibilidadComercialErp
```

Luego consumirlos desde:

```text
EcommercePublico
DistribucionApi
```

Ecommerce puede seguir siendo publico/SEO/precio publico. Distribucion debe ser autenticado/permisos/listas/cotizacion.

## Criterios de aceptacion MVP

- Distribucion puede consultar `configuracion_inicial`.
- Distribucion puede registrar solicitud de acceso.
- Cliente aprobado puede iniciar sesion.
- Catalogo devuelve solo productos visibles para canal distribucion.
- Precio se muestra solo con permiso.
- Disponibilidad no expone stock exacto.
- Cotizacion dry-run recalcula todo en ERP.
- Cotizacion registrar genera folio sin afectar inventario.
- Admin ERP puede aprobar cliente y asignar lista.
- Admin ERP puede ver cotizaciones recibidas.
