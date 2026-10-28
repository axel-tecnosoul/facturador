-- Integracion de la planilla "Cta. Cte. Clientes"
-- Ejecutar una sola vez sobre la base facturacion.

CREATE TABLE IF NOT EXISTS ipc_mensual (
  id_ipc INT UNSIGNED NOT NULL AUTO_INCREMENT,
  periodo DATE NOT NULL COMMENT 'Primer dia del mes informado',
  porcentaje DECIMAL(8,4) NOT NULL,
  observaciones VARCHAR(255) NULL,
  fecha_hora_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_ipc),
  UNIQUE KEY uk_ipc_periodo (periodo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cliente_servicio (
  id_cliente_servicio INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cliente INT NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  moneda ENUM('ARS','USD') NOT NULL DEFAULT 'ARS',
  precio_actual DECIMAL(15,2) NOT NULL DEFAULT 0,
  frecuencia_ajuste_meses TINYINT UNSIGNED NULL COMMENT '1 mensual, 3 trimestral, 4 cuatrimestral; NULL sin ajuste automatico',
  mes_proximo_ajuste DATE NULL,
  redondeo DECIMAL(15,2) NOT NULL DEFAULT 0,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  orden SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  fecha_hora_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cliente_servicio),
  KEY idx_cliente_servicio_cliente (id_cliente),
  KEY idx_cliente_servicio_ajuste (mes_proximo_ajuste, activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cliente_servicio_precio (
  id_cliente_servicio_precio INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cliente_servicio INT UNSIGNED NOT NULL,
  vigencia_desde DATE NOT NULL,
  precio_anterior DECIMAL(15,2) NULL,
  porcentaje_aplicado DECIMAL(10,4) NULL,
  precio_nuevo DECIMAL(15,2) NOT NULL,
  origen ENUM('INICIAL','IPC','MANUAL','MIGRACION') NOT NULL DEFAULT 'MANUAL',
  detalle VARCHAR(255) NULL,
  fecha_hora_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_cliente_servicio_precio),
  KEY idx_servicio_precio_servicio (id_cliente_servicio),
  CONSTRAINT fk_servicio_precio_servicio FOREIGN KEY (id_cliente_servicio) REFERENCES cliente_servicio (id_cliente_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cliente_cta_cte_movimiento (
  id_movimiento BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_cliente INT NOT NULL,
  fecha DATE NOT NULL,
  observaciones VARCHAR(500) NOT NULL,
  moneda ENUM('ARS','USD') NOT NULL DEFAULT 'USD',
  debe DECIMAL(15,2) NOT NULL DEFAULT 0,
  haber DECIMAL(15,2) NOT NULL DEFAULT 0,
  pesos_recibidos DECIMAL(15,2) NULL,
  cotizacion_usd DECIMAL(15,4) NULL,
  origen ENUM('MANUAL','SERVICIO','PAGO','MIGRACION') NOT NULL DEFAULT 'MANUAL',
  id_cliente_servicio INT UNSIGNED NULL,
  fecha_hora_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_movimiento),
  KEY idx_cta_cte_cliente_fecha (id_cliente, fecha),
  KEY idx_cta_cte_servicio (id_cliente_servicio),
  CONSTRAINT fk_cta_cte_servicio FOREIGN KEY (id_cliente_servicio) REFERENCES cliente_servicio (id_cliente_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
