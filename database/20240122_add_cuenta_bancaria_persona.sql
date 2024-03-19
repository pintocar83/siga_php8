SET search_path = modulo_base, pg_catalog, public;
ALTER TABLE modulo_base.persona ADD cuenta_bancaria VARCHAR(50)[];

UPDATE modulo_base.persona
SET cuenta_bancaria=persona_juridica.cuenta_bancaria
FROM persona_juridica
WHERE id=persona_juridica.id_persona;

INSERT INTO modulo_base.usuario_perfil_acceso(perfil, acceso) VALUES ('sigafs[banco_cuenta/167]','banco_movimiento=rw
banco_cuenta=rw|167
banco_movimiento_tipo=rw|BI,DP,TR,ND,NC');

ALTER TABLE modulo_base.persona_juridica DROP COLUMN cuenta_bancaria;

