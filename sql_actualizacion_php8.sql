CREATE TABLE modulo_nomina.escala_salarial (
    id serial,
    escala character varying(50),
    sueldo_basico numeric(20,2),
    primary key(id)
);

CREATE TABLE modulo_nomina.escala_salarial_configuracion (
    campo character varying(20) NOT NULL,
    definicion character varying(250),
    primary key(campo)
);

insert into modulo_nomina.escala_salarial_configuracion(campo, definicion) VALUES
('sueldo_basico', 'SUELDO_MENSUAL');

alter table modulo_nomina.ficha add column cuenta_nomina varchar(20);
alter table modulo_nomina.ficha add column id_escala_salarial integer;    

ALTER TABLE modulo_nomina.ficha ADD FOREIGN KEY (id_escala_salarial) REFERENCES modulo_nomina.escala_salarial(id);