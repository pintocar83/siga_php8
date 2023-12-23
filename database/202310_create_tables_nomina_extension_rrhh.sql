CREATE TABLE modulo_nomina.extension_rrhh_hoja (
    id SERIAL,
    codigo character varying(10),
    descripcion character varying(200),
    tipo character varying(1),
    id_periodo integer[],
    id_nomina integer[],
    activo boolean DEFAULT true,
    id_hoja_plantilla integer,
    PRIMARY KEY (id),
    FOREIGN KEY (tipo) REFERENCES modulo_nomina.periodo_tipo (tipo)
);

CREATE TABLE modulo_nomina.extension_rrhh_hoja_columna
(
    id SERIAL,
    nombre character varying(200),
    cls character varying(50),
    tipo character varying(20),
    operacion character varying(10),
    valor text,
    orden integer DEFAULT 1,
    ag_grid_state text,
    format character varying(20),
    PRIMARY KEY (id)
);

CREATE TABLE modulo_nomina.extension_rrhh_hoja_fila
(
    id SERIAL,
    id_hoja integer NOT NULL,
    id_nomina integer NOT NULL,
    id_ficha integer NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (id_ficha) REFERENCES modulo_nomina.ficha (id),
    FOREIGN KEY (id_hoja) REFERENCES modulo_nomina.extension_rrhh_hoja (id),
    FOREIGN KEY (id_nomina) REFERENCES modulo_nomina.nomina (id)
);


CREATE TABLE modulo_nomina.extension_rrhh_hoja_valor
(
    id SERIAL,
    id_hoja integer NOT NULL,
    id_nomina integer NOT NULL,
    id_ficha integer,
    id_columna integer,
    valor character varying(200),
    PRIMARY KEY (id),
    FOREIGN KEY (id_columna) REFERENCES modulo_nomina.extension_rrhh_hoja_columna (id),
    FOREIGN KEY (id_ficha) REFERENCES modulo_nomina.ficha (id),
    FOREIGN KEY (id_hoja) REFERENCES modulo_nomina.extension_rrhh_hoja (id),
    FOREIGN KEY (id_nomina) REFERENCES modulo_nomina.nomina (id)
);


CREATE INDEX extension_rrhh_hoja_valor_ficha
    ON modulo_nomina.extension_rrhh_hoja_valor USING btree (id_ficha);

CREATE INDEX extension_rrhh_hoja_valor_hoja
    ON modulo_nomina.extension_rrhh_hoja_valor USING btree (id_hoja);

CREATE INDEX extension_rrhh_hoja_valor_hoja_nomina
    ON modulo_nomina.extension_rrhh_hoja_valor USING btree (id_hoja, id_nomina);

CREATE INDEX extension_rrhh_hoja_valor_hoja_nomina_ficha
    ON modulo_nomina.extension_rrhh_hoja_valor USING btree (id_hoja, id_nomina, id_ficha);

CREATE INDEX extension_rrhh_hoja_valor_nomina
    ON modulo_nomina.extension_rrhh_hoja_valor USING btree (id_nomina);



INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (11, 'ESCALA SALARIAL', '', 'ficha', '', 'escala_salarial', 5, '{"width":167,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (17, 'DIRECCION ADSCRITO', '', 'text', '', '', 16, '{"width":165,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (12, 'ESTRUCTURA PRESUPUESTARIA
', 'align-center', 'ficha', '', 'estructura_presupuestaria', 6, '{"width":115,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (18, 'COMUNIDAD/SECTOR', '', 'text', '', '', 17, '{"width":164,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (1, 'SUELDO MENSUAL', 'align-right', 'concepto', 'MAX', '[89, 94]', 11, '{"width":67,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', 'numeric');
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (3, '% PROFESIONALIZACIÓN', 'align-center', 'concepto', 'MAX', '[85]', 13, '{"width":63,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', '%');
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (13, 'GENERO', 'align-center', 'ficha', '', 'genero', 7, '{"width":45,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (15, 'AÑOS', 'align-center', 'ficha', '', 'antiguedad_anio', 9, '{"width":48,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (10, 'NÓMINA', '', 'ficha', '', 'nomina', 1, '{"width":341,"hide":true,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":true,"rowGroupIndex":0,"pivot":false,"pivotIndex":null,"flex":1}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (7, 'CÉDULA', '', 'ficha', '', 'cedula', 2, '{"width":100,"hide":false,"pinned":"left","sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (8, 'NOMBRES Y APELLIDOS', '', 'ficha', '', 'nombres_apellidos', 3, '{"width":256,"hide":false,"pinned":null,"sort":"asc","sortIndex":0,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (9, 'CARGO', '', 'ficha', '', 'cargo', 4, '{"width":168,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (16, 'DIAS', 'align-center', 'ficha', '', 'antiguedad_dia', 10, '{"width":48,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (2, 'TIEMPO DE SERVICIO
(ADMINISTRACIÓN PUBLICA)', 'align-center', 'concepto', 'MAX_SUM', '[76, 93]', 12, '{"width":67,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (4, 'CONTRATOS 2022', 'yellow align-center', 'select', '', '["SI","NO"]', 14, '{"width":123,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (14, 'FECHA INGRESO', 'align-center', 'ficha', '', 'fecha_ingreso', 8, '{"width":81,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', 'date');
INSERT INTO modulo_nomina.extension_rrhh_hoja_columna VALUES (5, 'TIEMPO', 'yellow align-center', 'text', '', '', 15, '{"width":100,"hide":false,"pinned":null,"sort":null,"sortIndex":null,"aggFunc":null,"rowGroup":false,"rowGroupIndex":null,"pivot":false,"pivotIndex":null,"flex":null}', NULL);
SELECT pg_catalog.setval('modulo_nomina.extension_rrhh_hoja_columna_id_seq', 18, true);



ALTER TABLE modulo_base.persona_juridica ADD cuenta_bancaria VARCHAR(50)[];