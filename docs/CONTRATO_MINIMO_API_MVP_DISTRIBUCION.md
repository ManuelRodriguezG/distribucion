# Contrato minimo API MVP - Distribucion

## Proposito

Definir el minimo de datos que ERP y Distribucion deben intercambiar para completar el MVP. Este documento no reemplaza `docs/API_DISTRIBUCION_INSTRUCCIONES.md`; lo resume como lista de integracion inicial.

## Convencion general de respuesta

Toda respuesta de la API debe usar una estructura consistente:

```json
{
  "error": false,
  "mensaje": "Operacion exitosa",
  "data": {}
}
```

Para errores:

```json
{
  "error": true,
  "codigo": "codigo_legible",
  "mensaje": "Mensaje seguro para cliente",
  "data": {}
}
```

Reglas:

- No exponer errores SQL.
- No exponer costos, margenes, proveedores ni stock exacto.
- No confiar en precios enviados desde frontend.
- Revalidar permisos en cada endpoint privado.
- Mantener sesion externa separada de la sesion interna del ERP.

## Autenticacion

Endpoints publicos:

- `GET /DistribucionApi/contratos`
- `GET /DistribucionApi/configuracion_inicial`
- `POST /DistribucionApi/auth/registro`
- `POST /DistribucionApi/auth/login`

Endpoints privados:

- Catalogo autorizado, si se decide requerir login para todo catalogo.
- Perfil.
- Precios.
- Disponibilidad.
- Cotizaciones.

Encabezado sugerido para endpoints privados:

```text
Authorization: Bearer {token}
```

## GET /DistribucionApi/contratos

Objetivo:

- Permitir que Distribucion conozca version, endpoints y reglas generales disponibles.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Contratos disponibles",
  "data": {
    "api": {
      "nombre": "ERP Distribucion API",
      "version": "fase1-2026-09-08",
      "canal": "distribucion"
    },
    "endpoints": [
      "configuracion_inicial",
      "auth/registro",
      "auth/login",
      "catalogo",
      "producto/{slug}",
      "precios/resolver",
      "disponibilidad/resolver",
      "cotizacion/dryrun",
      "cotizacion/registrar"
    ]
  }
}
```

## GET /DistribucionApi/configuracion_inicial

Objetivo:

- Entregar configuracion inicial para pintar UI y saber estado de sesion.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Configuracion inicial",
  "data": {
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
}
```

## POST /DistribucionApi/auth/registro

Objetivo:

- Registrar solicitud de acceso comercial.

Payload minimo:

```json
{
  "nombre": "Nombre del contacto",
  "empresa": "Empresa",
  "correo": "correo@dominio.com",
  "telefono": "5555555555",
  "tipo_interes": "revendedor",
  "mensaje": "Estoy interesado en distribuir productos"
}
```

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Solicitud recibida. Tu acceso sera revisado.",
  "data": {
    "folio": "DIST-20260908-0001",
    "estatus": "pendiente"
  }
}
```

Reglas:

- No aprobar automaticamente.
- No asignar lista de precios automaticamente salvo decision explicita.
- No devolver permisos sensibles.

## POST /DistribucionApi/auth/login

Objetivo:

- Autenticar cliente externo.

Payload minimo:

```json
{
  "correo": "correo@dominio.com",
  "contrasenia": "secreta"
}
```

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Sesion iniciada",
  "data": {
    "token": "token_seguro",
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
}
```

Reglas:

- Si el cliente esta pendiente, rechazado o suspendido, la API debe responder con estado claro.
- El token es externo a Distribucion y no debe abrir sesion interna del ERP.

## GET /DistribucionApi/catalogo

Objetivo:

- Devolver productos visibles para el canal y cliente.

Parametros minimos:

```text
q
categoria
marca
pagina
limite
orden
```

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Catalogo",
  "data": {
    "pagina": 1,
    "limite": 24,
    "total": 100,
    "items": [
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
    ]
  }
}
```

Reglas:

- Solo productos autorizados para canal distribucion.
- Precio puede venir oculto.
- Disponibilidad debe ser comercial, no stock exacto.

## GET /DistribucionApi/producto/{slug}

Objetivo:

- Devolver ficha comercial autorizada.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Producto",
  "data": {
    "id_producto": 10,
    "slug": "producto-ejemplo",
    "nombre": "Producto ejemplo",
    "descripcion": "Descripcion comercial",
    "marca": "Marca",
    "categoria": "Categoria",
    "imagenes": ["https://..."],
    "skus": [
      {
        "id_sku": 22,
        "sku": "SKU-001",
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
          "agregar_cotizacion": true,
          "solicitar_precio": false
        }
      }
    ],
    "atributos": []
  }
}
```

