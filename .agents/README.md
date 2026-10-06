# Mapa de trabajo por chats - Distribucion

Este directorio divide el proyecto en contextos pequeños para trabajar por modulos sin saturar un solo chat.

## Como usarlo

Para iniciar un chat nuevo, copia una instruccion como esta:

```text
Estamos trabajando en el proyecto Distribucion en C:\xampp\htdocs\distribucion.
Lee AGENTS.md y despues .agents/modulos/XX-nombre-del-modulo.md.
No leas todo el proyecto salvo que sea necesario. Trabaja solo este modulo.
```

Si el modulo toca el ERP, agrega:

```text
Tambien revisa C:\xampp\htdocs\panel_de_control\AGENTS.md y respeta que el ERP es la fuente de verdad.
```

## Orden recomendado

1. Arquitectura y roadmap.
2. API ERP y contratos.
3. Autenticacion de clientes.
4. Catalogo visual.
5. Precios, permisos y listas.
6. Disponibilidad comercial.
7. Cotizaciones/prepedidos.
8. Administracion interna en ERP.
9. UI/UX y assets.
10. Pruebas, deploy y seguridad.

## Regla de ahorro de tokens

Cada chat debe leer:

- `AGENTS.md`.
- Solo un archivo de `.agents/modulos/`.
- `docs/API_DISTRIBUCION_INSTRUCCIONES.md` solo si toca API.
- Archivos PHP/JS puntuales usando busquedas `rg`.

No abrir archivos grandes del ERP sin una razon concreta.

