INSERT INTO modulo_base.usuario_perfil(anio,id_usuario,perfil,activo) 
SELECT '2025',id_usuario,perfil,activo FROM modulo_base.usuario_perfil WHERE anio = '2024';

INSERT INTO modulo_base.anio_detalle(anio) VALUES (2025);


--Permisos de solo lectura del año anterior
UPDATE modulo_base.usuario_perfil
SET perfil='basico
sigafs[lectura]'
WHERE anio='2024' AND id_usuario IN (SELECT id FROM modulo_base.usuario U WHERE U.usuario NOT IN ('admin', 'dvargas', 'ejserra', 'wfajardo'))