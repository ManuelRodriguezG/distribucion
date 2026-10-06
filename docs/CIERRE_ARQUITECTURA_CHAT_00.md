# Cierre arquitectura Chat 00 - Distribucion

## Proposito

Registrar el cierre del bloque de arquitectura, roadmap, alcance MVP y decisiones tecnicas generales antes de iniciar implementacion.

## Decision principal

Distribucion sera una plataforma externa para clientes, revendedores, mayoristas y distribuidores autorizados.

El ERP en `C:\xampp\htdocs\panel_de_control` seguira siendo la fuente de verdad para:

- Catalogo.
- SKUs.
- Variantes.
- Presentaciones.
- Paquetes.
- Imagenes.
- Categorias.
- Marcas.
- Atributos.
- Listas de precios.
- Inventario.
- Disponibilidad comercial.
- Clientes autorizados.
- Permisos comerciales.
- Aprobacion.
- Cotizaciones registradas.

Distribucion en `C:\xampp\htdocs\distribucion` sera consumidor de API y responsable de:

- Experiencia externa.
- Sesion externa de cliente.
- Registro/login visual.
- Catalogo visual.
- Detalle de producto.
- Carrito o cotizacion temporal.
- Consumo de API ERP.
- Confirmacion de folio devuelto por ERP.

## Alcance MVP aprobado

El MVP buscara validar este flujo:

1. Cliente solicita acceso.
2. ERP revisa y aprueba.
3. ERP asigna tipo/lista/permisos.
4. Cliente aprobado inicia sesion en Distribucion.
5. Cliente navega catalogo autorizado.
6. Cliente ve precios y disponibilidad segun ERP.
7. Cliente arma cotizacion.
8. ERP valida con dry-run.
9. ERP registra cotizacion y genera folio.
10. Ventas atiende manualmente desde ERP.

## Fuera del MVP

No implementar en el MVP:

- Pago en linea.
- Pedido automatico.
- Venta directa.
- Facturacion.
- Apartado de inventario.
- Descuento de inventario.
- Cuenta corriente.
- Promociones complejas.
- Historial avanzado.
- Administracion comercial completa dentro de Distribucion.
- Catalogo editable fuera del ERP.

## Documentos creados

- `docs/RESPONSABILIDADES_ERP_DISTRIBUCION.md`
- `docs/GUIA_IMPLEMENTACION_MVP_DISTRIBUCION.md`
- `docs/ROADMAP_PARALELO_ERP_DISTRIBUCION.md`
- `docs/CONTRATO_MINIMO_API_MVP_DISTRIBUCION.md`
- `docs/DECISIONES_TECNICAS_MVP_DISTRIBUCION.md`
- `docs/ALCANCE_FUNCIONAL_MVP_PANTALLAS.md`
- `docs/CRITERIOS_ACEPTACION_MVP_DISTRIBUCION.md`
- `docs/PRIMEROS_PASOS_IMPLEMENTACION_MVP.md`

## Archivos de contexto actualizados

- `.agents/modulos/00-arquitectura-y-roadmap.md`
- `.agents/PROMPTS_PARA_CHATS.md`

## Proximos chats recomendados

### Primero ERP

Abrir primero:

- `ERP 01 - Crear contratos base de DistribucionApi`

Objetivo:

- Crear/confirmar `DistribucionApi`.
- Exponer `contratos`.
- Exponer `configuracion_inicial`.
- Confirmar formato JSON y errores.

Despues:

- `ERP 02 - Clientes externos, aprobacion y permisos`
- `ERP 03 - Catalogo, precios, disponibilidad y cotizaciones`

### Despues Distribucion

Abrir cuando existan endpoints base o mocks claramente temporales:

- `Distribucion 01 - Autenticacion y sesion externa`
- `Distribucion 02 - Catalogo visual`
- `Distribucion 03 - Precios, permisos y disponibilidad`
- `Distribucion 04 - Cotizaciones`
- `Distribucion 05 - UI/UX y estados visuales`
- `Distribucion 06 - Pruebas, seguridad y cierre MVP`

## Reglas que no deben romperse

- No duplicar catalogo maestro en Distribucion.
- No duplicar listas de precios en Distribucion.
- No calcular precios finales en Distribucion.
- No calcular disponibilidad real en Distribucion.
- No mostrar stock exacto en MVP.
- No exponer costos, margenes ni proveedores.
- No registrar ventas automaticas.
- No crear pedidos automaticos.
- No descontar inventario desde Distribucion.
- No usar sesion interna ERP para clientes externos.
- No consultar tablas internas del ERP desde Distribucion.

## Decision pendiente

Autenticacion/token:

- Opcion recomendada MVP: token opaco generado por ERP, guardado/revocable en ERP.
- Opcion alternativa: JWT firmado si el ERP ya cuenta con infraestructura de expiracion, firma, rotacion y revocacion.

La decision final debe tomarse al implementar `DistribucionApi/auth/login` en el ERP.

## Criterio para cerrar arquitectura

Este bloque queda listo porque:

- Hay frontera clara entre ERP y Distribucion.
- Hay alcance MVP definido.
- Hay roadmap paralelo.
- Hay contratos minimos de API.
- Hay criterios de aceptacion.
- Hay prompts copiables para iniciar chats separados.

La siguiente etapa ya puede ser implementacion, empezando por el ERP.
