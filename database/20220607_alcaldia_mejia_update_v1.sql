alter table modulo_base.banco add column codigo varchar(4);
alter table modulo_base.banco alter column banco TYPE varchar(100);

INSERT INTO modulo_base.retencion_tipo VALUES (3, '1X1000');

alter table modulo_nomina.ficha add column antiguedad_apn integer default NULL;
alter table modulo_nomina.ficha add column profesionalizacion_porcentaje integer default NULL;


SET search_path = modulo_nomina, pg_catalog;

CREATE TABLE grupo_familiar (
    id integer NOT NULL,
    id_ficha integer NOT NULL,
    id_parentesco integer NOT NULL,
    id_persona bigint NULL,
    nacionalidad character varying(1),
    cedula bigint,
    nombres_apellidos character varying(100),
    genero character varying(1),
    fecha_nacimiento date
);


CREATE SEQUENCE grupo_familiar_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


CREATE TABLE grupo_familiar_parentesco (
    id integer NOT NULL,
    parentesco character varying(30),
    activo boolean DEFAULT true
);


CREATE SEQUENCE grupo_familiar_parentesco_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ONLY grupo_familiar ALTER COLUMN id SET DEFAULT nextval('grupo_familiar_id_seq'::regclass);

ALTER TABLE ONLY grupo_familiar_parentesco ALTER COLUMN id SET DEFAULT nextval('grupo_familiar_parentesco_id_seq'::regclass);

ALTER TABLE ONLY grupo_familiar_parentesco
    ADD CONSTRAINT grupo_familiar_parentesco_pkey PRIMARY KEY (id);

ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_pkey PRIMARY KEY (id);

ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_ficha_fkey FOREIGN KEY (id_ficha) REFERENCES ficha(id);

ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_parentesco_fkey FOREIGN KEY (id_parentesco) REFERENCES grupo_familiar_parentesco(id);

ALTER TABLE ONLY grupo_familiar
    ADD CONSTRAINT grupo_familiar_id_persona_fkey FOREIGN KEY (id_persona) REFERENCES modulo_base.persona(id);

INSERT INTO grupo_familiar_parentesco VALUES (1, 'ESPOSO / ESPOSA / CONYUGUE', true);
INSERT INTO grupo_familiar_parentesco VALUES (2, 'HERMANO / HERMANA', true);
INSERT INTO grupo_familiar_parentesco VALUES (3, 'HIJO / HIJA', true);
INSERT INTO grupo_familiar_parentesco VALUES (4, 'NIETO / NIETA', true);
INSERT INTO grupo_familiar_parentesco VALUES (5, 'PADRE / MADRE', true);
INSERT INTO grupo_familiar_parentesco VALUES (6, 'SUEGRO / SUEGRA', true);
INSERT INTO grupo_familiar_parentesco VALUES (7, 'TÍO / TÍA', true);
SELECT pg_catalog.setval('grupo_familiar_parentesco_id_seq', 7, true);


CREATE TABLE modulo_nomina.nomina_configuracion (
    dato character varying(100) NOT NULL,
    valor TEXT,
    PRIMARY KEY(dato)
);

INSERT INTO modulo_nomina.nomina_configuracion(dato, valor) VALUES
('ficha:escala_salarial', 'SUELDO_MENSUAL'),
('ficha:numero_hijos', 'NUMERO_HIJOS'),
('ficha:antiguedad_apn', 'TIEMPO_SERVICIO_AP'),
('ficha:antiguedad_total', 'TIEMPO_SERVICIO_TOTAL,ANTIGUEDAD_TOTAL'),
('ficha:profesionalizacion_porcentaje', 'PORCENTAJE_PROFESIONALIZACION');