INSERT INTO modulo_base.usuario_perfil(anio,id_usuario,perfil,activo) 
SELECT '2024',id_usuario,perfil,activo FROM modulo_base.usuario_perfil WHERE anio = '2023';

INSERT INTO modulo_base.anio_detalle(anio) VALUES (2024);