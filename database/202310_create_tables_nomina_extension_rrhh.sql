CREATE TABLE modulo_nomina.extension_rrhh_hoja
(
    id SERIAL,
    codigo character varying(10),
    descripcion character varying(200),
    tipo character varying(1),
    id_periodo integer[],
    id_nomina integer[],
    activo boolean DEFAULT true,
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
    visible integer DEFAULT 1,
    ancho integer,
    alinear character varying(10) DEFAULT 'right',
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


INSERT INTO modulo_nomina.extension_rrhh_hoja_columna(nombre, cls, tipo, operacion, valor, orden, visible, ancho, alinear) VALUES
('SUELDO MENSUAL', '', 'concepto', 'MAX', '[89, 94]', 1, 1, NULL, 'right'),
('TIEMPO DE SERVICIO<BR>(ADMINISTRACIÓN PUBLICA)', '', 'concepto', 'MAX_SUM', '[76, 93]', 2, 1, 52, 'right'),
('% PROFESIONALIZACIÓN', '', 'concepto', 'MAX', '[85]', 3, 1, NULL, 'right'),
('CONTRATOS 2022', 'yellow', 'select', '', '["SI","NO"]', 4, 1, 55, 'center'),
('TIEMPO', 'yellow', 'texto', '', '',  5, 1, 108, 'right');