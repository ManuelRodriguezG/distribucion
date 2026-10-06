# Modulo 05 - Inventario y disponibilidad comercial

## Objetivo

Mostrar disponibilidad comercial sin exponer stock exacto.

## Estados permitidos

- `disponible`.
- `pocas_piezas`.
- `bajo_pedido`.
- `consultar_disponibilidad`.
- `no_disponible`.

## Reglas

- ERP calcula desde inventario real.
- Distribucion solo muestra estado comercial.
- No mostrar ubicaciones, lotes, movimientos ni existencia exacta.
- No apartar inventario.
- No modificar inventario.

## Endpoint

- `POST /DistribucionApi/disponibilidad/resolver`.

## Pendientes

- Definir colores/labels de UI.
- Definir politica por canal.
- Definir umbrales de `pocas_piezas` en ERP.
- Definir productos bajo pedido.

