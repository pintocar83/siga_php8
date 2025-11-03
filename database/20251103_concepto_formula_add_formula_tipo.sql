ALTER TABLE modulo_nomina.concepto_formula ADD formula_tipo VARCHAR(1) DEFAULT '';
INSERT INTO modulo_nomina.periodo_tipo(tipo, denominacion) VALUES ('S','NOMINA SEMANAL');
UPDATE modulo_nomina.periodo_tipo SET denominacion='NOMINA QUINCENAL' WHERE tipo='Q';
UPDATE modulo_nomina.nomina SET tipo='';

ALTER TABLE modulo_nomina.concepto_formula DROP CONSTRAINT concepto_formula_pkey;
ALTER TABLE modulo_nomina.concepto_formula ADD CONSTRAINT concepto_formula_pkey PRIMARY KEY (id_concepto,fecha,formula_tipo);