# Integración Cta. Cte. Clientes

Rama de trabajo: `feature/cta-cte-clientes`.

## Objetivo

Reemplazar progresivamente la planilla de Google Sheets `Cta. Cte. Clientes` manteniendo la lógica de cuenta corriente, registro de IPC, precios por servicio, historial y ajustes periódicos.

## Instalación inicial

1. Hacer backup de la base `facturacion`.
2. Ejecutar `migrations/20261028_cta_cte_clientes.sql`.
3. Abrir `ctaCteClientes.php` desde el menú.

## Tablas nuevas

- `ipc_mensual`
- `cliente_servicio`
- `cliente_servicio_precio`
- `cliente_cta_cte_movimiento`

## Criterios

- Los movimientos se almacenan, no se almacena el saldo: el saldo se obtiene como `SUM(debe-haber)`.
- Los precios conservan historial.
- El IPC acumulado es compuesto: `(1+p1)*(1+p2)*...-1`.
- La frecuencia de ajuste se expresa en meses, permitiendo mensual (1), trimestral (3) y cuatrimestral (4).
- El redondeo es configurable por servicio y siempre hacia arriba para reproducir la lógica utilizada actualmente.
- Los ajustes IPC se deben previsualizar y confirmar antes de modificar precios.

## Migración de datos

La estructura está preparada para importar la planilla, pero la importación de datos debe realizarse vinculando cada hoja/cliente de la planilla con el `clientes.id` correspondiente de la base. No se debe inferir esa relación únicamente por nombre si existe ambigüedad.
