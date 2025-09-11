CREATE TABLE facturas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_cliente INT NOT NULL,
  fecha DATE,
  total DECIMAL(10,2) NOT NULL,
  estado VARCHAR(1) DEFAULT NULL,
  cae VARCHAR(50) DEFAULT NULL,
  fecha_vencimiento_cae DATE DEFAULT NULL,
  numero_comprobante VARCHAR(50) DEFAULT NULL,
  FOREIGN KEY (id_cliente) REFERENCES clientes(id)
);

CREATE TABLE factura_detalle (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_factura INT NOT NULL,
  descripcion VARCHAR(255) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  FOREIGN KEY (id_factura) REFERENCES facturas(id) ON DELETE CASCADE
);

ALTER TABLE cobros ADD COLUMN id_factura INT NULL;
ALTER TABLE cobros ADD FOREIGN KEY (id_factura) REFERENCES facturas(id);
