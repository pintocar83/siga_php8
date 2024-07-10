ALTER TABLE modulo_base.retencion ADD formula_presentacion VARCHAR(100) DEFAULT '';
ALTER TABLE modulo_base.factura ADD id_retencion_islr integer DEFAULT NULL;
ALTER TABLE modulo_base.factura ADD FOREIGN KEY (id_retencion_islr) REFERENCES modulo_base.retencion(id);