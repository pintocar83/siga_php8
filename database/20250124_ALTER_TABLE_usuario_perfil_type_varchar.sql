ALTER TABLE modulo_base.usuario_perfil ALTER COLUMN anio TYPE varchar(10);

#INSERT INTO modulo_base.usuario_perfil(anio,id_usuario,perfil,activo) 
#SELECT 'default',id_usuario,perfil,activo FROM modulo_base.usuario_perfil WHERE anio = '2024';