## GET /DistribucionApi/categorias

Objetivo:

- Devolver categorias visibles para Distribucion.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Categorias",
  "data": {
    "items": [
      {
        "id_categoria": 1,
        "nombre": "Categoria",
        "slug": "categoria",
        "total_productos": 10
      }
    ]
  }
}
```

## GET /DistribucionApi/marcas

Objetivo:

- Devolver marcas visibles para Distribucion.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Marcas",
  "data": {
    "items": [
      {
        "id_marca": 1,
        "nombre": "Marca",
        "slug": "marca",
        "total_productos": 10
      }
    ]
  }
}
```

## GET /DistribucionApi/filtros

Objetivo:

- Devolver filtros disponibles segun catalogo visible.

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Filtros",
  "data": {
    "categorias": [],
    "marcas": [],
    "atributos": []
  }
}
```

## POST /DistribucionApi/precios/resolver

Objetivo:

- Resolver precios de uno o varios SKUs para el cliente autenticado.

Payload minimo:

```json
{
  "items": [
    {
      "id_sku": 22,
      "cantidad": 3
    }
  ]
}
```

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Precios resueltos",
  "data": {
    "items": [
      {
        "id_sku": 22,
        "cantidad": 3,
        "precio": {
          "visible": true,
          "tipo": "lista_asignada",
          "moneda": "MXN",
          "monto": 125.5,
          "mensaje": null
        },
        "puede_cotizar": true
      }
    ]
  }
}
```

## POST /DistribucionApi/disponibilidad/resolver

Objetivo:

- Resolver disponibilidad comercial para uno o varios SKUs.

Payload minimo:

```json
{
  "items": [
    {
      "id_sku": 22,
      "cantidad": 3
    }
  ]
}
```

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Disponibilidad resuelta",
  "data": {
    "items": [
      {
        "id_sku": 22,
        "cantidad": 3,
        "disponibilidad": {
          "visible": true,
          "estado": "disponible",
          "mensaje": "Disponible para cotizacion"
        },
        "puede_cotizar": true
      }
    ]
  }
}
```

Estados permitidos:

- `disponible`
- `pocas_piezas`
- `bajo_pedido`
- `consultar_disponibilidad`
- `no_disponible`

## POST /DistribucionApi/cotizacion/dryrun

Objetivo:

- Validar cotizacion sin guardar.

Payload minimo:

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

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Cotizacion validada",
  "data": {
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
        "valido": true,
        "mensaje": null
      }
    ],
    "guardrails": {
      "no_aparta_inventario": true,
      "no_crea_venta": true,
      "requiere_revision_interna": true
    }
  }
}
```

Reglas:

- Recalcular precios en ERP.
- Validar permisos.
- Validar visibilidad.
- Validar disponibilidad.
- No guardar cotizacion.
- No apartar inventario.
- No crear venta.

## POST /DistribucionApi/cotizacion/registrar

Objetivo:

- Registrar solicitud real de cotizacion.

Payload minimo:

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

Respuesta minima:

```json
{
  "error": false,
  "mensaje": "Cotizacion registrada",
  "data": {
    "folio": "COT-DIST-20260908-0001",
    "estatus": "recibida"
  }
}
```

Reglas:

- Requiere cliente autenticado.
- Requiere permiso `distribucion.cotizacion.solicitar`.
- Recalcula todo antes de guardar.
- Guarda snapshot comercial.
- Genera folio en ERP.
- No aparta inventario.
- No crea venta.
- No crea pedido automatico.

## Criterio de contrato minimo completo

El contrato minimo se considera completo cuando Distribucion puede:

1. Leer configuracion inicial.
2. Registrar solicitud de acceso.
3. Iniciar sesion con cliente aprobado.
4. Consultar catalogo.
5. Consultar detalle.
6. Resolver precios.
7. Resolver disponibilidad.
8. Validar cotizacion.
9. Registrar cotizacion con folio.

Sin duplicar datos maestros ni reglas comerciales fuera del ERP.
